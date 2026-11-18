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
    public $isBypass;

    /**
     * Create a new notification instance.
     *
     * @param Thesis $thesis
     * @param string $type ('up' or 'sidang')
     * @param mixed $byUser
     * @param bool $isBypass
     */
    public function __construct(Thesis $thesis, string $type, $byUser = null, bool $isBypass = false)
    {
        $this->thesis = $thesis;
        $this->type = $type;
        $this->byUser = $byUser;
        $this->isBypass = $isBypass;
    }

    /**
     * Resolve display label for the user who granted ACC.
     */
    protected function resolveAccGiverName(): string
    {
        if (!$this->byUser) {
            return 'Dosen Pembimbing';
        }

        $role = $this->byUser->role ?? null;
        $userId = (int) ($this->byUser->id ?? 0);
        $isAdvisor = $userId > 0 && in_array($userId, [
            (int) ($this->thesis->pembimbing1_id ?? 0),
            (int) ($this->thesis->pembimbing2_id ?? 0),
        ], true);

        if ($this->isBypass || (in_array($role, ['admin', 'kaprodi'], true) && !$isAdvisor)) {
            return $role === 'admin' ? 'Admin (Bypass ACC)' : 'Kaprodi (Bypass ACC)';
        }

        return $this->byUser->name;
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
        $byName = $this->resolveAccGiverName();

        $fallback = "Halo *{nama_mahasiswa}*,\n\n"
            . "Selamat! Anda telah mendapatkan **ACC {jenis_acc}** dari *{nama_pemberi_acc}*.\n\n"
            . "Silakan cek status kelengkapan ACC dan segera lakukan pendaftaran gelombang melalui dashboard SIBIMA:\n"
            . "{link_login}";

        return \App\Models\WaTemplate::parse('acc_given', [
            'nama_mahasiswa' => $notifiable->name,
            'jenis_acc' => $typeName,
            'nama_pemberi_acc' => $byName,
            'link_login' => url('/login'),
        ], $fallback);
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
        $byName = $this->resolveAccGiverName();
        $targetUrl = $this->type === 'up'
            ? route('seminar-applications.index')
            : route('thesis-defense-applications.index');

        return [
            'thesis_id' => $this->thesis->id,
            'title'     => 'Selamat! Rekomendasi ACC ' . $typeName,
            'message'   => "Anda telah mendapatkan persetujuan ACC {$typeName} dari {$byName}. Silakan lanjutkan pendaftaran.",
            'url'       => $targetUrl,
            'acc_type'  => $this->type,
            'type'      => 'success',
        ];
    }
}
