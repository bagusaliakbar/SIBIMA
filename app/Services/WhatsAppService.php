<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send a WhatsApp message using a gateway (e.g., Fonnte)
     * 
     * @param string $to Recipient phone number (e.g. 08123456789 or 628123456789)
     * @param string $message The message content
     * @param string|int|null $customDelay Optional custom delay in seconds for staggered sending
     * @return bool
     */
    public function sendMessage($to, $message, $customDelay = null)
    {
        // 1. Check if WhatsApp is enabled globally
        if (! Setting::isWhatsAppEnabled()) {
            Log::info('WhatsApp Service: Message skipped because WhatsApp is globally disabled by Admin.');
            return false;
        }

        // 2. Check Circuit Breaker
        if (Setting::isWhatsAppCircuitTripped()) {
            $reason = Setting::getWhatsAppCircuitReason() ?? 'Gateway WhatsApp sedang dijeda sementara karena kendala teknis.';
            Log::warning("WhatsApp Service: Message skipped because Circuit Breaker is active ({$reason}).");
            return false;
        }

        $token = config('services.whatsapp.token');
        $baseUrl = config('services.whatsapp.base_url', 'https://api.fonnte.com/send');

        if (empty($token)) {
            Log::warning('WhatsApp Service: Token is not configured.');
            return false;
        }

        // Apply Anti-Spam decorator: spintax parsing and unique fingerprint
        $decoratedMessage = $this->applyAntiSpamDecorators($message);
        
        // Use sequential staggered delay if customDelay not explicitly specified
        $delay = $customDelay !== null ? (string) $customDelay : (string) $this->calculateStaggeredDelay();
        $typing = (bool) config('services.whatsapp.typing', true);

        try {
            $response = Http::withHeaders([
                'Authorization' => $token
            ])->timeout(20)->post($baseUrl, [
                'target' => $this->formatPhoneNumber($to),
                'message' => $decoratedMessage,
                'delay' => (string) $delay,
                'typing' => $typing,
                'countryCode' => '62', // Default Indonesia
            ]);

            if ($response->successful()) {
                $this->recordSentMessage();
                return true;
            }

            $body = $response->body();
            Log::error('WhatsApp Service Error: ' . $body);
            $this->checkAndTripCircuitBreaker($response->status(), $body);
            return false;
        } catch (\Exception $e) {
            Log::error('WhatsApp Service Exception: ' . $e->getMessage());
            return false;
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
            
            return Cache::lock('wa_dispatch_queue_lock', 5)->block(3, function () use ($now) {
                $lastScheduled = (int) Cache::get('wa_next_dispatch_timestamp', $now);
                $interval = rand(12, 24);
                $targetTimestamp = max($now, $lastScheduled) + $interval;
                Cache::put('wa_next_dispatch_timestamp', $targetTimestamp, now()->addHours(2));
                
                return max(5, $targetTimestamp - $now);
            });
        } catch (\Throwable $e) {
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

    /**
     * Ensure phone number starts with 62 or similar if needed by the provider
     */
    protected function formatPhoneNumber($number)
    {
        // Simple formatting: remove non-numeric
        $number = preg_replace('/[^0-9]/', '', $number);
        
        // If starts with 0, replace with 62
        if (str_starts_with($number, '0')) {
            $number = '62' . substr($number, 1);
        }

        return $number;
    }
}
