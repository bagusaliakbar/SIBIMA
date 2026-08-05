<?php

namespace App\Notifications;

use App\Channels\FonnteChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class CriticalStudentReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

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
        $sem = $notifiable->current_semester ?? 'Akhir';

        return "⚠️ *PERINGATAN MASA STUDI SIBIMA*\n\n"
             . "Halo *{$notifiable->name}*,\n\n"
             . "Saat ini Anda berada di **Semester {$sem}** (Semester Kritis). Mari manfaatkan waktu yang ada untuk segera menyelesaikan proses penyusunan skripsi Anda.\n\n"
             . "💡 *Langkah yang disarankan:*\n"
             . "1. Segera jadwalkan bimbingan rutin dengan Dosen Pembimbing.\n"
             . "2. Konsultasikan kendala atau hambatan penelitian Anda ke Prodi.\n\n"
             . "Mari selesaikan studi Anda tepat waktu! Cek progres Anda di dashboard SIBIMA:\n"
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
            'message' => "Peringatan Masa Studi Semester Kritis (Semester {$notifiable->current_semester})",
        ];
    }
}
