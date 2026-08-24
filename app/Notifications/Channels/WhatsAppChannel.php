<?php

namespace App\Notifications\Channels;

use App\Models\Setting;
use App\Services\WhatsAppService;
use Illuminate\Notifications\Notification;

class WhatsAppChannel
{
    protected $whatsapp;

    public function __construct(WhatsAppService $whatsapp)
    {
        $this->whatsapp = $whatsapp;
    }

    /**
     * Send the given notification.
     */
    public function send($notifiable, Notification $notification)
    {
        if (! Setting::isWhatsAppEnabled()) {
            return;
        }

        if (!method_exists($notification, 'toWhatsApp')) {
            return;
        }

        $message = $notification->toWhatsApp($notifiable);
        if (empty($message)) {
            return;
        }

        $to = $notifiable->phone_number; // Assuming User has phone_number field

        if (empty($to)) {
            return;
        }

        $this->whatsapp->sendMessage($to, $message);
    }
}
