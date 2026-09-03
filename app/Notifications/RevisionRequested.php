<?php

namespace App\Notifications;

use App\Channels\FonnteChannel;
use App\Models\Thesis;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class RevisionRequested extends Notification implements ShouldQueue
{
    use Queueable;

    public $thesis;
    public $type; // 'Seminar' or 'Sidang'
    public $revisionId;

    /**
     * Create a new notification instance.
     *
     * @param Thesis $thesis
     * @param string $type
     * @param int|null $revisionId
     */
    public function __construct(Thesis $thesis, string $type, $revisionId = null)
    {
        $this->thesis = $thesis;
        $this->type = $type;
        $this->revisionId = $revisionId;
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
        return \App\Models\WaTemplate::parse('revision_requested', [
            'nama_mahasiswa' => $notifiable->name,
            'jenis_ujian' => $this->type,
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
        $isDefense = in_array(strtolower($this->type), ['sidang', 'sidang akhir']);
        
        if ($isDefense) {
            $url = $this->revisionId 
                ? route('student-defense-revisions.show', $this->revisionId)
                : route('student-defense-revisions.index');
        } else {
            $url = $this->revisionId 
                ? route('student-seminar-revisions.show', $this->revisionId)
                : route('student-seminar-revisions.index');
        }

        return [
            'thesis_id'   => $this->thesis->id,
            'revision_id' => $this->revisionId,
            'title'       => 'Catatan Revisi Baru: ' . $this->type,
            'message'     => "Ada catatan revisi {$this->type} baru untuk skripsi Anda. Segera cek dan kirim balasan revisi.",
            'url'         => $url,
            'type'        => 'revision',
        ];
    }
}
