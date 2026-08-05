<?php

namespace App\Notifications;

use App\Channels\FonnteChannel;
use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

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
     * @param object $notifiable
     * @return array
     */
    public function via(object $notifiable): array
    {
        return [FonnteChannel::class, WhatsAppChannel::class, 'database', 'mail'];
    }

    /**
     * Get the Fonnte / WhatsApp representation of the notification.
     *
     * @param object $notifiable
     * @return string
     */
    public function toFonnte(object $notifiable): string
    {
        $label = $this->scheduleData['label'] ?? 'H-1';
        $location = $this->scheduleData['location'] ?? 'Belum ditentukan';

        return "🔔 *REMINDER JADWAL SIBIMA ({$label})*\n\n" .
               "Halo, *{$notifiable->name}*!\n\n" .
               "{$this->message}\n\n" .
               "📅 *Detail Jadwal:*\n" .
               "• Jenis: {$this->scheduleData['type']}\n" .
               "• Tanggal: {$this->scheduleData['date']}\n" .
               "• Waktu: {$this->scheduleData['time']}\n" .
               "• Ruangan: {$location}\n" .
               "• Mahasiswa: {$this->scheduleData['student']}\n\n" .
               "Harap hadir tepat waktu dan mempersiapkan dokumen yang diperlukan. Cek detail di dashboard SIBIMA:\n" .
               url('/login');
    }

    /**
     * Get the WhatsApp representation of the notification (legacy fallback).
     */
    public function toWhatsApp(object $notifiable): string
    {
        return $this->toFonnte($notifiable);
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
     * Get the array representation of the notification.
     *
     * @param object $notifiable
     * @return array
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
