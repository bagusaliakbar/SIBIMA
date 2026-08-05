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

        return \App\Models\WaTemplate::parse('schedule_published', [
            'nama_penerima' => $notifiable->name,
            'jenis_ujian' => $this->type,
            'tanggal_ujian' => $date,
            'lokasi_ujian' => $location,
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
            'schedule_id' => $this->schedule->id,
            'message' => "Jadwal {$this->type} Anda telah dirilis.",
        ];
    }
}
