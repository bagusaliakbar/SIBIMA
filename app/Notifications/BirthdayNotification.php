<?php

namespace App\Notifications;

use App\Channels\FonnteChannel;
use App\Models\WaTemplate;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class BirthdayNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
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
        $todayFormatted = Carbon::now()->locale('id')->translatedFormat('l, d F Y');
        $age = $notifiable->age ? "{$notifiable->age} tahun" : '';

        if ($notifiable->role === 'mahasiswa') {
            $thesis = $notifiable->thesis;
            $thesisTitle = $thesis ? ($thesis->final_title ?: $thesis->title) : 'Sedang Berjalan';
            $p1Name = $thesis?->pembimbing1?->name ?: 'Dosen Pembimbing';

            return WaTemplate::parse('birthday_student', [
                'nama_mahasiswa'   => $notifiable->name,
                'umur'             => $age,
                'judul_skripsi'    => $thesisTitle,
                'nama_pembimbing1' => $p1Name,
                'hari_ini'         => $todayFormatted,
            ]);
        }

        // Dosen / Kaprodi
        return WaTemplate::parse('birthday_lecturer', [
            'nama_dosen' => $notifiable->name,
            'umur'       => $age,
            'hari_ini'   => $todayFormatted,
        ]);
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $isStudent = $notifiable->role === 'mahasiswa';

        return [
            'title' => 'Selamat Ulang Tahun! 🎂🎉',
            'message' => $isStudent
                ? "Selamat ulang tahun, {$notifiable->name}! Semoga selalu sehat, dimudahkan dalam bimbingan skripsi, dan segera wisuda!"
                : "Selamat bertambah usia, {$notifiable->name}! Terima kasih atas dedikasi dan bimbingan yang tulus bagi kemajuan mahasiswa.",
            'icon' => 'cake',
            'type' => 'birthday',
            'url' => route('dashboard'),
        ];
    }
}
