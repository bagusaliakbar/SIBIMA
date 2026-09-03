<?php

namespace App\Notifications;

use App\Channels\FonnteChannel;
use App\Models\Thesis;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AccNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $thesis;
    public $type; // 'up' or 'sidang'
    public $byUser;

    /**
     * Create a new notification instance.
     *
     * @param Thesis $thesis
     * @param string $type ('up' or 'sidang')
     * @param mixed $byUser
     */
    public function __construct(Thesis $thesis, string $type, $byUser = null)
    {
        $this->thesis = $thesis;
        $this->type = $type;
        $this->byUser = $byUser;
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
        $typeName = $this->type === 'up' ? 'Seminar UP' : 'Sidang Akhir';
        $byName = $this->byUser ? $this->byUser->name : 'Dosen Pembimbing';

        return \App\Models\WaTemplate::parse('acc_given', [
            'nama_mahasiswa' => $notifiable->name,
            'jenis_acc' => $typeName,
            'nama_pemberi_acc' => $byName,
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
        $typeName = $this->type === 'up' ? 'Seminar UP' : 'Sidang Akhir';
        $targetUrl = $this->type === 'up'
            ? route('seminar-applications.index')
            : route('thesis-defense-applications.index');

        return [
            'thesis_id' => $this->thesis->id,
            'title'     => 'Selamat! Rekomendasi ACC ' . $typeName,
            'message'   => "Anda telah mendapatkan persetujuan ACC {$typeName} dari pembimbing. Silakan lanjutkan pendaftaran.",
            'url'       => $targetUrl,
            'acc_type'  => $this->type,
            'type'      => 'success',
        ];
    }
}
