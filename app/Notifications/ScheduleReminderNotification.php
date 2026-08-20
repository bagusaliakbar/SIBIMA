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
        return [FonnteChannel::class, 'database', 'mail'];
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

        return \App\Models\WaTemplate::parse('schedule_reminder', [
            'label_waktu' => $label,
            'nama_penerima' => $notifiable->name,
            'pesan_pengingat' => $this->message,
            'jenis_ujian' => $this->scheduleData['type'] ?? 'Ujian',
            'tanggal_ujian' => $this->scheduleData['date'] ?? '-',
            'jam_ujian' => $this->scheduleData['time'] ?? '-',
            'lokasi_ujian' => $location,
            'nama_mahasiswa' => $this->scheduleData['student'] ?? '-',
            'link_login' => url('/login'),
        ]);
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
