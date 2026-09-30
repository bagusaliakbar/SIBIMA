<?php

namespace App\Services;

use App\Models\ThesisDefenseScheduleDetail;
use App\Models\Wave;
use App\Models\User;
use App\Models\MentoringSession;
use Carbon\Carbon;

class MonitoringService
{
    /**
     * Get the active wave or default.
     */
    public function getActiveWave($selectedWaveId = null)
    {
        $activeWave = Wave::getCurrentActive();
        $selectedWaveId = $selectedWaveId ?: $activeWave?->id;

        return [$activeWave, $selectedWaveId];
    }

    /**
     * Calculate defense scores and grade.
     */
    public function calculateDefenseScores(ThesisDefenseScheduleDetail $detail)
    {
        $detail->load(['thesis.pembimbing1', 'thesis.pembimbing2', 'revisions']);
        
        $revP1 = $detail->revisions->where('examiner_id', $detail->thesis->pembimbing1_id)->first();
        $revE1 = $detail->revisions->where('examiner_id', $detail->examiner1_id)->first();
        $revE2 = $detail->revisions->where('examiner_id', $detail->examiner2_id)->first();

        $calc = function($rev) {
            if (!$rev || $rev->score_presentation === null) return null;
            return ($rev->score_presentation * 0.25) + ($rev->score_explanation * 0.40) + ($rev->score_writing * 0.35);
        };

        $scoreP1 = $calc($revP1);
        $scoreE1 = $calc($revE1);
        $scoreE2 = $calc($revE2);

        $scores = collect([$scoreP1, $scoreE1, $scoreE2])->filter(fn($s) => $s !== null);
        $totalScore = $scores->sum();
        $finalScore = $scores->count() > 0 ? $totalScore / $scores->count() : 0;

        $finalGrade = $scores->count() > 0 ? $this->getGrade($finalScore) : '-';

        // Averages for presentation, explanation, writing
        $pres_scores = collect([$revP1->score_presentation ?? null, $revE1->score_presentation ?? null, $revE2->score_presentation ?? null])->filter(fn($s) => $s !== null);
        $avgPres = $pres_scores->count() > 0 ? $pres_scores->avg() : 0;
        
        $expl_scores = collect([$revP1->score_explanation ?? null, $revE1->score_explanation ?? null, $revE2->score_explanation ?? null])->filter(fn($s) => $s !== null);
        $avgExpl = $expl_scores->count() > 0 ? $expl_scores->avg() : 0;
        
        $writ_scores = collect([$revP1->score_writing ?? null, $revE1->score_writing ?? null, $revE2->score_writing ?? null])->filter(fn($s) => $s !== null);
        $avgWrit = $writ_scores->count() > 0 ? $writ_scores->avg() : 0;

        return compact(
            'revP1', 'revE1', 'revE2', 'scoreP1', 'scoreE1', 'scoreE2', 
            'avgPres', 'avgExpl', 'avgWrit', 'finalScore', 'finalGrade'
        );
    }

    /**
     * Calculate seminar scores and grade.
     */
    public function calculateSeminarScores(\App\Models\SeminarScheduleDetail $detail)
    {
        $detail->load(['thesis.pembimbing1', 'revisions']);
        
        $revE1 = $detail->revisions->where('examiner_id', $detail->examiner1_id)->first();
        $revE2 = $detail->revisions->where('examiner_id', $detail->examiner2_id)->first();

        $calc = function($rev) {
            if (!$rev || $rev->score_presentation === null) return null;
            return ($rev->score_presentation * 0.25) + ($rev->score_explanation * 0.40) + ($rev->score_writing * 0.35);
        };

        $scoreE1 = $calc($revE1);
        $scoreE2 = $calc($revE2);

        $scores = collect([$scoreE1, $scoreE2])->filter(fn($s) => $s !== null);
        $totalScore = $scores->sum();
        $finalScore = $scores->count() > 0 ? $totalScore / $scores->count() : 0;

        $finalGrade = $scores->count() > 0 ? $this->getGrade($finalScore) : '-';

        // Averages
        $pres_scores = collect([$revE1->score_presentation ?? null, $revE2->score_presentation ?? null])->filter(fn($s) => $s !== null);
        $avgPres = $pres_scores->count() > 0 ? $pres_scores->avg() : 0;
        
        $expl_scores = collect([$revE1->score_explanation ?? null, $revE2->score_explanation ?? null])->filter(fn($s) => $s !== null);
        $avgExpl = $expl_scores->count() > 0 ? $expl_scores->avg() : 0;
        
        $writ_scores = collect([$revE1->score_writing ?? null, $revE2->score_writing ?? null])->filter(fn($s) => $s !== null);
        $avgWrit = $writ_scores->count() > 0 ? $writ_scores->avg() : 0;

        return compact(
            'revE1', 'revE2', 'scoreE1', 'scoreE2', 
            'avgPres', 'avgExpl', 'avgWrit', 'finalScore', 'finalGrade'
        );
    }


