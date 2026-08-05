<?php

namespace App\Notifications;

use App\Channels\FonnteChannel;
use App\Models\Thesis;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ThesisAccepted extends Notification implements ShouldQueue
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
        $p1 = $this->thesis->pembimbing1->name ?? 'Belum ditentukan';
        $p2 = $this->thesis->pembimbing2->name ?? 'Belum ditentukan';

        return \App\Models\WaTemplate::parse('thesis_accepted', [
            'nama_mahasiswa' => $notifiable->name,
            'pembimbing_1' => $p1,
            'pembimbing_2' => $p2,
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
            'message' => "Pengajuan judul diterima. Pembimbing Anda: {$this->thesis->pembimbing1->name} & {$this->thesis->pembimbing2->name}",
        ];
    }
}
