<?php

namespace App\Notifications;

use App\Channels\FonnteChannel;
use App\Models\MentoringSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class MentoringReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $session;
    public $targetRole; // 'student' or 'dosen'

    /**
     * Create a new notification instance.
     *
     * @param MentoringSession $session
     * @param string $targetRole
     */
    public function __construct(MentoringSession $session, string $targetRole = 'student')
    {
        $this->session = $session;
        $this->targetRole = $targetRole;
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
        $date = Carbon::parse($this->session->scheduled_at)->translatedFormat('l, d F Y H:i');
        $topic = $this->session->topic ?? '-';
        $location = $this->session->location ?? 'Sesuai kesepakatan';
        $studentName = $this->session->thesis->student->name ?? 'Mahasiswa';
        $dosenName = $this->session->dosen->name ?? 'Dosen Pembimbing';

        return \App\Models\WaTemplate::parse('mentoring_reminder', [
            'nama_penerima' => $notifiable->name,
            'nama_mahasiswa' => $studentName,
            'nama_dosen' => $dosenName,
            'tanggal_bimbingan' => $date,
            'lokasi_bimbingan' => $location,
            'topik_bimbingan' => $topic,
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
        return [
            'mentoring_id' => $this->session->id,
            'title'        => 'Pengingat Jadwal Bimbingan (H-1)',
            'message'      => "Pengingat H-1 bimbingan skripsi: {$this->session->topic}",
            'url'          => route('mentoring-sessions.index'),
            'type'         => 'warning',
        ];
    }
}
