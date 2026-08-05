<?php

namespace App\Notifications;

use App\Channels\FonnteChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class KaprodiCriticalSummaryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $students;

    /**
     * Create a new notification instance.
     *
     * @param Collection $students
     */
    public function __construct(Collection $students)
    {
        $this->students = $students;
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
        $count = $this->students->count();
        
        $msg = "📊 *LAPORAN MAHASISWA SEMESTER KRITIS SIBIMA*\n\n"
             . "Halo Bpk/Ibu *{$notifiable->name}*,\n\n"
             . "Saat ini terdapat **{$count} mahasiswa** yang berada pada semester kritis (Semester 13-14+):\n\n";

        $limit = 5;
        foreach ($this->students->take($limit) as $student) {
            $msg .= "• {$student->name} ({$student->identifier}) - Sem {$student->current_semester}\n";
        }

        if ($count > $limit) {
            $remaining = $count - $limit;
            $msg .= "...dan {$remaining} mahasiswa lainnya.\n";
        }

        $msg .= "\nSilakan periksa daftar selengkapnya dan lakukan pemantauan pada menu Monitoring Kritis SIBIMA:\n"
             . url('/monitoring/critical');

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
            'message' => "Laporan Mahasiswa Semester Kritis: {$this->students->count()} Mahasiswa",
        ];
    }
}
