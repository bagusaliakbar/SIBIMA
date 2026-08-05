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

        $msg = "Halo *{$notifiable->name}*,\n\n"
             . "Status pengajuan bimbingan skripsi Anda (Topik: *{$topic}*) bersama *{$dosenName}* telah **{$statusText}**.\n\n";

        if (!empty($this->feedback)) {
            $msg .= "Catatan/Alasan Dosen:\n\"{$this->feedback}\"\n\n";
        }

        $msg .= "Silakan cek detail di dashboard SIBIMA:\n" . url('/mentoring-sessions');

        return $msg;
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
            'mentoring_id' => $this->session->id,
            'message' => "Status bimbingan ({$this->session->topic}) diperbarui menjadi: " . strtoupper($this->status),
        ];
    }
}
