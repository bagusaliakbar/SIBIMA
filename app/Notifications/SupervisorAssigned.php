<?php

namespace App\Notifications;

use App\Channels\FonnteChannel;
use App\Models\Thesis;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SupervisorAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    public $thesis;
    public $role;

    /**
     * Create a new notification instance.
     *
     * @param Thesis $thesis
     * @param string $role (Pembimbing 1 / Pembimbing 2)
     */
    public function __construct(Thesis $thesis, string $role)
    {
        $this->thesis = $thesis;
        $this->role = $role;
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
        $studentName = $this->thesis->student->name ?? 'Mahasiswa';
        $title = $this->thesis->title ?? 'Tidak ada judul';

        return \App\Models\WaTemplate::parse('supervisor_assigned', [
            'nama_dosen' => $notifiable->name,
            'peran_pembimbing' => $this->role,
            'nama_mahasiswa' => $studentName,
            'judul_skripsi' => $title,
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
            'message' => "Anda ditugaskan sebagai {$this->role} untuk {$this->thesis->student->name}",
        ];
    }
}
