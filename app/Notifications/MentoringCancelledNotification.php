<?php

namespace App\Notifications;

use App\Channels\FonnteChannel;
use App\Models\MentoringSession;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class MentoringCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $sessionData;
    public $cancelledBy;
    public $reason;

    /**
     * Create a new notification instance.
     *
     * @param MentoringSession $session
     * @param User $cancelledBy
     * @param string $reason
     */
    public function __construct(MentoringSession $session, User $cancelledBy, string $reason = '')
    {
        $this->sessionData = [
            'id' => $session->id,
            'topic' => $session->topic ?? '-',
            'scheduled_at' => $session->scheduled_at ? $session->scheduled_at->toIso8601String() : null,
            'type' => $session->type,
            'location' => $session->location,
            'dosen_name' => $session->dosen->name ?? $session->thesis?->pembimbing1?->name ?? 'Dosen Pembimbing',
            'student_name' => $session->thesis?->student?->name ?? 'Mahasiswa',
        ];
        $this->cancelledBy = $cancelledBy;
        $this->reason = $reason ?: 'Tidak ada alasan khusus yang dicantumkan.';
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
        $scheduledAt = $this->sessionData['scheduled_at'] ? Carbon::parse($this->sessionData['scheduled_at'])->locale('id')->translatedFormat('l, d F Y H:i') : '-';
        $pembatal = $this->cancelledBy->name;
        $roleLabel = in_array($this->cancelledBy->role, ['dosen', 'admin', 'kaprodi']) ? 'Dosen Pembimbing' : 'Mahasiswa';

        $fallback = "🚫 *PEMBATALAN JADWAL BIMBINGAN SKRIPSI*\n\n"
            . "Halo *{nama_penerima}*,\n\n"
            . "Sesi bimbingan skripsi berikut telah *DIBATALKAN* oleh {$roleLabel} *{$pembatal}*:\n\n"
            . "📝 *Topik*: {topik_bimbingan}\n"
            . "📅 *Jadwal Awal*: {tanggal_bimbingan} WIB\n"
            . "💬 *Alasan Pembatalan*: {alasan_pembatalan}\n\n"
            . "Silakan ajukan atau jadwalkan kembali sesi bimbingan berikutnya melalui sistem SIBIMA:\n"
            . "{link_mentoring}\n\n"
            . "Terima kasih.\n_Sistem Informasi Bimbingan Skripsi (SIBIMA)_";

        return \App\Models\WaTemplate::parse('mentoring_cancelled', [
            'nama_penerima'     => $notifiable->name,
            'nama_pembatal'     => $pembatal,
            'role_pembatal'     => $roleLabel,
            'tanggal_bimbingan' => $scheduledAt,
            'topik_bimbingan'   => $this->sessionData['topic'],
            'alasan_pembatalan' => $this->reason,
            'link_mentoring'    => route('mentoring-sessions.index'),
        ], $fallback);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $scheduledAt = $this->sessionData['scheduled_at'] ? Carbon::parse($this->sessionData['scheduled_at'])->locale('id')->translatedFormat('d M Y H:i') : '-';
        return [
            'mentoring_id' => $this->sessionData['id'],
            'title'        => 'Pembatalan Jadwal Bimbingan',
            'message'      => "{$this->cancelledBy->name} membatalkan jadwal bimbingan pada {$scheduledAt} WIB ({$this->sessionData['topic']}). Alasan: {$this->reason}",
            'url'          => route('mentoring-sessions.index'),
            'type'         => 'danger',
        ];
    }
}
