<?php

namespace App\Notifications;

use App\Channels\FonnteChannel;
use App\Models\WaTemplate;
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
        $agendaTitle = $this->schedule->title ?? "Jadwal {$this->type}";

        // Ensure details and their relations are available
        if (method_exists($this->schedule, 'details')) {
            $this->schedule->loadMissing(['details.thesis.student', 'details.thesis.pembimbing1', 'details.thesis.pembimbing2', 'details.examiner1', 'details.examiner2']);
            $details = $this->schedule->details;
        } else {
            $details = collect();
        }

        // Branch by recipient role: Dosen vs Mahasiswa
        if (($notifiable->role ?? null) === 'dosen') {
            // Determine lecturer roles in this schedule session
            $isChairman = (int) ($this->schedule->chairman_id ?? 0) === (int) $notifiable->id;
            $isModerator = (int) ($this->schedule->moderator_id ?? 0) === (int) $notifiable->id;
            $isExaminer = false;
            $isSupervisor = false;

            $studentLines = [];
            foreach ($details as $detail) {
                if ((int) ($detail->examiner1_id ?? 0) === (int) $notifiable->id || (int) ($detail->examiner2_id ?? 0) === (int) $notifiable->id) {
                    $isExaminer = true;
                }
                if ($detail->thesis) {
                    if ((int) ($detail->thesis->pembimbing1_id ?? 0) === (int) $notifiable->id || (int) ($detail->thesis->pembimbing2_id ?? 0) === (int) $notifiable->id) {
                        $isSupervisor = true;
                    }

                    $student = $detail->thesis->student ?? null;
                    if ($student) {
                        $timeStr = '';
                        if ($detail->start_time && $detail->end_time) {
                            $timeStr = ' (' . Carbon::parse($detail->start_time)->format('H:i') . ' - ' . Carbon::parse($detail->end_time)->format('H:i') . ' WIB)';
                        } elseif ($detail->start_time) {
                            $timeStr = ' (' . Carbon::parse($detail->start_time)->format('H:i') . ' WIB)';
                        }
                        $studentLines[] = "• {$student->name}{$timeStr}";
                    }
                }
            }

            $roles = [];
            if ($isChairman) {
                $roles[] = str_contains($this->type, 'Sidang') ? 'Ketua Sidang' : 'Ketua Seminar';
            }
            if ($isModerator) {
                $roles[] = 'Moderator';
            }
            if ($isExaminer) {
                $roles[] = 'Dosen Penguji';
            }
            if ($isSupervisor) {
                $roles[] = 'Dosen Pembimbing';
            }

            $peranDosen = !empty($roles) ? implode(' & ', $roles) : 'Dosen Penguji';
            $daftarMahasiswa = !empty($studentLines) ? implode("\n", $studentLines) : '-';
            $jumlahMahasiswa = count($studentLines);

            return WaTemplate::parse('schedule_published_lecturer', [
                'nama_dosen' => $notifiable->name,
                'peran_dosen' => $peranDosen,
                'jenis_ujian' => $this->type,
                'nama_agenda' => $agendaTitle,
                'tanggal_ujian' => $date,
                'lokasi_ujian' => $location,
                'jumlah_mahasiswa' => $jumlahMahasiswa,
                'daftar_mahasiswa' => $daftarMahasiswa,
                'link_login' => url('/login'),
            ]);
        }

        // Student / General recipient
        // Find specific time slot for this student if available
        $jamUjian = 'Sesuai Jadwal';
        foreach ($details as $detail) {
            if ($detail->thesis && (int) $detail->thesis->student_id === (int) $notifiable->id) {
                if ($detail->start_time && $detail->end_time) {
                    $jamUjian = Carbon::parse($detail->start_time)->format('H:i') . ' - ' . Carbon::parse($detail->end_time)->format('H:i') . ' WIB';
                } elseif ($detail->start_time) {
                    $jamUjian = Carbon::parse($detail->start_time)->format('H:i') . ' WIB';
                }
                break;
            }
        }

        return WaTemplate::parse('schedule_published', [
            'nama_mahasiswa' => $notifiable->name,
            'nama_penerima' => $notifiable->name,
            'jenis_ujian' => $this->type,
            'tanggal_ujian' => $date,
            'jam_ujian' => $jamUjian,
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
        $isLecturer = ($notifiable->role ?? null) === 'dosen';
        $message = $isLecturer
            ? "Penugasan jadwal {$this->type} ({$this->schedule->title}) telah dirilis."
            : "Jadwal {$this->type} Anda telah dirilis.";

        return [
            'schedule_id' => $this->schedule->id,
            'schedule_type' => $this->type,
            'message' => $message,
        ];
    }
}
