<?php

namespace App\Notifications;

use App\Channels\FonnteChannel;
use App\Models\MentoringSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class MentoringStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $session;
    public $status;
    public $feedback;

    /**
     * Create a new notification instance.
     *
     * @param MentoringSession $session
     * @param string $status
     * @param string|null $feedback
     */
    public function __construct(MentoringSession $session, string $status, ?string $feedback = null)
    {
        $this->session = $session;
        $this->status = $status;
        $this->feedback = $feedback;
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
        $dosenName = $this->session->dosen->name ?? 'Dosen Pembimbing';
        $topic = $this->session->topic ?? '-';
        $statusText = match ($this->status) {
            'approved' => 'DISETUJUI',
            'rejected' => 'DITOLAK',
            'absent'   => 'DITANDAI TIDAK HADIR',
            default    => strtoupper($this->status),
        };

        return \App\Models\WaTemplate::parse('mentoring_status_updated', [
            'nama_mahasiswa' => $notifiable->name,
            'topik_bimbingan' => $topic,
            'nama_dosen' => $dosenName,
            'status_bimbingan' => $statusText,
            'catatan_dosen' => $this->feedback ?? 'Tidak ada catatan.',
            'link_mentoring' => url('/mentoring-sessions'),
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
        $statusText = match ($this->status) {
            'approved'  => 'Disetujui',
            'rejected'  => 'Ditolak',
            'absent'    => 'Tidak Hadir',
            'completed' => 'Selesai',
            default     => ucfirst($this->status),
        };

        $type = match ($this->status) {
            'approved', 'completed' => 'success',
            'rejected', 'absent'    => 'danger',
            default                 => 'info',
        };

        $dosenName = $this->session->dosen->name ?? 'Dosen Pembimbing';

        return [
            'mentoring_id' => $this->session->id,
            'title'        => 'Status Bimbingan: ' . $statusText,
            'message'      => "Bimbingan \"{$this->session->topic}\" bersama {$dosenName} telah {$statusText}.",
            'url'          => route('mentoring-sessions.index'),
            'type'         => $type,
        ];
    }
}
