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
        
        if (empty($token)) {
            Log::warning('Fonnte API token is not configured. WhatsApp message not sent.');
            return;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62', // Optional but good practice
            ]);

            if (! $response->successful()) {
                Log::error('Fonnte API Error: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Fonnte Channel Exception: ' . $e->getMessage());
        }
    }
}
