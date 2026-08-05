<?php

namespace App\Notifications;

use App\Channels\FonnteChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class RevisionSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $revision;
    public $studentName;
    public $notes;
    public $type; // 'Seminar' or 'Sidang Akhir'

    /**
     * Create a new notification instance.
     *
     * @param mixed $revision
     * @param string $studentName
     * @param string $notes
     * @param string $type
     */
    public function __construct($revision, string $studentName, string $notes, string $type)
    {
        $this->revision = $revision;
        $this->studentName = $studentName;
        $this->notes = $notes;
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
        return \App\Models\WaTemplate::parse('revision_submitted', [
            'nama_dosen' => $notifiable->name,
            'nama_mahasiswa' => $this->studentName,
            'jenis_ujian' => $this->type,
            'catatan_mahasiswa' => $this->notes,
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
            'revision_id' => $this->revision->id,
            'message' => "Mahasiswa {$this->studentName} telah mengunggah perbaikan revisi {$this->type}.",
        ];
    }
}
