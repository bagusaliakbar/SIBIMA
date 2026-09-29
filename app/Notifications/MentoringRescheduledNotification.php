<?php

namespace App\Notifications;

use App\Channels\FonnteChannel;
use App\Models\MentoringSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class MentoringRescheduledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $session;

    /**
     * Create a new notification instance.
     *
     * @param MentoringSession $session
     */
    public function __construct(MentoringSession $session)
    {
        $this->session = $session;
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
        $notes = $this->session->notes ?? '-';
        $date = Carbon::parse($this->session->scheduled_at)->locale('id')->translatedFormat('l, d F Y H:i');
        $type = ucfirst($this->session->type ?? 'Offline');
        $location = $this->session->location ? " ({$this->session->location})" : '';
        $jenisLokasi = "{$type}{$location}";

        $catatanBlock = !empty($this->session->notes) ? "\n💬 *Catatan*: {$this->session->notes}" : "";

        $fallback = "🔔 *PERUBAHAN JADWAL BIMBINGAN (RESCHEDULE)*\n\n"
            . "Halo *{nama_mahasiswa}*,\n\n"
            . "Jadwal bimbingan skripsi Anda telah diubah / dijadwalkan ulang oleh Dosen Pembimbing *{nama_dosen}*:\n\n"
            . "📝 *Topik*: {topik_bimbingan}\n"
            . "📅 *Waktu Baru*: {tanggal_bimbingan} WIB\n"
            . "📍 *Jenis/Lokasi*: {jenis_bimbingan}"
            . "{catatan_bimbingan}\n\n"
            . "⚠️ *Penting*: Silakan buka sistem SIBIMA untuk melakukan *Konfirmasi Ulang Kehadiran* Anda:\n"
            . "{link_mentoring}\n\n"
            . "Terima kasih.\n_Sistem Informasi Bimbingan Skripsi (SIBIMA)_";

        return \App\Models\WaTemplate::parse('mentoring_rescheduled', [
            'nama_mahasiswa'    => $notifiable->name,
            'nama_dosen'        => $dosenName,
            'tanggal_bimbingan' => $date,
            'topik_bimbingan'   => $topic,
            'jenis_bimbingan'   => $jenisLokasi,
            'catatan_bimbingan' => $catatanBlock,
            'catatan'           => $notes,
            'catatan_dosen'     => $notes,
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
        $dosenName = $this->session->dosen->name ?? 'Dosen Pembimbing';
        $topic = !empty($this->session->topic) ? $this->session->topic : '-';
        $notes = !empty($this->session->notes) ? $this->session->notes : null;
        $date = Carbon::parse($this->session->scheduled_at)->locale('id')->translatedFormat('l, d F Y H:i') . ' WIB';
        $type = ucfirst($this->session->type ?? 'Offline');
        $location = $this->session->location ? " ({$this->session->location})" : '';
        $jenisLokasi = "{$type}{$location}";

        $message = "Dosen {$dosenName} menjadwalkan ulang bimbingan ke {$date} ({$jenisLokasi}).";
        $message .= "\nTopik: {$topic}";
        if ($notes) {
            $message .= "\nCatatan: {$notes}";
        }

        return [
            'mentoring_id'  => $this->session->id,
            'title'         => 'Perubahan Jadwal Bimbingan (Reschedule)',
            'message'       => $message,
            'topic'         => $topic,
            'notes'         => $notes,
            'scheduled_at'  => $date,
            'type_location' => $jenisLokasi,
            'dosen_name'    => $dosenName,
            'url'           => route('mentoring-sessions.index', ['highlight' => $this->session->id]) . '#session-' . $this->session->id,
            'actionable'    => 'attendance',
            'type'          => 'warning',
        ];
    }
}
