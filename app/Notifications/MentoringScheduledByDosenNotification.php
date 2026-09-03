<?php

namespace App\Notifications;

use App\Channels\FonnteChannel;
use App\Models\MentoringSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class MentoringScheduledByDosenNotification extends Notification implements ShouldQueue
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
    public function toFonnte($notifiable)
    {
        $dosenName = $this->session->dosen->name ?? 'Dosen Pembimbing';
        $topic = $this->session->topic ?? '-';
        $date = Carbon::parse($this->session->scheduled_at)->locale('id')->translatedFormat('l, d F Y H:i');
        $type = ucfirst($this->session->type ?? 'Offline');
        $location = $this->session->location ? " ({$this->session->location})" : '';
        $jenisLokasi = "{$type}{$location}";

        $fallback = "🔔 *JADWAL BIMBINGAN SKRIPSI BARU*\n\n"
            . "Halo *{nama_mahasiswa}*,\n\n"
            . "Dosen Pembimbing Anda, *{nama_dosen}*, telah membuat jadwal bimbingan skripsi baru:\n\n"
            . "📝 *Topik*: {topik_bimbingan}\n"
            . "📅 *Waktu*: {tanggal_bimbingan} WIB\n"
            . "📍 *Jenis/Lokasi*: {jenis_bimbingan}\n\n"
            . "⚠️ *Penting*: Silakan buka sistem SIBIMA untuk melakukan *Konfirmasi Kehadiran* (Akan Hadir / Izin):\n"
            . "{link_mentoring}\n\n"
            . "Terima kasih.\n_Sistem Informasi Bimbingan Skripsi (SIBIMA)_";

        return \App\Models\WaTemplate::parse('mentoring_scheduled_by_dosen', [
            'nama_mahasiswa'    => $notifiable->name,
            'nama_dosen'        => $dosenName,
            'tanggal_bimbingan' => $date,
            'topik_bimbingan'   => $topic,
            'jenis_bimbingan'   => $jenisLokasi,
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
        return [
            'mentoring_id' => $this->session->id,
            'title'        => 'Jadwal Bimbingan Baru',
            'message'      => "Dosen {$dosenName} menjadwalkan bimbingan: {$this->session->topic}",
            'url'          => route('mentoring-sessions.index', ['highlight' => $this->session->id]) . '#session-' . $this->session->id,
            'actionable'   => 'attendance',
            'type'         => 'attendance',
        ];
    }
}