    /**
     * Convert score to grade.
     */
    public function getGrade($score)
    {
        if ($score >= 80) return 'A';
        if ($score >= 70) return 'B';
        if ($score >= 60) return 'C';
        if ($score >= 50) return 'D';
        return 'E';
    }

    /**
     * Get weekly mentoring monitoring data and statistics.
     */
    public function getWeeklyMentoringData(\Carbon\Carbon $startDate, \Carbon\Carbon $endDate, array $filters = [])
    {
        $search = $filters['search'] ?? null;
        $pembimbingId = $filters['pembimbing_id'] ?? null;
        $entryYear = $filters['entry_year'] ?? null;
        $complianceStatus = $filters['compliance_status'] ?? null;

        $thesesQuery = \App\Models\Thesis::with([
            'student',
            'pembimbing1',
            'pembimbing2',
            'mentoringSessions' => function ($q) use ($startDate, $endDate) {
                $q->where('status', 'completed')
                    ->where('is_absent', false)
                    ->whereBetween('scheduled_at', [$startDate, $endDate])
                    ->orderBy('scheduled_at', 'desc');
            }
        ])
        ->where('status', '!=', 'completed')
        ->when($pembimbingId, function ($q) use ($pembimbingId) {
            $q->where(function ($sub) use ($pembimbingId) {
                $sub->where('pembimbing1_id', $pembimbingId)
                    ->orWhere('pembimbing2_id', $pembimbingId);
            });
        })
        ->when($entryYear, function ($q) use ($entryYear) {
            $q->whereHas('student', function ($sub) use ($entryYear) {
                $sub->where('entry_year', $entryYear);
            });
        })
        ->when($search, function ($q) use ($search) {
            $q->where(function ($sq) use ($search) {
                $sq->whereHas('student', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('identifier', 'like', "%{$search}%");
                })
                ->orWhere('title', 'like', "%{$search}%")
                ->orWhere('final_title', 'like', "%{$search}%");
            });
        })
        ->orderBy('created_at', 'desc');

        $allTheses = $thesesQuery->get();

        // Compute compliance for each thesis in the week
        $processedTheses = $allTheses->map(function ($thesis) {
            $sessions = $thesis->mentoringSessions;
            $p1Sessions = $sessions->where('dosen_id', $thesis->pembimbing1_id);
            $p2Sessions = $sessions->where('dosen_id', $thesis->pembimbing2_id);

            $countP1 = $p1Sessions->count();
            $countP2 = $p2Sessions->count();
            $latestP1 = $p1Sessions->first();
            $latestP2 = $p2Sessions->first();

            $hasP1 = (bool) $thesis->pembimbing1_id;
            $hasP2 = (bool) $thesis->pembimbing2_id;

            if ($hasP1 && $hasP2) {
                if ($countP1 >= 1 && $countP2 >= 1) {
                    $status = 'compliant';
                    $label = 'Lengkap (P1 & P2)';
                } elseif ($countP1 >= 1 || $countP2 >= 1) {
                    $status = 'partial';
                    $label = $countP1 >= 1 ? 'Hanya P1' : 'Hanya P2';
                } else {
                    $status = 'inactive';
                    $label = 'Belum Bimbingan';
                }
            } elseif ($hasP1) {
                if ($countP1 >= 1) {
                    $status = 'compliant';
                    $label = 'Lengkap (P1)';
                } else {
                    $status = 'inactive';
                    $label = 'Belum Bimbingan';
                }
            } else {
                $status = 'inactive';
                $label = 'Belum Ada Pembimbing';
            }

            $thesis->weekly_p1_count = $countP1;
            $thesis->weekly_p2_count = $countP2;
            $thesis->weekly_latest_p1 = $latestP1;
            $thesis->weekly_latest_p2 = $latestP2;
            $thesis->weekly_compliance_status = $status;
            $thesis->weekly_compliance_label = $label;

            return $thesis;
        });

