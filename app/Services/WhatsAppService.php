<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send a WhatsApp message using a gateway (e.g., Fonnte)
     * 
     * @param string $to Recipient phone number (e.g. 08123456789 or 628123456789)
     * @param string $message The message content
     * @return bool
     */
    public function sendMessage($to, $message)
    {
        $token = config('services.whatsapp.token');
        $baseUrl = config('services.whatsapp.base_url', 'https://api.fonnte.com/send');

        if (empty($token)) {
            Log::warning('WhatsApp Service: Token is not configured.');
            return false;
        }

        // Apply Anti-Spam decorator: spintax parsing and unique fingerprint
        $decoratedMessage = $this->applyAntiSpamDecorators($message);
        $delay = config('services.whatsapp.delay', '5-12');
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
                return true;
            }

            Log::error('WhatsApp Service Error: ' . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error('WhatsApp Service Exception: ' . $e->getMessage());
            return false;
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
