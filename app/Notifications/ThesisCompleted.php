<?php

namespace App\Notifications;

use App\Channels\FonnteChannel;
use App\Models\Thesis;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ThesisCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    public $thesis;

    /**
     * Create a new notification instance.
     *
     * @param Thesis $thesis
     */
    public function __construct(Thesis $thesis)
    {
        $this->thesis = $thesis;
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
        return \App\Models\WaTemplate::parse('thesis_completed', [
            'nama_mahasiswa' => $notifiable->name,
            'link_login' => url('/login'),
        ]);
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
            'message' => "Selamat! Skripsi Anda telah selesai dan dinyatakan Lulus.",
        ];
    }
}
