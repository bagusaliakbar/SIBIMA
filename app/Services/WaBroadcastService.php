<?php

namespace App\Services;

use App\Models\User;
use App\Models\Thesis;
use App\Models\MentoringSession;
use App\Models\SeminarApplication;
use App\Models\SeminarScheduleDetail;
use App\Models\ThesisDefenseApplication;
use App\Models\ThesisDefenseScheduleDetail;
use App\Models\Wave;
use App\Models\WaBroadcast;
use App\Models\WaBroadcastLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class WaBroadcastService
{
    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Get target recipients according to filter criteria.
     *
     * @param string $targetType
     * @param array $filters
     * @return Collection
     */
    public function getTargetRecipients(string $targetType, array $filters = []): Collection
    {
        return match ($targetType) {
            'mahasiswa_belum_seminar' => $this->getMahasiswaBelumSeminar($filters),
            'mahasiswa_bimbingan_pasif' => $this->getMahasiswaBimbinganPasif($filters),
            'mahasiswa_kritis' => $this->getMahasiswaKritis($filters),
            'mahasiswa_belum_sidang' => $this->getMahasiswaBelumSidang($filters),
            'mahasiswa_belum_skripsi' => $this->getMahasiswaBelumSkripsi($filters),
            'dosen_pembimbing_aktif' => $this->getDosenPembimbingAktif($filters),
            'dosen_penguji_gelombang' => $this->getDosenPengujiGelombang($filters),
            'all_mahasiswa_aktif' => $this->getAllMahasiswaAktif($filters),
            'custom' => $this->getCustomRecipients($filters),
            default => collect(),
        };
    }

    /**
     * Mahasiswa bimbingan skripsi aktif yang belum mendaftar atau belum lulus seminar proposal.
     */
    protected function getMahasiswaBelumSeminar(array $filters): Collection
    {
        $query = Thesis::with(['student', 'pembimbing1', 'pembimbing2'])
            ->where('status', 'active');

        if (!empty($filters['cohort']) && $filters['cohort'] !== 'all') {
            $query->whereHas('student', function ($q) use ($filters) {
                $q->where('entry_year', $filters['cohort']);
            });
        }

        // Belum memiliki pengajuan seminar yang approved
        $theses = $query->whereDoesntHave('seminarApplications', function ($q) {
            $q->where('status', 'approved');
        })->get();

        return $theses->map(function ($thesis) {
            $student = $thesis->student;
            if (!$student) return null;

            return [
                'user_id' => $student->id,
                'name' => $student->name,
                'identifier' => $student->identifier ?? '-',
                'phone' => $student->phone_number,
                'role' => 'mahasiswa',
                'cohort' => $student->entry_year ?? '-',
                'status_info' => 'Belum Seminar Proposal',
                'context' => [
                    'nama' => $student->name,
                    'npm' => $student->identifier ?? '-',
                    'angkatan' => $student->entry_year ?? '-',
                    'judul' => $thesis->final_title ?: $thesis->title,
                    'pembimbing_1' => $thesis->pembimbing1->name ?? '-',
                    'pembimbing_2' => $thesis->pembimbing2->name ?? '-',
                    'link_seminar' => route('seminar-applications.create'),
                    'link_login' => url('/login'),
                ],
            ];
        })->filter()->values();
    }

    /**
     * Mahasiswa bimbingan skripsi aktif tapi belum pernah bimbingan > X hari.
     */
    protected function getMahasiswaBimbinganPasif(array $filters): Collection
    {
        $days = (int) ($filters['days'] ?? 30);
        $thresholdDate = now()->subDays($days);

        $query = Thesis::with(['student', 'pembimbing1', 'pembimbing2', 'mentoringSessions'])
            ->where('status', 'active');

        if (!empty($filters['cohort']) && $filters['cohort'] !== 'all') {
            $query->whereHas('student', function ($q) use ($filters) {
                $q->where('entry_year', $filters['cohort']);
            });
        }

        $theses = $query->get()->filter(function ($thesis) use ($thresholdDate) {
            $lastSession = $thesis->mentoringSessions
                ->whereIn('status', ['approved', 'completed'])
                ->sortByDesc('date')
                ->first();

            if (!$lastSession) {
                return true; // Belum pernah bimbingan sama sekali
            }

            return Carbon::parse($lastSession->date)->lt($thresholdDate);
        });

        return $theses->map(function ($thesis) {
            $student = $thesis->student;
            if (!$student) return null;

            $lastSession = $thesis->mentoringSessions
                ->whereIn('status', ['approved', 'completed'])
                ->sortByDesc('date')
                ->first();

            $lastDate = $lastSession ? Carbon::parse($lastSession->date) : null;
            $inactiveDays = $lastDate ? (int) $lastDate->diffInDays(now()) : 999;
            $lastDateFormatted = $lastDate ? $lastDate->locale('id')->translatedFormat('d F Y') : 'Belum pernah bimbingan';

            return [
                'user_id' => $student->id,
                'name' => $student->name,
                'identifier' => $student->identifier ?? '-',
                'phone' => $student->phone_number,
                'role' => 'mahasiswa',
                'cohort' => $student->entry_year ?? '-',
                'status_info' => $lastDate ? "Mangkir {$inactiveDays} hari (terakhir: {$lastDateFormatted})" : "Belum pernah bimbingan",
                'context' => [
                    'nama' => $student->name,
                    'npm' => $student->identifier ?? '-',
                    'angkatan' => $student->entry_year ?? '-',
                    'judul' => $thesis->final_title ?: $thesis->title,
                    'pembimbing_1' => $thesis->pembimbing1->name ?? '-',
                    'pembimbing_2' => $thesis->pembimbing2->name ?? '-',
                    'hari_tanpa_bimbingan' => $inactiveDays >= 999 ? '> 90 hari' : "{$inactiveDays} hari",
                    'terakhir_bimbingan' => $lastDateFormatted,
                    'link_bimbingan' => route('mentoring-sessions.index'),
                    'link_login' => url('/login'),
                ],
            ];
        })->filter()->values();
    }

    /**
     * Mahasiswa semester kritis (semester 13-14+).
     */
    protected function getMahasiswaKritis(array $filters): Collection
    {
        $students = User::where('role', 'mahasiswa')
            ->whereNotNull('entry_year')
            ->get()
            ->filter(function ($user) {
                return $user->is_critical_semester;
            });

        return $students->map(function ($student) {
            $activeThesis = Thesis::with(['pembimbing1', 'pembimbing2'])
                ->where('student_id', $student->id)
                ->whereIn('status', ['active', 'pending'])
                ->latest()
                ->first();

            return [
                'user_id' => $student->id,
                'name' => $student->name,
                'identifier' => $student->identifier ?? '-',
                'phone' => $student->phone_number,
                'role' => 'mahasiswa',
                'cohort' => $student->entry_year ?? '-',
                'status_info' => "Semester {$student->current_semester} (Kritis DO)",
                'context' => [
                    'nama' => $student->name,
                    'npm' => $student->identifier ?? '-',
                    'angkatan' => $student->entry_year ?? '-',
                    'semester' => (string) $student->current_semester,
                    'judul' => $activeThesis ? ($activeThesis->final_title ?: $activeThesis->title) : 'Belum Memiliki Judul',
                    'pembimbing_1' => $activeThesis->pembimbing1->name ?? '-',
                    'pembimbing_2' => $activeThesis->pembimbing2->name ?? '-',
                    'link_login' => url('/login'),
                ],
            ];
        })->values();
    }

    /**
     * Mahasiswa yang telah menyelesaikan seminar proposal namun belum mendaftar sidang skripsi.
     */
    protected function getMahasiswaBelumSidang(array $filters): Collection
    {
        $query = Thesis::with(['student', 'pembimbing1', 'pembimbing2'])
            ->where('status', 'active')
            ->whereHas('seminarApplications', function ($q) {
                $q->where('status', 'approved');
            })
            ->whereDoesntHave('defenseApplications', function ($q) {
                $q->where('status', 'approved');
            });

        if (!empty($filters['cohort']) && $filters['cohort'] !== 'all') {
            $query->whereHas('student', function ($q) use ($filters) {
                $q->where('entry_year', $filters['cohort']);
            });
        }

        return $query->get()->map(function ($thesis) {
            $student = $thesis->student;
            if (!$student) return null;

            return [
                'user_id' => $student->id,
                'name' => $student->name,
                'identifier' => $student->identifier ?? '-',
                'phone' => $student->phone_number,
                'role' => 'mahasiswa',
                'cohort' => $student->entry_year ?? '-',
                'status_info' => 'Lolos Seminar • Belum Mendaftar Sidang',
                'context' => [
                    'nama' => $student->name,
                    'npm' => $student->identifier ?? '-',
                    'angkatan' => $student->entry_year ?? '-',
                    'judul' => $thesis->final_title ?: $thesis->title,
                    'pembimbing_1' => $thesis->pembimbing1->name ?? '-',
                    'pembimbing_2' => $thesis->pembimbing2->name ?? '-',
                    'link_sidang' => route('thesis-defense-applications.create'),
                    'link_login' => url('/login'),
                ],
            ];
        })->filter()->values();
    }

    /**
     * Mahasiswa angkatan tertentu yang belum mengajukan skripsi sama sekali.
     */
    protected function getMahasiswaBelumSkripsi(array $filters): Collection
    {
        $query = User::where('role', 'mahasiswa')
            ->whereDoesntHave('thesis');

        if (!empty($filters['cohort']) && $filters['cohort'] !== 'all') {
            $query->where('entry_year', $filters['cohort']);
        }

        return $query->get()->map(function ($student) {
            return [
                'user_id' => $student->id,
                'name' => $student->name,
                'identifier' => $student->identifier ?? '-',
                'phone' => $student->phone_number,
                'role' => 'mahasiswa',
                'cohort' => $student->entry_year ?? '-',
                'status_info' => 'Belum Mengajukan Judul Skripsi',
                'context' => [
                    'nama' => $student->name,
                    'npm' => $student->identifier ?? '-',
                    'angkatan' => $student->entry_year ?? '-',
                    'link_pengajuan' => route('theses.create'),
                    'link_login' => url('/login'),
                ],
            ];
        });
    }

    /**
     * Seluruh dosen pembimbing yang memiliki mahasiswa bimbingan skripsi aktif.
     */
    protected function getDosenPembimbingAktif(array $filters): Collection
    {
        $dosenList = User::whereIn('role', ['dosen', 'kaprodi'])
            ->where(function ($q) {
                $q->whereHas('thesesAsP1', function ($t) {
                    $t->where('status', 'active');
                })->orWhereHas('thesesAsP2', function ($t) {
                    $t->where('status', 'active');
                });
            })
            ->withCount([
                'thesesAsP1' => function ($q) { $q->where('status', 'active'); },
                'thesesAsP2' => function ($q) { $q->where('status', 'active'); },
            ])
            ->get();

        return $dosenList->map(function ($dosen) {
            $totalActive = $dosen->theses_as_p1_count + $dosen->theses_as_p2_count;

            return [
                'user_id' => $dosen->id,
                'name' => $dosen->name,
                'identifier' => $dosen->identifier ?? '-',
                'phone' => $dosen->phone_number,
                'role' => 'dosen',
                'cohort' => '-',
                'status_info' => "Membimbing {$totalActive} Mahasiswa Aktif",
                'context' => [
                    'nama' => $dosen->name,
                    'nidn' => $dosen->identifier ?? '-',
                    'jumlah_bimbingan' => (string) $totalActive,
                    'link_dashboard' => url('/dashboard'),
                    'link_login' => url('/login'),
                ],
            ];
        });
    }

    /**
     * Dosen penguji pada jadwal seminar/sidang di gelombang tertentu.
     */
    protected function getDosenPengujiGelombang(array $filters): Collection
    {
        $waveId = $filters['wave_id'] ?? null;
        $wave = $waveId ? Wave::find($waveId) : Wave::where('is_active', true)->first();
        $waveName = $wave ? $wave->name : 'Gelombang Aktif';

        $examinerIds = collect();

        // Ambil penguji dari seminar
        $seminarExaminers = SeminarScheduleDetail::whereHas('schedule', function ($q) use ($waveId) {
            if ($waveId) $q->where('wave_id', $waveId);
        })->get(['examiner1_id', 'examiner2_id']);

        foreach ($seminarExaminers as $detail) {
            if ($detail->examiner1_id) $examinerIds->push($detail->examiner1_id);
            if ($detail->examiner2_id) $examinerIds->push($detail->examiner2_id);
        }

        // Ambil penguji dari sidang
        $defenseExaminers = ThesisDefenseScheduleDetail::whereHas('schedule', function ($q) use ($waveId) {
            if ($waveId) $q->where('wave_id', $waveId);
        })->get(['examiner1_id', 'examiner2_id']);

        foreach ($defenseExaminers as $detail) {
            if ($detail->examiner1_id) $examinerIds->push($detail->examiner1_id);
            if ($detail->examiner2_id) $examinerIds->push($detail->examiner2_id);
        }

        $uniqueIds = $examinerIds->unique()->filter()->values();

        $dosenList = User::whereIn('id', $uniqueIds)->get();

        return $dosenList->map(function ($dosen) use ($waveName) {
            return [
                'user_id' => $dosen->id,
                'name' => $dosen->name,
                'identifier' => $dosen->identifier ?? '-',
                'phone' => $dosen->phone_number,
                'role' => 'dosen',
                'cohort' => '-',
                'status_info' => "Penguji Ujian ({$waveName})",
                'context' => [
                    'nama' => $dosen->name,
                    'nidn' => $dosen->identifier ?? '-',
                    'gelombang' => $waveName,
                    'link_dashboard' => url('/dashboard'),
                    'link_login' => url('/login'),
                ],
            ];
        });
    }

    /**
     * Seluruh mahasiswa aktif.
     */
    protected function getAllMahasiswaAktif(array $filters): Collection
    {
        $query = User::where('role', 'mahasiswa')->where('is_active', true);

        if (!empty($filters['cohort']) && $filters['cohort'] !== 'all') {
            $query->where('entry_year', $filters['cohort']);
        }

        return $query->get()->map(function ($student) {
            return [
                'user_id' => $student->id,
                'name' => $student->name,
                'identifier' => $student->identifier ?? '-',
                'phone' => $student->phone_number,
                'role' => 'mahasiswa',
                'cohort' => $student->entry_year ?? '-',
                'status_info' => 'Mahasiswa Aktif',
                'context' => [
                    'nama' => $student->name,
                    'npm' => $student->identifier ?? '-',
                    'angkatan' => $student->entry_year ?? '-',
                    'link_login' => url('/login'),
                ],
            ];
        });
    }

    /**
     * Daftar kontak pilihan khusus.
     */
    protected function getCustomRecipients(array $filters): Collection
    {
        $userIds = $filters['user_ids'] ?? [];
        if (empty($userIds)) return collect();

        $users = User::whereIn('id', $userIds)->get();

        return $users->map(function ($user) {
            return [
                'user_id' => $user->id,
                'name' => $user->name,
                'identifier' => $user->identifier ?? '-',
                'phone' => $user->phone_number,
                'role' => $user->role,
                'cohort' => $user->entry_year ?? '-',
                'status_info' => ucfirst($user->role),
                'context' => [
                    'nama' => $user->name,
                    'npm' => $user->identifier ?? '-',
                    'nidn' => $user->identifier ?? '-',
                    'angkatan' => $user->entry_year ?? '-',
                    'link_login' => url('/login'),
                ],
            ];
        });
    }

    /**
     * Render message with dynamic placeholder variables replaced.
     *
     * @param string $template
     * @param array $context
     * @return string
     */
    public function renderPersonalizedMessage(string $template, array $context): string
    {
        $msg = $template;

        // Common defaults
        if (!isset($context['link_login'])) {
            $context['link_login'] = url('/login');
        }

        foreach ($context as $key => $val) {
            if (is_scalar($val)) {
                $msg = str_replace('{' . $key . '}', (string) $val, $msg);
            }
        }

        return $msg;
    }

    /**
     * Send broadcast to target recipients and record logs.
     *
     * @param WaBroadcast $broadcast
     * @param array|null $selectedUserIds
     * @return array
     */
    public function executeBroadcast(WaBroadcast $broadcast, ?array $selectedUserIds = null): array
    {
        @set_time_limit(300);
        if (function_exists('ignore_user_abort')) {
            @ignore_user_abort(true);
        }

        $allTargets = $this->getTargetRecipients($broadcast->target_type, $broadcast->target_filter ?? []);

        // Filter if specific subset of user IDs was selected by user
        if (!empty($selectedUserIds)) {
            $targets = $allTargets->whereIn('user_id', $selectedUserIds);
        } else {
            $targets = $allTargets;
        }

        $total = $targets->count();
        $success = 0;
        $failed = 0;
        $delay = max(1, (int) $broadcast->delay_seconds);

        $broadcast->update([
            'status' => 'processing',
            'total_recipients' => $total,
            'successful_count' => 0,
            'failed_count' => 0,
        ]);

        try {
            foreach ($targets as $index => $item) {
                $renderedMsg = $this->renderPersonalizedMessage($broadcast->message_template, $item['context'] ?? []);
                $phone = $item['phone'] ?? null;

                $log = WaBroadcastLog::create([
                    'wa_broadcast_id' => $broadcast->id,
                    'recipient_id' => $item['user_id'] ?? null,
                    'recipient_name' => $item['name'] ?? 'Penerima',
                    'recipient_identifier' => $item['identifier'] ?? '-',
                    'recipient_phone' => $phone,
                    'message_content' => $renderedMsg,
                    'status' => 'pending',
                ]);

                if (empty($phone)) {
                    $log->update([
                        'status' => 'failed',
                        'error_message' => 'Nomor WhatsApp belum terdaftar di profil akun.',
                    ]);
                    $failed++;
                    $broadcast->increment('failed_count');
                    continue;
                }

                try {
                    // Beri jeda halus antar panggilan HTTP ke server Fonnte (0.6 detik)
                    // Sementara jeda antar pesan sebenarnya ditangani secara terjadwal oleh parameter delay Fonnte
                    if ($index > 0) {
                        usleep(600000);
                    }

                    $isSent = $this->whatsAppService->sendMessage($phone, $renderedMsg, $delay);

                    if ($isSent) {
                        $log->update([
                            'status' => 'sent',
                            'sent_at' => now(),
                        ]);
                        $success++;
                        $broadcast->increment('successful_count');
                    } else {
                        $log->update([
                            'status' => 'failed',
                            'error_message' => 'Gateway WhatsApp Fonnte gagal mengirim pesan (periksa kuota/koneksi).',
                        ]);
                        $failed++;
                        $broadcast->increment('failed_count');
                    }
                } catch (\Throwable $e) {
                    Log::error('Broadcast error for recipient ' . $item['name'] . ': ' . $e->getMessage());
                    $log->update([
                        'status' => 'failed',
                        'error_message' => $e->getMessage(),
                    ]);
                    $failed++;
                    $broadcast->increment('failed_count');
                }
            }
        } finally {
            $broadcast->update([
                'status' => 'completed',
                'successful_count' => $success,
                'failed_count' => $failed,
                'sent_at' => now(),
            ]);
        }

        return [
            'total' => $total,
            'successful' => $success,
            'failed' => $failed,
        ];
    }
}
