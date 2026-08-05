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

        return "Halo *{$notifiable->name}*,\n\n"
             . "Pengajuan judul skripsi Anda **BISA DILANJUTKAN**\n\n"
             . "Berikut adalah dosen pembimbing yang ditugaskan untuk Anda:\n"
             . "Pembimbing 1: {$p1}\n"
             . "Pembimbing 2: {$p2}\n\n"
             . "Silakan segera menghubungi dosen pembimbing Anda, diskusikan konsep/gambaran rencana penelitiannya dan memulai proses bimbingan melalui dashboard SIBIMA:\n"
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
            'message' => "Pengajuan judul diterima. Pembimbing Anda: {$this->thesis->pembimbing1->name} & {$this->thesis->pembimbing2->name}",
        ];
    }
}
