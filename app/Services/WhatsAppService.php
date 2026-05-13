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

        try {
            $response = Http::withHeaders([
                'Authorization' => $token
            ])->post($baseUrl, [
                'target' => $this->formatPhoneNumber($to),
                'message' => $message,
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
