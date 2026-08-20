<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
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
        // Get the phone number from the notifiable model
        if (! method_exists($notifiable, 'routeNotificationForFonnte')) {
            return;
        }

        $target = $notifiable->routeNotificationForFonnte($notification);

        if (! $target) {
            return;
        }

        // Get the message content from the notification
        if (! method_exists($notification, 'toFonnte')) {
            return;
        }

        $message = $notification->toFonnte($notifiable);

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
        $delay = config('services.whatsapp.delay', '5-12');
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
                Log::error('Fonnte API Error: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Fonnte Channel Exception: ' . $e->getMessage());
        }
    }

    /**
     * Apply Anti-Spam fingerprint and Spintax processing to prevent WhatsApp duplicate message bans.
     */
    protected function applyAntiSpamDecorators(string $message): string
    {
        // 1. Process Spintax if present: e.g. {Halo|Hai|Yth.|Salam}
        $message = preg_replace_callback('/\{([^{}]+)\}/', function ($matches) {
            $options = explode('|', $matches[1]);
            return $options[array_rand($options)];
        }, $message);

        // 2. Append a subtle unique transaction code & timestamp to ensure 100% unique hash per message
        $uniqueRef = strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
        $timestamp = now()->locale('id')->isoFormat('D MMM Y, HH:mm');

        $footer = "\n\n_Ref: #SB-{$uniqueRef} • {$timestamp} WIB_\n_Notifikasi Otomatis Sistem SIBIMA_";

        return trim($message) . $footer;
    }
}
