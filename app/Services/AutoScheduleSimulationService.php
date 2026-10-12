<?php

namespace App\Services;

use App\Models\SeminarSchedule;
use App\Models\SeminarScheduleDetail;
use App\Models\ThesisDefenseSchedule;
use App\Models\ThesisDefenseScheduleDetail;
use App\Models\Thesis;
use App\Models\User;
use App\Models\Wave;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AutoScheduleSimulationService
{
    protected $conflictService;
    protected $scheduleService;

    public function __construct(ConflictService $conflictService, ScheduleService $scheduleService)
    {
        $this->conflictService = $conflictService;
        $this->scheduleService = $scheduleService;
    }

    /**
     * Get eligible candidates who have applied and are ready for scheduling.
     */
    public function getEligibleCandidates(string $type, $waveId = null)
    {
        $targetWave = $waveId ? Wave::find($waveId) : Wave::getCurrentActive();
        if (!$targetWave) {
            $targetWave = Wave::orderBy('id', 'desc')->first();
        }

        if (!$targetWave) {
            return collect();
        }

        if ($type === 'seminar') {
            $scheduledThesisIds = SeminarScheduleDetail::whereNotNull('thesis_id')->pluck('thesis_id');

            return Thesis::with(['student', 'pembimbing1', 'pembimbing2', 'seminarApplication'])
                ->where('status', '!=', 'completed')
                ->where('acc_up_p1', true)
                ->where('acc_up_p2', true)
                ->whereNotIn('id', $scheduledThesisIds)
                ->whereHas('seminarApplication', function ($q) use ($targetWave) {
                    $q->where('wave_id', $targetWave->id);
                })
                ->get();
        } else {
            $scheduledThesisIds = ThesisDefenseScheduleDetail::whereNotNull('thesis_id')->pluck('thesis_id');

            return Thesis::with(['student', 'pembimbing1', 'pembimbing2', 'defenseApplication'])
                ->where('status', '!=', 'completed')
                ->where('acc_sidang_p1', true)
                ->where('acc_sidang_p2', true)
                ->whereNotIn('id', $scheduledThesisIds)
                ->whereHas('defenseApplication', function ($q) use ($targetWave) {
                    $q->where('wave_id', $targetWave->id);
                })
                ->get();
        }
    }

    /**
     * Generate an automated schedule simulation.
     */
    public function generateSimulation(string $type, $waveId, array $options)
    {
        $targetWave = $waveId ? Wave::find($waveId) : Wave::getCurrentActive();
        if (!$targetWave) {
            $targetWave = Wave::orderBy('id', 'desc')->first();
        }

        $date = $options['date'] ?? Carbon::tomorrow()->toDateString();
        $roomCount = max(1, (int) ($options['room_count'] ?? 2));
        $roomNames = $options['room_names'] ?? [];
        $maxStudentsPerRoom = max(1, (int) ($options['max_students_per_room'] ?? 8));
        $startTime = $options['start_time'] ?? '08:30';
        $durationMinutes = max(15, (int) ($options['duration_minutes'] ?? ($type === 'seminar' ? 50 : 60)));
        $breakEnabled = filter_var($options['break_enabled'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $breakStart = $options['break_start'] ?? '11:30';
        $breakEnd = $options['break_end'] ?? '13:00';
        $selectedThesisIds = $options['thesis_ids'] ?? null;

        // Default room names if not provided
        $defaultRoomTemplates = [
            'Ruang Sidang Utama (R. 201)',
            'Ruang Smart Class (R. 202)',
            'Ruang Multimedia (R. 203)',
            'Ruang Rapat Senat (R. 204)',
            'Ruang Sidang B (R. 205)',
        ];

        $resolvedRooms = [];
        for ($i = 0; $i < $roomCount; $i++) {
            $resolvedRooms[] = !empty($roomNames[$i]) ? trim($roomNames[$i]) : ($defaultRoomTemplates[$i] ?? 'Ruangan ' . ($i + 1));
        }

        // Fetch eligible theses
        $candidates = $this->getEligibleCandidates($type, $targetWave?->id);
        if (!empty($selectedThesisIds) && is_array($selectedThesisIds)) {
            $candidates = $candidates->whereIn('id', $selectedThesisIds)->values();
        }

        if ($candidates->isEmpty()) {
            return [
                'status' => 'empty',
                'message' => 'Tidak ada pendaftar ' . ($type === 'seminar' ? 'seminar' : 'sidang') . ' yang siap dijadwalkan pada gelombang ini.',
                'candidates_count' => 0,
                'rooms' => [],
                'conflicts' => [],
                'stats' => [
                    'total_candidates' => 0,
                    'scheduled_candidates' => 0,
                    'unscheduled_candidates' => 0,
                    'room_count' => count($resolvedRooms),
                    'conflict_count' => 0,
                    'grouping_p1_success_rate' => 100,
                ]
            ];
        }

        // Fetch all active lecturers for examiner assignment
        $allDosens = User::where('role', 'dosen')->orderBy('name')->get();

        // 1. GROUPING BY PEMBIMBING 1
        // Mahasiswa dengan Pembimbing 1 yang sama diutamakan berada di 1 ruangan yang sama
        $p1Groups = [];
        foreach ($candidates as $thesis) {
            $p1Id = $thesis->pembimbing1_id ?? 0;
            if (!isset($p1Groups[$p1Id])) {
                $p1Groups[$p1Id] = [
                    'dosen_id' => $p1Id,
                    'dosen_name' => $thesis->pembimbing1?->name ?? 'Tanpa Pembimbing 1',
                    'dosen' => $thesis->pembimbing1,
                    'theses' => collect(),
                ];
            }
            $p1Groups[$p1Id]['theses']->push($thesis);
        }

        // Heuristik: Urutkan kelompok pembimbing 1 dari yang jumlah bimbingannya paling banyak (First-Fit Decreasing)
        uasort($p1Groups, function ($a, $b) {
            return $b['theses']->count() <=> $a['theses']->count();
        });

        // 2. ALLOCATION TO ROOMS (Cluster Bin-Packing)
        $roomsData = [];
        foreach ($resolvedRooms as $index => $roomName) {
            $roomsData[$index] = [
                'room_index' => $index,
                'room_name' => $roomName,
                'title' => strtoupper($type === 'seminar' ? 'Seminar Proposal' : 'Sidang Skripsi') . ' ' . ($targetWave?->name ? strtoupper($targetWave->name) . ' - ' : '') . $roomName,
                'theses' => collect(),
                'max_capacity' => $maxStudentsPerRoom,
                'chairman_id' => null,
                'moderator_id' => null,
                'sessions' => [],
            ];
        }

        $unscheduledTheses = collect();
        $p1FragmentationCount = 0; // Menghitung jika ada kelompok P1 yang terpaksa dipecah
        $totalP1Groups = count($p1Groups);

        foreach ($p1Groups as $group) {
            $groupTheses = $group['theses'];
            $groupSize = $groupTheses->count();

            // Cari ruangan yang masih muat menampung seluruh grup
            $bestRoomIndex = null;
            $bestRemainingCapacity = -1;

            foreach ($roomsData as $rIdx => $rData) {
                $currentCount = $rData['theses']->count();
                $remaining = $rData['max_capacity'] - $currentCount;

                if ($remaining >= $groupSize) {
                    // Cari ruangan yang paling seimbang (ruangan dengan sisa terbanyak)
                    if ($remaining > $bestRemainingCapacity) {
                        $bestRemainingCapacity = $remaining;
                        $bestRoomIndex = $rIdx;
                    }
                }
            }

            if ($bestRoomIndex !== null) {
                // Seluruh mahasiswa kelompok P1 ini masuk ke ruangan yang sama secara utuh
                foreach ($groupTheses as $th) {
                    $roomsData[$bestRoomIndex]['theses']->push($th);
                }
            } else {
                // Jika tidak ada satu ruangan pun yang muat menampung seluruh grup sekaligus:
                // Masukkan sebagian ke ruangan yang sisa kapasitasnya paling besar, sisanya ke ruangan lain
                $remainingGroupTheses = $groupTheses->values();
                $wasFragmented = false;

                while ($remainingGroupTheses->isNotEmpty()) {
                    // Cari ruangan dengan sisa kapasitas terbesar (> 0)
                    $maxRemainRoomIdx = null;
                    $maxRemain = 0;

                    foreach ($roomsData as $rIdx => $rData) {
                        $remaining = $rData['max_capacity'] - $rData['theses']->count();
                        if ($remaining > $maxRemain) {
                            $maxRemain = $remaining;
                            $maxRemainRoomIdx = $rIdx;
                        }
                    }

                    if ($maxRemainRoomIdx !== null && $maxRemain > 0) {
                        $chunk = $remainingGroupTheses->splice(0, $maxRemain);
                        foreach ($chunk as $th) {
                            $roomsData[$maxRemainRoomIdx]['theses']->push($th);
                        }
                        if ($remainingGroupTheses->isNotEmpty()) {
                            $wasFragmented = true;
                        }
                    } else {
                        // Semua ruangan sudah penuh kapasitasnya
                        foreach ($remainingGroupTheses as $th) {
                            $unscheduledTheses->push($th);
                        }
                        break;
                    }
                }

                if ($wasFragmented) {
                    $p1FragmentationCount++;
                }
            }
        }

        // 3. GENERATE TIME SLOTS & EXAMINER ASSIGNMENT
        // Hitung slot waktu per urutan sesi
        $maxSessionsInAnyRoom = 0;
        foreach ($roomsData as $r) {
            $maxSessionsInAnyRoom = max($maxSessionsInAnyRoom, $r['theses']->count());
        }

        $timeSlots = $this->generateTimeSlots(
            $maxSessionsInAnyRoom,
            $startTime,
            $durationMinutes,
            $breakEnabled,
            $breakStart,
            $breakEnd
        );

        // Muat bentrok dari database eksisting pada tanggal tersebut
        $existingDbConflicts = $this->loadExistingDbSchedules($date);

        // Tracking penugasan penguji untuk pemerataan beban (fair load balancing)
        $dosenExaminerCount = [];
        foreach ($allDosens as $dosen) {
            $dosenExaminerCount[$dosen->id] = 0;
        }

        // Warna badge penanda Pembimbing 1 agar visual grouping terlihat jelas
        $p1ColorPalette = [
            'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300 border-blue-200 dark:border-blue-800',
            'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
            'bg-violet-100 text-violet-800 dark:bg-violet-900/40 dark:text-violet-300 border-violet-200 dark:border-violet-800',
            'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 border-amber-200 dark:border-amber-800',
            'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300 border-rose-200 dark:border-rose-800',
            'bg-cyan-100 text-cyan-800 dark:bg-cyan-900/40 dark:text-cyan-300 border-cyan-200 dark:border-cyan-800',
            'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800',
            'bg-teal-100 text-teal-800 dark:bg-teal-900/40 dark:text-teal-300 border-teal-200 dark:border-teal-800',
        ];
        $p1Colors = [];
        $colorIdx = 0;
        foreach (array_keys($p1Groups) as $p1Id) {
            $p1Colors[$p1Id] = $p1ColorPalette[$colorIdx % count($p1ColorPalette)];
            $colorIdx++;
        }

        // Simpan sesi dan tentukan penguji per slot waktu
        // Di setiap slot waktu, kumpulkan status dosen sibuk di semua ruangan
        for ($slotIdx = 0; $slotIdx < $maxSessionsInAnyRoom; $slotIdx++) {
            $slot = $timeSlots[$slotIdx] ?? [
                'start' => Carbon::parse($startTime)->addMinutes($slotIdx * $durationMinutes)->format('H:i'),
                'end' => Carbon::parse($startTime)->addMinutes(($slotIdx + 1) * $durationMinutes)->format('H:i'),
            ];

            // Dosen yang sedang bertugas di slot ini antar seluruh ruangan
            $busyDosenInThisSlot = [];

            // A. Kumpulkan semua Pembimbing 1 dan Pembimbing 2 di semua ruangan pada slot ini
            foreach ($roomsData as $rIdx => $rData) {
                if ($rData['theses']->has($slotIdx)) {
                    $th = $rData['theses']->get($slotIdx);
                    if ($th->pembimbing1_id) {
                        $busyDosenInThisSlot[$th->pembimbing1_id] = [
                            'role' => 'Pembimbing 1',
                            'room_index' => $rIdx,
                            'student_name' => $th->student?->name,
                        ];
                    }
                    if ($th->pembimbing2_id) {
                        $busyDosenInThisSlot[$th->pembimbing2_id] = [
                            'role' => 'Pembimbing 2',
                            'room_index' => $rIdx,
                            'student_name' => $th->student?->name,
                        ];
                    }
                }
            }

            // B. Alokasikan Penguji 1 dan Penguji 2 untuk masing-masing ruangan di slot ini
            foreach ($roomsData as $rIdx => $rData) {
                if (!$rData['theses']->has($slotIdx)) {
                    continue;
                }

                $thesis = $rData['theses']->get($slotIdx);
                $p1Id = $thesis->pembimbing1_id;
                $p2Id = $thesis->pembimbing2_id;

                // Kandidat Penguji 1:
                // - Bukan Pembimbing 1 & bukan Pembimbing 2
                // - Tidak sedang bertugas di slot ini di ruangan manapun
                // - Tidak bentrok dengan jadwal lain di database pada jam tersebut
                $examiner1Candidates = $allDosens->filter(function ($dosen) use ($p1Id, $p2Id, $busyDosenInThisSlot, $existingDbConflicts, $slot) {
                    if ($dosen->id == $p1Id || $dosen->id == $p2Id) return false;
                    if (isset($busyDosenInThisSlot[$dosen->id])) return false;
                    if ($this->isDosenBusyInDb($existingDbConflicts, $dosen->id, $slot['start'], $slot['end'])) return false;
                    return true;
                });

                // Urutkan kandidat berdasarkan jumlah penugasan terendah
                $examiner1 = $examiner1Candidates->sortBy(fn($d) => $dosenExaminerCount[$d->id] ?? 0)->first();

                if (!$examiner1) {
                    // Fallback jika dosen sangat sedikit: ambil dosen di luar pembimbing
                    $examiner1 = $allDosens->filter(fn($d) => $d->id != $p1Id && $d->id != $p2Id)
                        ->sortBy(fn($d) => $dosenExaminerCount[$d->id] ?? 0)
                        ->first();
                }

                if ($examiner1) {
                    $busyDosenInThisSlot[$examiner1->id] = [
                        'role' => 'Penguji 1',
                        'room_index' => $rIdx,
                        'student_name' => $thesis->student?->name,
                    ];
                    $dosenExaminerCount[$examiner1->id] = ($dosenExaminerCount[$examiner1->id] ?? 0) + 1;
                }

                // Kandidat Penguji 2:
                $e1Id = $examiner1?->id;
                $examiner2Candidates = $allDosens->filter(function ($dosen) use ($p1Id, $p2Id, $e1Id, $busyDosenInThisSlot, $existingDbConflicts, $slot) {
                    if ($dosen->id == $p1Id || $dosen->id == $p2Id || $dosen->id == $e1Id) return false;
                    if (isset($busyDosenInThisSlot[$dosen->id])) return false;
                    if ($this->isDosenBusyInDb($existingDbConflicts, $dosen->id, $slot['start'], $slot['end'])) return false;
                    return true;
                });

                $examiner2 = $examiner2Candidates->sortBy(fn($d) => $dosenExaminerCount[$d->id] ?? 0)->first();

                if (!$examiner2) {
                    // Fallback
                    $examiner2 = $allDosens->filter(fn($d) => $d->id != $p1Id && $d->id != $p2Id && $d->id != $e1Id)
                        ->sortBy(fn($d) => $dosenExaminerCount[$d->id] ?? 0)
                        ->first();
                }

                if ($examiner2) {
                    $busyDosenInThisSlot[$examiner2->id] = [
                        'role' => 'Penguji 2',
                        'room_index' => $rIdx,
                        'student_name' => $thesis->student?->name,
                    ];
                    $dosenExaminerCount[$examiner2->id] = ($dosenExaminerCount[$examiner2->id] ?? 0) + 1;
                }

                // Simpan data sesi ke ruangan
                $roomsData[$rIdx]['sessions'][] = [
                    'order' => $slotIdx + 1,
                    'start_time' => $slot['start'],
                    'end_time' => $slot['end'],
                    'thesis_id' => $thesis->id,
                    'thesis' => [
                        'id' => $thesis->id,
                        'title' => $thesis->final_title ?? $thesis->title,
                        'student_name' => $thesis->student?->name ?? 'Mahasiswa',
                        'student_nim' => $thesis->student?->nim ?? '-',
                        'pembimbing1_id' => $p1Id,
                        'pembimbing1_name' => $thesis->pembimbing1?->name ?? '-',
                        'pembimbing2_id' => $p2Id,
                        'pembimbing2_name' => $thesis->pembimbing2?->name ?? null,
                        'p1_color' => $p1Colors[$p1Id] ?? 'bg-slate-100 text-slate-800 border-slate-200',
                    ],
                    'examiner1_id' => $examiner1?->id,
                    'examiner1_name' => $examiner1?->name ?? '-',
                    'examiner2_id' => $examiner2?->id,
                    'examiner2_name' => $examiner2?->name ?? '-',
                    'conflicts' => [],
                ];
            }
        }

        // 4. PENENTUAN KETUA SIDANG & MODERATOR (Saran Otomatis)
        foreach ($roomsData as $rIdx => &$rData) {
            // Saran Ketua: Pembimbing 1 pertama di ruangan ini
            $firstSession = $rData['sessions'][0] ?? null;
            if ($firstSession && !empty($firstSession['thesis']['pembimbing1_id'])) {
                $rData['chairman_id'] = $firstSession['thesis']['pembimbing1_id'];
                $rData['chairman_name'] = $firstSession['thesis']['pembimbing1_name'];
            }

            // Saran Moderator: Penguji 1 atau Dosen lain
            if ($firstSession && !empty($firstSession['examiner1_id'])) {
                $rData['moderator_id'] = $firstSession['examiner1_id'];
                $rData['moderator_name'] = $firstSession['examiner1_name'];
            }
        }
        unset($rData);

        // 5. DETEKSI BENTROK MENYELURUH (Conflict Verification Pass)
        $conflictsReport = $this->verifyConflicts($roomsData, $existingDbConflicts);

        // Hitung statistik simulasi
        $totalCandidates = $candidates->count();
        $scheduledCandidates = $totalCandidates - $unscheduledTheses->count();
        $groupingP1SuccessRate = $totalP1Groups > 0
            ? round((($totalP1Groups - $p1FragmentationCount) / $totalP1Groups) * 100)
            : 100;

        return [
            'status' => 'success',
            'type' => $type,
            'wave_id' => $targetWave?->id,
            'wave_name' => $targetWave?->name ?? 'Gelombang Aktif',
            'date' => $date,
            'date_formatted' => Carbon::parse($date)->locale('id')->translatedFormat('l, d F Y'),
            'start_time' => $startTime,
            'duration_minutes' => $durationMinutes,
            'break_enabled' => $breakEnabled,
            'break_start' => $breakStart,
            'break_end' => $breakEnd,
            'rooms' => array_values($roomsData),
            'unscheduled_theses' => $unscheduledTheses->values(),
            'conflicts' => $conflictsReport['conflicts'],
            'has_conflict' => $conflictsReport['has_conflict'],
            'stats' => [
                'total_candidates' => $totalCandidates,
                'scheduled_candidates' => $scheduledCandidates,
                'unscheduled_candidates' => $unscheduledTheses->count(),
                'room_count' => count($roomsData),
                'conflict_count' => count($conflictsReport['conflicts']),
                'grouping_p1_success_rate' => $groupingP1SuccessRate,
                'all_dosens_involved' => count(array_filter($dosenExaminerCount, fn($c) => $c > 0)),
            ],
            'dosens' => $allDosens->map(fn($d) => ['id' => $d->id, 'name' => $d->name]),
        ];
    }

    /**
     * Generate list of time slots with break consideration.
     */
    protected function generateTimeSlots(int $count, string $startTime, int $durationMinutes, bool $breakEnabled, string $breakStart, string $breakEnd): array
    {
        $slots = [];
        $current = Carbon::parse($startTime);
        $bStart = Carbon::parse($breakStart);
        $bEnd = Carbon::parse($breakEnd);

        for ($i = 0; $i < $count; $i++) {
            $slotStart = $current->copy();
            $slotEnd = $current->copy()->addMinutes($durationMinutes);

            // Jika slot jatuh di atau melewati waktu istirahat
            if ($breakEnabled) {
                // Jika mulai di antara waktu istirahat atau slot selesai melewati breakStart
                if ($slotStart->gte($bStart) && $slotStart->lt($bEnd)) {
                    $slotStart = $bEnd->copy();
                    $slotEnd = $slotStart->copy()->addMinutes($durationMinutes);
                } elseif ($slotEnd->gt($bStart) && $slotStart->lt($bStart)) {
                    // Jika sesi ini melewati batas breakStart, pindahkan ke setelah breakEnd
                    $slotStart = $bEnd->copy();
                    $slotEnd = $slotStart->copy()->addMinutes($durationMinutes);
                }
            }

            $slots[] = [
                'start' => $slotStart->format('H:i'),
                'end' => $slotEnd->format('H:i'),
            ];

            // Waktu mulai sesi berikutnya
            $current = $slotEnd->copy();
        }

        return $slots;
    }

    /**
     * Load existing schedules from database for conflict checking.
     */
    protected function loadExistingDbSchedules(string $date): array
    {
        $seminars = SeminarScheduleDetail::whereHas('schedule', fn($q) => $q->where('date', $date))
            ->with(['thesis'])
            ->get();

        $defenses = ThesisDefenseScheduleDetail::whereHas('schedule', fn($q) => $q->where('date', $date))
            ->with(['thesis'])
            ->get();

        $events = [];
        foreach ($seminars as $s) {
            $events[] = [
                'type' => 'Seminar',
                'start_time' => $s->start_time,
                'end_time' => $s->end_time,
                'examiner1_id' => $s->examiner1_id,
                'examiner2_id' => $s->examiner2_id,
                'pembimbing1_id' => $s->thesis?->pembimbing1_id,
                'pembimbing2_id' => $s->thesis?->pembimbing2_id,
            ];
        }

        foreach ($defenses as $d) {
            $events[] = [
                'type' => 'Sidang',
                'start_time' => $d->start_time,
                'end_time' => $d->end_time,
                'examiner1_id' => $d->examiner1_id,
                'examiner2_id' => $d->examiner2_id,
                'pembimbing1_id' => $d->thesis?->pembimbing1_id,
                'pembimbing2_id' => $d->thesis?->pembimbing2_id,
            ];
        }

        return $events;
    }

    /**
     * Check if a lecturer is busy in the database on a given time slot.
     */
    protected function isDosenBusyInDb(array $existingDbEvents, $dosenId, string $start, string $end): bool
    {
        foreach ($existingDbEvents as $ev) {
            $isOverlap = Carbon::parse($start)->lt(Carbon::parse($ev['end_time'])) &&
                         Carbon::parse($ev['start_time'])->lt(Carbon::parse($end));

            if ($isOverlap) {
                if ($ev['examiner1_id'] == $dosenId ||
                    $ev['examiner2_id'] == $dosenId ||
                    $ev['pembimbing1_id'] == $dosenId ||
                    $ev['pembimbing2_id'] == $dosenId) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Cross-verify all rooms and sessions to guarantee 0 conflicts.
     */
    protected function verifyConflicts(array &$roomsData, array $existingDbEvents): array
    {
        $conflicts = [];

        // Kumpulkan semua penugasan dosen di simulasi ini
        $assignments = [];
        foreach ($roomsData as $rIdx => &$rData) {
            foreach ($rData['sessions'] as $sIdx => &$session) {
                $th = $session['thesis'];
                $sessionConflicts = [];

                $dosenRoles = [
                    'Pembimbing 1' => $th['pembimbing1_id'] ?? null,
                    'Pembimbing 2' => $th['pembimbing2_id'] ?? null,
                    'Penguji 1' => $session['examiner1_id'] ?? null,
                    'Penguji 2' => $session['examiner2_id'] ?? null,
                ];

                foreach ($dosenRoles as $role => $dosenId) {
                    if (!$dosenId) continue;

                    // 1. Cek bentrok dengan jadwal database eksisting
                    if ($this->isDosenBusyInDb($existingDbEvents, $dosenId, $session['start_time'], $session['end_time'])) {
                        $msg = "Dosen {$role} telah memiliki jadwal lain di database pada pukul {$session['start_time']} - {$session['end_time']}.";
                        $sessionConflicts[] = $msg;
                        $conflicts[] = [
                            'room' => $rData['room_name'],
                            'time' => "{$session['start_time']} - {$session['end_time']}",
                            'message' => $msg,
                        ];
                    }

                    // 2. Simpan untuk cross-check antar ruangan di simulasi ini
                    $assignments[] = [
                        'room_index' => $rIdx,
                        'room_name' => $rData['room_name'],
                        'session_index' => $sIdx,
                        'start_time' => $session['start_time'],
                        'end_time' => $session['end_time'],
                        'dosen_id' => $dosenId,
                        'role' => $role,
                        'student_name' => $th['student_name'],
                    ];
                }

                $session['conflicts'] = $sessionConflicts;
            }
        }
        unset($rData, $session);

        // Cross-check antar assignments di simulasi
        $totalAssignments = count($assignments);
        for ($i = 0; $i < $totalAssignments; $i++) {
            for ($j = $i + 1; $j < $totalAssignments; $j++) {
                $a1 = $assignments[$i];
                $a2 = $assignments[$j];

                // Jika ruangan berbeda dan dosen sama
                if ($a1['room_index'] !== $a2['room_index'] && $a1['dosen_id'] === $a2['dosen_id']) {
                    // Cek overlap waktu
                    $isOverlap = Carbon::parse($a1['start_time'])->lt(Carbon::parse($a2['end_time'])) &&
                                 Carbon::parse($a2['start_time'])->lt(Carbon::parse($a1['end_time']));

                    if ($isOverlap) {
                        $dosenName = User::find($a1['dosen_id'])?->name ?? 'Dosen';
                        $msg = "Bentrok Ruangan: {$dosenName} terjadwal di {$a1['room_name']} ({$a1['role']}) dan {$a2['room_name']} ({$a2['role']}) pada jam yang sama ({$a1['start_time']} - {$a1['end_time']}).";
                        $conflicts[] = [
                            'room' => "{$a1['room_name']} & {$a2['room_name']}",
                            'time' => "{$a1['start_time']} - {$a1['end_time']}",
                            'message' => $msg,
                        ];

                        // Tambahkan ke session
                        $roomsData[$a1['room_index']]['sessions'][$a1['session_index']]['conflicts'][] = $msg;
                        $roomsData[$a2['room_index']]['sessions'][$a2['session_index']]['conflicts'][] = $msg;
                    }
                }
            }
        }

        return [
            'has_conflict' => !empty($conflicts),
            'conflicts' => array_unique($conflicts, SORT_REGULAR),
        ];
    }

    /**
     * Apply and persist simulation data into official database records.
     */
    public function applySimulation(string $type, $waveId, array $roomsData, string $date)
    {
        return DB::transaction(function () use ($type, $waveId, $roomsData, $date) {
            $modelClass = $type === 'seminar' ? SeminarSchedule::class : ThesisDefenseSchedule::class;
            $detailModelClass = $type === 'seminar' ? SeminarScheduleDetail::class : ThesisDefenseScheduleDetail::class;

            $createdSchedules = [];

            foreach ($roomsData as $room) {
                if (empty($room['sessions'])) {
                    continue;
                }

                // Format data untuk ScheduleService
                $schedulePayload = [
                    'title' => $room['title'] ?? ($room['room_name'] . ' - ' . Carbon::parse($date)->format('d M Y')),
                    'date' => $date,
                    'chairman_id' => !empty($room['chairman_id']) ? $room['chairman_id'] : null,
                    'moderator_id' => !empty($room['moderator_id']) ? $room['moderator_id'] : null,
                    'location' => $room['room_name'] ?? 'Ruangan Ujian',
                    'meeting_link' => $room['meeting_link'] ?? null,
                    'details' => [],
                ];

                foreach ($room['sessions'] as $session) {
                    $schedulePayload['details'][] = [
                        'thesis_id' => $session['thesis_id'] ?? null,
                        'activity_name' => $session['activity_name'] ?? null,
                        'start_time' => $session['start_time'],
                        'end_time' => $session['end_time'],
                        'examiner1_id' => !empty($session['examiner1_id']) ? $session['examiner1_id'] : null,
                        'examiner2_id' => !empty($session['examiner2_id']) ? $session['examiner2_id'] : null,
                    ];
                }

                $schedule = $this->scheduleService->storeSchedule($modelClass, $detailModelClass, $schedulePayload, $waveId);
                $createdSchedules[] = $schedule;
            }

            return $createdSchedules;
        });
    }
}
