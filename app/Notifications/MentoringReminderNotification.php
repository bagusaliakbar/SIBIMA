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

        if ($this->targetRole === 'dosen') {
            $studentName = $this->session->thesis->student->name ?? 'Mahasiswa';
            return "🔔 *REMINDER BIMBINGAN BESOK (H-1)*\n\n"
                 . "Halo Bpk/Ibu *{$notifiable->name}*,\n\n"
                 . "Anda memiliki jadwal bimbingan skripsi besok bersama mahasiswa *{$studentName}*.\n\n"
                 . "📅 Waktu: {$date} WIB\n"
                 . "📍 Tempat/Media: {$location}\n"
                 . "📝 Topik: {$topic}\n\n"
                 . "Cek detail bimbingan di dashboard SIBIMA:\n"
                 . url('/mentoring-sessions');
        } else {
            $dosenName = $this->session->dosen->name ?? 'Dosen Pembimbing';
            return "🔔 *REMINDER BIMBINGAN BESOK (H-1)*\n\n"
                 . "Halo *{$notifiable->name}*,\n\n"
                 . "Jangan lupa jadwal bimbingan skripsi Anda besok bersama *{$dosenName}*.\n\n"
                 . "📅 Waktu: {$date} WIB\n"
                 . "📍 Tempat/Media: {$location}\n"
                 . "📝 Topik: {$topic}\n\n"
                 . "Mohon persiapkan draf dan catatan bimbingan dengan baik. Cek detail di SIBIMA:\n"
                 . url('/mentoring-sessions');
        }
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
            'message' => "Pengingat H-1 bimbingan skripsi: {$this->session->topic}",
        ];
    }
}
