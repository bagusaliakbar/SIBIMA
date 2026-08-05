<?php

namespace App\Notifications;

use App\Channels\FonnteChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class SchedulePublished extends Notification implements ShouldQueue
{
    use Queueable;

    public $schedule; // SeminarSchedule or ThesisDefenseSchedule
    public $type; // 'Seminar' or 'Sidang'

    /**
     * Create a new notification instance.
     *
     * @param mixed $schedule
     * @param string $type
     */
    public function __construct($schedule, string $type)
    {
        $this->schedule = $schedule;
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
        $date = Carbon::parse($this->schedule->date)->translatedFormat('l, d F Y');
        $location = $this->schedule->location ?? 'Belum ditentukan';

        return "Halo *{$notifiable->name}*,\n\n"
             . "Jadwal *{$this->type}* Anda telah dirilis!\n\n"
             . "Tanggal: {$date}\n"
             . "Ruangan: {$location}\n\n"
             . "Mohon hadir tepat waktu dan persiapkan segala dokumen yang diperlukan. Cek detail selengkapnya di dashboard SIBIMA:\n"
             . url('/login');
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
            'schedule_id' => $this->schedule->id,
            'message' => "Jadwal {$this->type} Anda telah dirilis.",
        ];
    }
}
