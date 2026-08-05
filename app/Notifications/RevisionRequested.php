<?php

namespace App\Notifications;

use App\Channels\FonnteChannel;
use App\Models\Thesis;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class RevisionRequested extends Notification implements ShouldQueue
{
    use Queueable;

    public $thesis;
    public $type; // 'Seminar' or 'Sidang'

    /**
     * Create a new notification instance.
     *
     * @param Thesis $thesis
     * @param string $type
     */
    public function __construct(Thesis $thesis, string $type)
    {
        $this->thesis = $thesis;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [FonnteChannel::class, 'database'];
    }

    /**
     * Get the Fonnte / WhatsApp representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    public function toFonnte($notifiable)
    {
        return "Halo *{$notifiable->name}*,\n\n"
             . "Dosen penguji telah memberikan **Revisi {$this->type}** untuk skripsi Anda.\n\n"
             . "Silakan login ke dashboard SIBIMA untuk melihat detail revisi yang harus dikerjakan dan segera perbaiki sesuai tenggat waktu yang diberikan.\n\n"
             . url('/login');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'thesis_id' => $this->thesis->id,
            'message' => "Ada revisi {$this->type} baru untuk skripsi Anda.",
        ];
    }
}
