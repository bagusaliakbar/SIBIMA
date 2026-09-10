<?php

namespace App\Services;

use App\Models\ThesisDefenseScheduleDetail;
use App\Models\Wave;
use App\Models\User;

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
}
