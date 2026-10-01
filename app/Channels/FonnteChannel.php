<?php

namespace App\Channels;

use App\Models\Setting;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        // 1. Check if WhatsApp notifications are globally enabled by Admin
        if (! Setting::isWhatsAppEnabled()) {
            Log::info('FonnteChannel: WhatsApp notifications are globally disabled by Admin. Skipped.');
            return;
        }

        // 2. Check Circuit Breaker (if tripped due to recent bans/gateway disconnections)
        if (Setting::isWhatsAppCircuitTripped()) {
            $reason = Setting::getWhatsAppCircuitReason() ?? 'Gateway WhatsApp sedang dijeda sementara karena kendala teknis.';
            Log::warning("FonnteChannel: Message skipped because Circuit Breaker is active ({$reason}).");
            return;
        }

        // 3. Get the phone number from the notifiable model
        if (! method_exists($notifiable, 'routeNotificationForFonnte')) {
            return;
        }

        $target = $notifiable->routeNotificationForFonnte($notification);

        if (! $target) {
            return;
        }

        // 4. Get the message content from the notification
        if (! method_exists($notification, 'toFonnte')) {
            return;
        }

        $message = $notification->toFonnte($notifiable);

        // If message is empty (e.g. specific template is disabled via WaTemplate::parse), skip gracefully
        if (empty($message)) {
            return;
        }

        $token = config('services.whatsapp.token') ?? env('WHATSAPP_TOKEN');
        $baseUrl = config('services.whatsapp.base_url', 'https://api.fonnte.com/send');
        
        if (empty($token)) {
            Log::warning('Fonnte API token is not configured. WhatsApp message not sent.');
            return;
        }

        // Apply Anti-Spam decorator: spintax parsing and unique fingerprint
        $decoratedMessage = $this->applyAntiSpamDecorators($message);

        // Calculate intelligent sequential staggered delay to prevent burst bans
        $delay = $this->calculateStaggeredDelay();
        $typing = (bool) config('services.whatsapp.typing', true);

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->timeout(20)->post($baseUrl, [
                'target' => $target,
                'message' => $decoratedMessage,
                'delay' => (string) $delay,
                'typing' => $typing,
                'countryCode' => '62',
            ]);

            if (! $response->successful()) {
                $body = $response->body();
                Log::error('Fonnte API Error: ' . $body);
                $this->checkAndTripCircuitBreaker($response->status(), $body);
            } else {
                $this->recordSentMessage();
            }
        } catch (\Exception $e) {
            Log::error('Fonnte Channel Exception: ' . $e->getMessage());
        }
    }

    /**
     * Calculate an intelligent staggered delay between consecutive messages
     * to eliminate burst traffic and comply with WhatsApp anti-spam policies.
     */
    protected function calculateStaggeredDelay(): int
    {
        try {
            $now = now()->timestamp;
            
            // Atomic lock to calculate sequential slot
            return Cache::lock('wa_dispatch_queue_lock', 5)->block(3, function () use ($now) {
                $lastScheduled = (int) Cache::get('wa_next_dispatch_timestamp', $now);
                
                // Random human-like interval between messages: 12 to 24 seconds
                $interval = rand(12, 24);
                
                // Target dispatch timestamp
                $targetTimestamp = max($now, $lastScheduled) + $interval;
                
                // Save new scheduled timestamp (valid for 2 hours)
                Cache::put('wa_next_dispatch_timestamp', $targetTimestamp, now()->addHours(2));
                
                // Return delay in seconds relative to right now (minimum 5s)
                return max(5, $targetTimestamp - $now);
            });
        } catch (\Throwable $e) {
            // Fallback to random delay if lock fails
            return rand(8, 20);
        }
    }

    /**
     * Inspect error response and trip the circuit breaker if account/device issues are detected.
     */
    protected function checkAndTripCircuitBreaker(int $statusCode, string $responseBody): void
    {
        $lowerBody = strtolower($responseBody);
        
        $criticalKeywords = [
            'device disconnected',
            'not connected',
            'invalid token',
            'banned',
            'blocked',
            'suspended',
            'rate limit',
            'too many requests',
            'limit exceeded',
        ];

        foreach ($criticalKeywords as $keyword) {
            if (str_contains($lowerBody, $keyword)) {
                // Trip circuit breaker for 30 minutes to protect gateway & avoid cascading errors
                Cache::put('wa_circuit_breaker_tripped', true, now()->addMinutes(30));
                Cache::put('wa_circuit_breaker_reason', "Gateway error: {$keyword} (Status {$statusCode})", now()->addMinutes(30));
                Log::critical("WhatsApp Circuit Breaker tripped: detected '{$keyword}'. Pausing all outgoing WhatsApp notifications for 30 minutes.");
                break;
            }
        }
    }

    /**
     * Track sent count for hourly quota monitoring.
     */
    protected function recordSentMessage(): void
    {
        $key = 'wa_hourly_sent_count_' . now()->format('YmdH');
        $count = Cache::increment($key);
        if ($count === 1) {
            Cache::put($key, 1, now()->addHours(2));
        }

        if ($count > 80) {
            Log::warning("WhatsApp safety limit: {$count} messages sent in current hour.");
        }
    }

    /**
     * Apply Anti-Spam fingerprint and Spintax processing to prevent WhatsApp duplicate message bans.
     */
    protected function applyAntiSpamDecorators(string $message): string
    {
        // 1. Process Spintax if present: e.g. {Halo|Hai|Yth.|Salam}
        while (preg_match('/\{([^{}]+)\}/', $message)) {
            $message = preg_replace_callback('/\{([^{}]+)\}/', function ($matches) {
                // Only treat as spintax if it contains pipe separator '|'
                if (!str_contains($matches[1], '|')) {
                    return $matches[0];
                }
                $options = explode('|', $matches[1]);
                return $options[array_rand($options)];
            }, $message);
        }

        // 2. Append a subtle unique transaction code & timestamp to ensure 100% unique hash per message
        $uniqueRef = strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
        $timestamp = now()->locale('id')->isoFormat('D MMM Y, HH:mm');

        $footer = "\n\n_Ref: #SB-{$uniqueRef} • {$timestamp} WIB_\n_Notifikasi Otomatis Sistem SIBIMA_";

        return trim($message) . $footer;
    }
}