        // Compute aggregate KPI stats across all matching items
        $totalActive = $processedTheses->count();
        $compliantCount = $processedTheses->where('weekly_compliance_status', 'compliant')->count();
        $partialCount = $processedTheses->where('weekly_compliance_status', 'partial')->count();
        $inactiveCount = $processedTheses->where('weekly_compliance_status', 'inactive')->count();

        $stats = [
            'total' => $totalActive,
            'compliant' => $compliantCount,
            'compliant_percent' => $totalActive > 0 ? round(($compliantCount / $totalActive) * 100) : 0,
            'partial' => $partialCount,
            'partial_percent' => $totalActive > 0 ? round(($partialCount / $totalActive) * 100) : 0,
            'inactive' => $inactiveCount,
            'inactive_percent' => $totalActive > 0 ? round(($inactiveCount / $totalActive) * 100) : 0,
        ];

        // Filter by compliance status if requested
        $filteredCollection = $processedTheses;
        if ($complianceStatus && in_array($complianceStatus, ['compliant', 'partial', 'inactive'])) {
            $filteredCollection = $processedTheses->where('weekly_compliance_status', $complianceStatus)->values();
        }

        // Paginate manually
        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $perPage = 15;
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $filteredCollection->forPage($page, $perPage)->values(),
            $filteredCollection->count(),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'query' => request()->query()]
        );

        return [
            'paginated' => $paginated,
            'all' => $filteredCollection,
            'stats' => $stats,
        ];
    }

    /**
     * Generate reminder text for students who haven't completed weekly mentoring.
     */
    public function generateWeeklyReminderMessage(\App\Models\Thesis $thesis, \Carbon\Carbon $startDate, \Carbon\Carbon $endDate): string
    {
        $studentName = $thesis->student ? ucwords(strtolower($thesis->student->name)) : 'Mahasiswa';
        $weekRange = $startDate->locale('id')->isoFormat('D MMMM') . ' - ' . $endDate->locale('id')->isoFormat('D MMMM Y');
        $p1Name = $thesis->pembimbing1 ? $thesis->pembimbing1->name : '-';
        $p2Name = $thesis->pembimbing2 ? $thesis->pembimbing2->name : '-';
        
        $p1Count = $thesis->weekly_p1_count ?? 0;
        $p2Count = $thesis->weekly_p2_count ?? 0;

        if ($p1Count == 0 && $p2Count == 0) {
            $missingNotice = "ke Pembimbing 1 ({$p1Name}) maupun ke Pembimbing 2 ({$p2Name})";
        } elseif ($p1Count == 0) {
            $missingNotice = "ke Pembimbing 1 ({$p1Name})";
        } else {
            $missingNotice = "ke Pembimbing 2 ({$p2Name})";
        }

        return "Halo Sdr/i *{$studentName}*,\n\nBerdasarkan pantauan sistem SIBIMA FASILKOM UNSUB untuk periode minggu ini (*{$weekRange}*), Anda belum tercatat melakukan sesi bimbingan skripsi {$missingNotice}.\n\nSesuai standar akademik, mahasiswa diwajibkan melakukan bimbingan *minimal 1x setiap minggunya* baik ke Pembimbing 1 dan Pembimbing 2. Mohon segera berkoordinasi dan menjadwalkan sesi bimbingan dengan dosen pembimbing Anda.\n\nTetap semangat menyelesaikan tugas akhir Anda!";
    }

    /**
     * Get critical students query.
     */
    public function getCriticalStudentsQuery($search = null, $pembimbingId = null)
    {
        return User::criticalSemester()
            ->whereHas('thesis', function($q) use ($pembimbingId) {
                $q->where('status', '!=', 'completed');
                if ($pembimbingId) {
                    $q->where(function($sub) use ($pembimbingId) {
                        $sub->where('pembimbing1_id', $pembimbingId)
                            ->orWhere('pembimbing2_id', $pembimbingId);
                    });
                }
            })
            ->when($search, function ($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('identifier', 'like', "%{$search}%");
                });
            })
            ->with(['thesis.pembimbing1', 'thesis.pembimbing2'])
            ->orderBy('entry_year', 'asc');
    }

    /**
     * Get mentoring activity rankings (most active) and early warning radar (inactive/at-risk)
     * for both lecturers and students.
     */
    public function getMentoringActivityData(array $filters = []): array
    {
        $period = $filters['period'] ?? 'this_month';
        $now = \Carbon\Carbon::now();

        // 1. Resolve date range based on period
        $startDate = null;
        $endDate = null;

        switch ($period) {
            case 'last_30_days':
                $startDate = $now->copy()->subDays(30)->startOfDay();
                $endDate = $now->copy()->endOfDay();
                $periodLabel = '30 Hari Terakhir (' . $startDate->format('d M') . ' - ' . $endDate->format('d M Y') . ')';
                break;
            case 'last_90_days':
                $startDate = $now->copy()->subDays(90)->startOfDay();
                $endDate = $now->copy()->endOfDay();
                $periodLabel = '90 Hari Terakhir (' . $startDate->format('d M') . ' - ' . $endDate->format('d M Y') . ')';
                break;
            case 'this_semester':
                if ($now->month >= 3 && $now->month <= 8) {
                    $startDate = \Carbon\Carbon::create($now->year, 3, 1, 0, 0, 0);
                    $endDate = \Carbon\Carbon::create($now->year, 8, 31, 23, 59, 59);
                    $periodLabel = 'Semester Genap ' . ($now->year - 1) . '/' . $now->year;
                } else {
                    $semYear = $now->month >= 9 ? $now->year : $now->year - 1;
                    $startDate = \Carbon\Carbon::create($semYear, 9, 1, 0, 0, 0);
                    $endDate = \Carbon\Carbon::create($semYear + 1, 2, 28, 23, 59, 59);
                    $periodLabel = 'Semester Ganjil ' . $semYear . '/' . ($semYear + 1);
                }
                break;
            case 'all_time':
                $startDate = null;
                $endDate = null;
                $periodLabel = 'Semua Waktu (Keseluruhan)';
                break;
            case 'custom':
                if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
                    $startDate = \Carbon\Carbon::parse($filters['date_from'])->startOfDay();
                    $endDate = \Carbon\Carbon::parse($filters['date_to'])->endOfDay();
                    $periodLabel = \Carbon\Carbon::parse($filters['date_from'])->format('d M Y') . ' s/d ' . \Carbon\Carbon::parse($filters['date_to'])->format('d M Y');
                } else {
                    $startDate = $now->copy()->startOfMonth();
                    $endDate = $now->copy()->endOfMonth();
                    $periodLabel = 'Bulan Ini (' . $now->translatedFormat('F Y') . ')';
                }
                break;
            case 'this_month':
            default:
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                $periodLabel = 'Bulan Ini (' . $now->translatedFormat('F Y') . ')';
                break;
        }

        $cohort = $filters['cohort'] ?? null;
        $search = trim($filters['search'] ?? '');

        // 2. Fetch all completed sessions for this period
        $periodSessionsQuery = \App\Models\MentoringSession::where('status', 'completed')
            ->where('is_absent', false);

        if ($startDate && $endDate) {
            $periodSessionsQuery->whereBetween('scheduled_at', [$startDate, $endDate]);
        }

        $periodSessions = $periodSessionsQuery->get(['id', 'thesis_id', 'dosen_id', 'scheduled_at']);

        // Latest session per dosen and per thesis all-time
        $latestSessionByDosen = \App\Models\MentoringSession::where('status', 'completed')
            ->where('is_absent', false)
            ->selectRaw('dosen_id, MAX(scheduled_at) as last_session_at')
            ->groupBy('dosen_id')
            ->pluck('last_session_at', 'dosen_id');

        $latestSessionByThesis = \App\Models\MentoringSession::where('status', 'completed')
            ->where('is_absent', false)
            ->selectRaw('thesis_id, MAX(scheduled_at) as last_session_at')
            ->groupBy('thesis_id')
            ->pluck('last_session_at', 'thesis_id');

        // All-time session counts
        $allTimeCountsByDosen = \App\Models\MentoringSession::where('status', 'completed')
            ->where('is_absent', false)
            ->selectRaw('dosen_id, COUNT(*) as total')
            ->groupBy('dosen_id')
            ->pluck('total', 'dosen_id');

        $allTimeCountsByThesis = \App\Models\MentoringSession::where('status', 'completed')
            ->where('is_absent', false)
            ->selectRaw('thesis_id, COUNT(*) as total')
            ->groupBy('thesis_id')
            ->pluck('total', 'thesis_id');

        // ==========================================
        // A. DOSEN RANKINGS & INACTIVE RADAR
        // ==========================================
        $allActiveTheses = \App\Models\Thesis::where('status', '!=', 'completed')
            ->get(['id', 'student_id', 'pembimbing1_id', 'pembimbing2_id', 'title', 'status', 'created_at']);

        $allDosens = User::where('role', 'dosen')
            ->where('is_active', true)
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('identifier', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->get();

        $dosenList = $allDosens->map(function ($dosen) use ($periodSessions, $allActiveTheses, $latestSessionByDosen, $allTimeCountsByDosen, $now) {
            $dosenPeriodSessions = $periodSessions->where('dosen_id', $dosen->id);
            $sessionsInPeriod = $dosenPeriodSessions->count();
            $uniqueStudentsInPeriod = $dosenPeriodSessions->pluck('thesis_id')->unique()->count();

            $supervisedTheses = $allActiveTheses->filter(function ($t) use ($dosen) {
                return $t->pembimbing1_id === $dosen->id || $t->pembimbing2_id === $dosen->id;
            });
            $supervisedCount = $supervisedTheses->count();

            $lastSessionAt = isset($latestSessionByDosen[$dosen->id]) ? \Carbon\Carbon::parse($latestSessionByDosen[$dosen->id]) : null;
            $daysSinceLastSession = $lastSessionAt ? (int) $lastSessionAt->diffInDays($now) : null;
            $totalAllTime = $allTimeCountsByDosen[$dosen->id] ?? 0;

            if ($sessionsInPeriod >= 8) {
                $activityLevel = 'Sangat Rajin';
                $activityColor = 'emerald';
            } elseif ($sessionsInPeriod >= 4) {
                $activityLevel = 'Rajin';
                $activityColor = 'blue';
            } elseif ($sessionsInPeriod >= 1) {
                $activityLevel = 'Cukup Aktif';
                $activityColor = 'amber';
            } else {
                if ($supervisedCount === 0) {
                    $activityLevel = 'Tidak Ada Bimbingan';
                    $activityColor = 'slate';
                } else {
                    $activityLevel = 'Pasif / Perlu Perhatian';
                    $activityColor = 'rose';
                }
            }

            return [
                'id' => $dosen->id,
                'name' => $dosen->name,
                'identifier' => $dosen->identifier,
                'email' => $dosen->email,
                'phone' => $dosen->phone_number ?? $dosen->phone,
                'avatar_url' => $dosen->avatar_url,
                'supervised_count' => $supervisedCount,
                'sessions_in_period' => $sessionsInPeriod,
                'unique_students_in_period' => $uniqueStudentsInPeriod,
                'total_all_time' => $totalAllTime,
                'last_session_at' => $lastSessionAt,
                'days_since_last' => $daysSinceLastSession,
                'activity_level' => $activityLevel,
                'activity_color' => $activityColor,
            ];
        });

        $topDosens = $dosenList->filter(fn($d) => $d['sessions_in_period'] > 0 || $d['supervised_count'] > 0)
            ->sortByDesc('sessions_in_period')
            ->values()
            ->map(function ($d, $idx) {
                $d['rank'] = $idx + 1;
                return $d;
            });

        $inactiveDosens = $dosenList->filter(function ($d) {
            return $d['supervised_count'] > 0 && ($d['sessions_in_period'] === 0 || ($d['days_since_last'] !== null && $d['days_since_last'] >= 14));
        })->sortByDesc(function ($d) {
            return $d['days_since_last'] === null ? 9999 : $d['days_since_last'];
        })->values();

        // ==========================================
        // B. MAHASISWA RANKINGS & INACTIVE RADAR
        // ==========================================
        $thesesQuery = \App\Models\Thesis::with(['student', 'pembimbing1', 'pembimbing2', 'seminarApplications'])
            ->where('status', '!=', 'completed')
            ->whereHas('student')
            ->when($cohort, function ($q) use ($cohort) {
                $q->whereHas('student', function ($sq) use ($cohort) {
                    $sq->where('entry_year', $cohort);
                });
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->whereHas('student', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%")
                           ->orWhere('identifier', 'like', "%{$search}%");
                    })
                    ->orWhere('title', 'like', "%{$search}%");
                });
            });

        $theses = $thesesQuery->get();

        $studentList = $theses->map(function ($thesis) use ($periodSessions, $latestSessionByThesis, $allTimeCountsByThesis, $now) {
            $student = $thesis->student;

            $thesisPeriodSessions = $periodSessions->where('thesis_id', $thesis->id);
            $p1Sessions = $thesisPeriodSessions->where('dosen_id', $thesis->pembimbing1_id)->count();
            $p2Sessions = $thesisPeriodSessions->where('dosen_id', $thesis->pembimbing2_id)->count();
            $sessionsInPeriod = $thesisPeriodSessions->count();

            $totalAllTime = $allTimeCountsByThesis[$thesis->id] ?? 0;
            $lastSessionAt = isset($latestSessionByThesis[$thesis->id]) ? \Carbon\Carbon::parse($latestSessionByThesis[$thesis->id]) : null;
            $daysSinceLastSession = $lastSessionAt ? (int) $lastSessionAt->diffInDays($now) : null;

            $stage = 'Bab 1-3';
            if ($thesis->acc_sidang_p1 && $thesis->acc_sidang_p2) {
                $stage = 'Siap Sidang';
            } elseif ($thesis->seminarApplications && $thesis->seminarApplications->where('status', 'approved')->count() > 0) {
                $stage = 'Bab 4-5';
            } elseif ($thesis->acc_up_p1 && $thesis->acc_up_p2) {
                $stage = 'Siap Sempro';
            }

            if ($daysSinceLastSession !== null && $daysSinceLastSession <= 7) {
                $healthStatus = 'Aktif Mingguan';
                $healthColor = 'emerald';
            } elseif ($daysSinceLastSession !== null && $daysSinceLastSession <= 14) {
                $healthStatus = 'Waspada (1-2 Minggu)';
                $healthColor = 'amber';
            } else {
                $healthStatus = $lastSessionAt ? 'Kritis (> 2 Minggu)' : 'Belum Pernah Bimbingan';
                $healthColor = 'rose';
            }

            return [
                'thesis_id' => $thesis->id,
                'student_id' => $student->id,
                'name' => $student->name,
                'identifier' => $student->identifier,
                'email' => $student->email,
                'phone' => $student->phone_number ?? $student->phone,
                'avatar_url' => $student->avatar_url,
                'entry_year' => $student->entry_year,
                'title' => $thesis->title,
                'stage' => $stage,
                'pembimbing1_name' => $thesis->pembimbing1?->name,
                'pembimbing2_name' => $thesis->pembimbing2?->name,
                'sessions_in_period' => $sessionsInPeriod,
                'p1_sessions' => $p1Sessions,
                'p2_sessions' => $p2Sessions,
                'total_all_time' => $totalAllTime,
                'last_session_at' => $lastSessionAt,
                'days_since_last' => $daysSinceLastSession,
                'health_status' => $healthStatus,
                'health_color' => $healthColor,
            ];
        });

        $topStudents = $studentList->filter(fn($s) => $s['sessions_in_period'] > 0 || $s['total_all_time'] > 0)
            ->sortByDesc('sessions_in_period')
            ->values()
            ->map(function ($s, $idx) {
                $s['rank'] = $idx + 1;
                return $s;
            });

        $inactiveStudents = $studentList->filter(function ($s) {
            return $s['sessions_in_period'] === 0 || ($s['days_since_last'] !== null && $s['days_since_last'] >= 14) || $s['last_session_at'] === null;
        })->sortByDesc(function ($s) {
            return $s['days_since_last'] === null ? 9999 : $s['days_since_last'];
        })->values();

        // ==========================================
        // C. KPI AGGREGATE STATS
        // ==========================================
        $totalSessionsInPeriod = $periodSessions->count();
        $activeDosenCount = $allDosens->count();
        $avgSessionsPerDosen = $activeDosenCount > 0 ? round($totalSessionsInPeriod / $activeDosenCount, 1) : 0;
        $topDosen = $topDosens->first();
        $topStudent = $topStudents->first();

        $kpi = [
            'total_sessions_in_period' => $totalSessionsInPeriod,
            'avg_sessions_per_dosen' => $avgSessionsPerDosen,
            'top_dosen_name' => $topDosen['name'] ?? '-',
            'top_dosen_sessions' => $topDosen['sessions_in_period'] ?? 0,
            'top_student_name' => $topStudent['name'] ?? '-',
            'top_student_sessions' => $topStudent['sessions_in_period'] ?? 0,
            'inactive_students_count' => $inactiveStudents->count(),
            'inactive_dosens_count' => $inactiveDosens->count(),
        ];

        return [
            'period' => $period,
            'period_label' => $periodLabel,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'cohort' => $cohort,
            'search' => $search,
            'kpi' => $kpi,
            'top_dosens' => $topDosens,
            'inactive_dosens' => $inactiveDosens,
            'top_students' => $topStudents,
            'inactive_students' => $inactiveStudents,
        ];
    }

    /**
     * Generate reminder message text for inactive student or dosen.
     */
    public function generateActivityReminderMessage(string $type, User $user, ?\App\Models\Thesis $thesis = null, ?int $daysInactive = null): string
    {
        $userName = ucwords(strtolower($user->name));

        if ($type === 'dosen') {
            $inactiveNotice = ($daysInactive && $daysInactive < 9000) ? "dalam *{$daysInactive} hari* terakhir" : "pada periode ini";
            return "Yth. Bapak/Ibu *{$userName}*,\n\nSalam hangat dari Program Studi FASILKOM UNSUB.\n\nBerdasarkan pantauan sistem SIBIMA, belum tercatat aktivitas sesi bimbingan skripsi dengan mahasiswa bimbingan aktif Bapak/Ibu {$inactiveNotice}.\n\nMohon kesediaan Bapak/Ibu untuk memeriksa pengajuan jadwal atau mengoordinasikan sesi bimbingan bersama mahasiswa bimbingan agar progres skripsi mereka tetap berjalan lancar.\n\nTerima kasih banyak atas perhatian dan dedikasi Bapak/Ibu.";
        }

        $p1 = $thesis?->pembimbing1?->name ?? 'Pembimbing 1';
        $p2 = $thesis?->pembimbing2?->name ?? 'Pembimbing 2';
        $inactiveText = ($daysInactive && $daysInactive < 9000) ? "selama *{$daysInactive} hari* terakhir" : "sejak pengajuan skripsi";

        return "Halo Sdr/i *{$userName}*,\n\nBerdasarkan pantauan radar keaktifan bimbingan SIBIMA FASILKOM UNSUB, Anda tercatat belum melakukan sesi bimbingan skripsi {$inactiveText}.\n\nKami mengingatkan agar Anda *segera berinisiatif menghubungi Dosen Pembimbing* ({$p1} & {$p2}) untuk mengajukan jadwal dan berkonsultasi mengenai kelanjutan tugas akhir Anda.\n\nJangan biarkan skripsi Anda tertunda. Tetap semangat menyelesaikan studi!";
    }

    /**
     * Get uncompleted mentoring sessions data grouped by lecturer and flat sessions.
     */
    public function getUncompletedMentoringData(array $filters = []): array
    {
        $scope = $filters['scope'] ?? 'all'; // 'all' (default), 'overdue' (lewat hari/sebelum hari ini), 'today' (hari ini terlewat)
        $dosenId = $filters['dosen_id'] ?? null;
        $search = $filters['search'] ?? null;

        // Base Query: KETAT HANYA SESI YANG BELUM SELESAI DAN TANGGAL/JAMNYA SUDAH TERLEWAT
        $baseQuery = MentoringSession::with(['thesis.student', 'thesis.pembimbing1', 'thesis.pembimbing2', 'dosen'])
            ->whereNotIn('status', ['completed', 'rejected'])
            ->where('scheduled_at', '<=', now());

        if ($scope === 'today') {
            $baseQuery->whereDate('scheduled_at', Carbon::today());
        } elseif ($scope === 'overdue') {
            $baseQuery->where('scheduled_at', '<', Carbon::today());
        }
        // jika 'all', tetap where('scheduled_at', '<=', now())

        if ($dosenId) {
            $baseQuery->where(function($q) use ($dosenId) {
                $q->where('dosen_id', $dosenId)
                  ->orWhereHas('thesis', fn($t) => $t->where('pembimbing1_id', $dosenId)->orWhere('pembimbing2_id', $dosenId));
            });
        }

        if ($search) {
            $baseQuery->where(function($q) use ($search) {
                $q->where('topic', 'like', "%{$search}%")
                  ->orWhereHas('dosen', fn($dq) => $dq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('thesis.student', fn($sq) => $sq->where('name', 'like', "%{$search}%")->orWhere('identifier', 'like', "%{$search}%"));
            });
        }

        // All matching sessions
        $allSessions = (clone $baseQuery)->orderBy('scheduled_at', 'asc')->get();

        // Total global metrics across all uncompleted sessions (HANYA YANG SUDAH TERLEWAT)
        $globalUncompletedCount = MentoringSession::whereNotIn('status', ['completed', 'rejected'])
            ->where('scheduled_at', '<=', now())
            ->count();
        $globalOverdueCount = MentoringSession::whereNotIn('status', ['completed', 'rejected'])
            ->where('scheduled_at', '<', Carbon::today())
            ->count();
        $globalTodayCount = MentoringSession::whereNotIn('status', ['completed', 'rejected'])
            ->whereDate('scheduled_at', Carbon::today())
            ->where('scheduled_at', '<=', now())
            ->count();

        $filteredSessionsCount = $allSessions->count();
        $affectedStudentsCount = $allSessions->pluck('thesis.student_id')->filter()->unique()->count();

        // Group by lecturer
        $dosenGroups = $allSessions->groupBy(function($session) {
            return $session->dosen_id ?: ($session->thesis?->pembimbing1_id ?: null);
        })->filter(function($group, $key) {
            return !empty($key);
        });

        $dosenUsers = User::whereIn('id', $dosenGroups->keys())->get()->keyBy('id');

        $lecturersData = $dosenGroups->map(function($sessions, $lecturerId) use ($dosenUsers) {
            $dosen = $dosenUsers->get($lecturerId);
            if (!$dosen) return null;

            $total = $sessions->count();
            $priorDays = $sessions->where('scheduled_at', '<', Carbon::today())->count();
            $today = $sessions->filter(fn($s) => $s->scheduled_at->isToday())->count();
            $oldest = $sessions->sortBy('scheduled_at')->first();
            $oldestDate = $oldest ? $oldest->scheduled_at : null;
            $daysOverdue = $oldestDate && $oldestDate->isPast() ? $oldestDate->diffInDays(now()) : 0;

            $uniqueStudents = $sessions->map(fn($s) => $s->thesis?->student)->filter()->unique('id')->values();

            return [
                'dosen' => $dosen,
                'total_sessions' => $total,
                'prior_days_sessions' => $priorDays,
                'today_sessions' => $today,
                'overdue_sessions' => $total,
                'oldest_session_at' => $oldestDate,
                'days_overdue' => $daysOverdue,
                'students_count' => $uniqueStudents->count(),
                'students' => $uniqueStudents,
                'sessions' => $sessions,
            ];
        })->filter()->sortByDesc('total_sessions')->values();

        $uniqueDosenCount = $lecturersData->count();

        return [
            'scope' => $scope,
            'dosen_id' => $dosenId,
            'search' => $search,
            'global_uncompleted_count' => $globalUncompletedCount,
            'global_overdue_count' => $globalOverdueCount,
            'global_today_count' => $globalTodayCount,
            'filtered_sessions_count' => $filteredSessionsCount,
            'unique_dosen_count' => $uniqueDosenCount,
            'affected_students_count' => $affectedStudentsCount,
            'lecturers' => $lecturersData,
            'all_sessions' => $allSessions,
        ];
    }

    /**
     * Generate reminder message for uncompleted mentoring sessions of a lecturer.
     */
    public function generateUncompletedMentoringReminderMessage(User $dosen, $sessions): string
    {
        $userName = $dosen->name;
        $count = $sessions->count();
        
        $sessionList = $sessions->take(5)->map(function($s) {
            $student = $s->thesis?->student;
            $tgl = $s->scheduled_at->locale('id')->translatedFormat('d M Y, H:i');
            return "• {$student?->name} ({$student?->identifier}): \"{$s->topic}\" [Jadwal: {$tgl} WIB]";
        })->implode("\n");

        if ($count > 5) {
            $sessionList .= "\n• ...dan " . ($count - 5) . " sesi lainnya.";
        }

        $link = route('mentoring-sessions.index');

        return "Yth. Bpk/Ibu *{$userName}*,\n\nSalam takzim dari Program Studi FASILKOM UNSUB.\n\nBerdasarkan pantauan sistem SIBIMA, tercatat terdapat *{$count} sesi bimbingan* mahasiswa yang statusnya belum diselesaikan atau belum diinput catatan/feedback hasil bimbingan:\n\n{$sessionList}\n\nMohon kesediaan Bpk/Ibu untuk memperbarui status dan menginput hasil bimbingan agar mahasiswa dapat segera menindaklanjuti proses skripsi mereka:\n{$link}\n\nTerima kasih banyak atas perhatian dan kerja sama Bpk/Ibu.\n_Program Studi FASILKOM UNSUB_";
    }
}
