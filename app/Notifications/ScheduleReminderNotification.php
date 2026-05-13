<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Channels\WhatsAppChannel;

class ScheduleReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $title;
    protected $message;
    protected $scheduleData;

    /**
     * Create a new notification instance.
     */
    public function __construct($title, $message, $scheduleData)
    {
        $this->title = $title;
        $this->message = $message;
        $this->scheduleData = $scheduleData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database', 'mail'];
        
        if (!empty($notifiable->phone_number)) {
            $channels[] = WhatsAppChannel::class;
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject($this->title)
                    ->greeting('Halo, ' . $notifiable->name . '!')
                    ->line($this->message)
                    ->line('Detail Jadwal:')
                    ->line('Tanggal: ' . $this->scheduleData['date'])
                    ->line('Waktu: ' . $this->scheduleData['time'])
                    ->line('Mahasiswa: ' . $this->scheduleData['student'])
                    ->action('Lihat Dashboard', url('/dashboard'))
                    ->line('Terima kasih telah menggunakan sistem SIBIMA.');
    }

    /**
     * Get the WhatsApp representation of the notification.
     */
    public function toWhatsApp(object $notifiable): string
    {
        return "🔔 *REMINDER SIBIMA*\n\n" .
               "Halo, *{$notifiable->name}*!\n\n" .
               "{$this->message}\n\n" .
               "📅 *Detail Jadwal:*\n" .
               "• Tanggal: {$this->scheduleData['date']}\n" .
               "• Waktu: {$this->scheduleData['time']}\n" .
               "• Mahasiswa: {$this->scheduleData['student']}\n\n" .
               "Harap hadir tepat waktu. Terima kasih.";
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'schedule_data' => $this->scheduleData,
            'type' => 'reminder'
        ];
    }
}
