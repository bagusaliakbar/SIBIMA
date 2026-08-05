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
        $limit = 5;
        $studentList = '';

        foreach ($this->students->take($limit) as $student) {
            $studentList .= "• {$student->name} ({$student->identifier}) - Sem {$student->current_semester}\n";
        }

        if ($count > $limit) {
            $remaining = $count - $limit;
            $studentList .= "...dan {$remaining} mahasiswa lainnya.\n";
        }

        return \App\Models\WaTemplate::parse('kaprodi_critical_summary', [
            'nama_kaprodi' => $notifiable->name,
            'jumlah_mahasiswa' => $count,
            'daftar_mahasiswa' => $studentList,
            'link_monitoring' => url('/monitoring/critical'),
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
            'message' => "Laporan Mahasiswa Semester Kritis: {$this->students->count()} Mahasiswa",
        ];
    }
}
