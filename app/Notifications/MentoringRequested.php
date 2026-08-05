<?php

namespace App\Notifications;

use App\Channels\FonnteChannel;
use App\Models\MentoringSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class MentoringRequested extends Notification implements ShouldQueue
{
    use Queueable;

    public $mentoringSession;

    /**
     * Create a new notification instance.
     *
     * @param MentoringSession $session
     */
    public function __construct(MentoringSession $session)
    {
        $this->mentoringSession = $session;
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
        $studentName = $this->mentoringSession->thesis->student->name ?? 'Mahasiswa';
        $date = Carbon::parse($this->mentoringSession->scheduled_at)->translatedFormat('l, d F Y H:i');
        $topic = $this->mentoringSession->topic ?? '-';

        return "Halo Bpk/Ibu *{$notifiable->name}*,\n\n"
             . "Mahasiswa bimbingan Anda, *{$studentName}*, telah mengajukan jadwal bimbingan skripsi.\n\n"
             . "Waktu: {$date} WIB\n"
             . "Topik: {$topic}\n\n"
             . "Silakan cek dan konfirmasi jadwal tersebut di dashboard SIBIMA:\n"
             . url('/mentoring-sessions');
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
            'mentoring_id' => $this->mentoringSession->id,
            'message' => "Pengajuan bimbingan dari {$this->mentoringSession->student->name}",
        ];
    }
}
