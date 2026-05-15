<?php

namespace App\Services;

use App\Models\Thesis;
use App\Models\MentoringSession;
use App\Models\SeminarApplication;
use App\Models\ThesisDefenseApplication;
use App\Models\SeminarScheduleDetail;
use App\Models\ThesisDefenseScheduleDetail;
use App\Models\ThesisDefenseRevision;
use App\Models\Announcement;
use App\Models\User;
use App\Models\Wave;
use Illuminate\Support\Facades\Auth;

class DashboardService
{
    public function getStudentData($user)
    {
        $data = [
            'thesis' => null,
            'upcomingSessions' => collect(),
            'pastSessionsCount' => 0,
            'pastSessionsCountP1' => 0,
            'pastSessionsCountP2' => 0,
            'recentLogbooks' => collect(),
            'seminar' => null,
            'defense' => null,
            'mySeminarSchedule' => null,
            'myDefenseSchedule' => null,
            'daysSinceLastSession' => null,
            'isStale' => false,
            'progress' => $this->calculateStudentProgress(null, 0, null, null),
        ];

        $data['thesis'] = Thesis::with(['pembimbing1', 'pembimbing2'])
            ->where('student_id', $user->id)->first();

        if ($data['thesis']) {
            $thesis = $data['thesis'];
            $data['upcomingSessions'] = MentoringSession::where('thesis_id', $thesis->id)
                ->where('scheduled_at', '>=', now())
                ->whereIn('status', ['pending', 'approved'])
                ->orderBy('scheduled_at', 'asc')
                ->take(5)->get();

            $data['pastSessionsCount'] = MentoringSession::where('thesis_id', $thesis->id)
                ->where('status', 'completed')->count();

            $data['pastSessionsCountP1'] = MentoringSession::where('thesis_id', $thesis->id)
                ->where('dosen_id', $thesis->pembimbing1_id)
                ->where('status', 'completed')->count();

            $data['pastSessionsCountP2'] = MentoringSession::where('thesis_id', $thesis->id)
                ->where('dosen_id', $thesis->pembimbing2_id)
                ->where('status', 'completed')->count();

            $data['recentLogbooks'] = MentoringSession::where('thesis_id', $thesis->id)
                ->whereNotNull('notes')->where('status', 'completed')
                ->orderBy('scheduled_at', 'desc')->take(5)->get();

            $data['seminar'] = SeminarApplication::where('thesis_id', $thesis->id)->first();
            $data['defense'] = ThesisDefenseApplication::where('thesis_id', $thesis->id)->first();

            $data['mySeminarSchedule'] = SeminarScheduleDetail::with(['schedule', 'examiner1', 'examiner2'])
                ->where('thesis_id', $thesis->id)->first();
            
            $data['myDefenseSchedule'] = ThesisDefenseScheduleDetail::with(['schedule', 'examiner1', 'examiner2'])
                ->where('thesis_id', $thesis->id)->first();

            $lastSession = MentoringSession::where('thesis_id', $thesis->id)
                ->where('status', 'completed')->orderBy('scheduled_at', 'desc')->first();
            
            $data['daysSinceLastSession'] = $lastSession ? (int)now()->diffInDays($lastSession->scheduled_at) : null;
            $data['isStale'] = $data['daysSinceLastSession'] !== null && $data['daysSinceLastSession'] >= 14;

            // Calculate Progress Data
            $data['progress'] = $this->calculateStudentProgress($thesis, $data['pastSessionsCount'], $data['seminar'], $data['defense']);

            // Auto-sync Graduation
            if ($thesis->status !== 'completed' && $data['progress']['isGraduated']) {
                $thesis->update(['status' => 'completed']);
            }
        }

        return $data;
    }

    public function calculateStudentProgress($thesis, $pastSessionsCount, $seminar, $defense)
    {
        $isGraduated = $thesis && $thesis->status === 'completed';
        $progressPercent = 0;
        $seminarDone = false;
        $defenseDone = false;
        $currentStage = 0; // 0: No Thesis, 1: Judul, 2: Bimbingan, 3: Seminar, 4: Penelitian, 5: Sidang, 6: Yudisium

        if ($thesis) {
            $currentStage = 1; // Judul
            $progressPercent = 15;

            // Step 2: Bimbingan (Minimal 4 kali)
            $mentoring1 = min(4, $pastSessionsCount);
            if ($pastSessionsCount > 0) {
                $currentStage = 2;
                $progressPercent += ($mentoring1 / 4) * 20;
            }

            if ($mentoring1 >= 4) {
                // Step 3: Seminar
                $seminarDone = ($seminar && in_array($seminar->status, ['approved', 'completed', 'finished']));
                if ($seminarDone) {
                    $currentStage = 3;
                    $progressPercent = 55;

                    // Step 4: Penelitian (Bimbingan setelah seminar)
                    $mentoring2 = max(0, min(4, $pastSessionsCount - 4));
                    if ($mentoring2 > 0) {
                        $currentStage = 4;
                        $progressPercent += ($mentoring2 / 4) * 20;
                    }

                    if ($mentoring2 >= 4) {
                        // Step 5: Sidang
                        $hasDefenseRevisions = ThesisDefenseRevision::whereHas('detail', function ($q) use ($thesis) {
                            $q->where('thesis_id', $thesis->id);
                        })->exists();

                        $defenseDone = ($defense && in_array($defense->status, ['approved', 'completed', 'finished'])) || $hasDefenseRevisions;
                        if ($defenseDone) {
                            $currentStage = 5;
                            $progressPercent = 90;

                            // Step 6: Yudisium / Kelulusan
                            if ($isGraduated) {
                                $currentStage = 6;
                                $progressPercent = 100;
                            }
                        }
                    }
                }
            }
        }

        return [
            'percent' => $progressPercent,
            'isGraduated' => $isGraduated,
            'seminarDone' => $seminarDone,
            'defenseDone' => $defenseDone,
            'currentStage' => $currentStage,
            'stages' => [
                ['name' => 'Judul', 'desc' => 'Pengajuan judul skripsi'],
                ['name' => 'Bimbingan', 'desc' => 'Proses bimbingan Bab 1-3'],
                ['name' => 'Seminar', 'desc' => 'Seminar proposal/hasil'],
                ['name' => 'Penelitian', 'desc' => 'Pengolahan data & bimbingan Bab 4-5'],
                ['name' => 'Sidang', 'desc' => 'Ujian sidang akhir skripsi'],
                ['name' => 'Yudisium', 'desc' => 'Pernyataan kelulusan final']
            ]
        ];
    }

    public function getDosenData($user)
    {
        $dosenId = $user->id;
        $data = [];

        $data['examinerSeminarSchedules'] = SeminarScheduleDetail::with(['schedule', 'thesis.student'])
            ->where(function($q) use ($dosenId) {
                $q->where('examiner1_id', $dosenId)->orWhere('examiner2_id', $dosenId)
                ->orWhereHas('thesis', fn($sq) => $sq->where('pembimbing1_id', $dosenId)->orWhere('pembimbing2_id', $dosenId));
            })
            ->whereHas('schedule', fn($q) => $q->where('date', '>=', now()->toDateString()))
            ->orderBy('start_time', 'asc')->get();

        $data['examinerDefenseSchedules'] = ThesisDefenseScheduleDetail::with(['schedule', 'thesis.student'])
            ->where(function($q) use ($dosenId) {
                $q->where('examiner1_id', $dosenId)->orWhere('examiner2_id', $dosenId)
                ->orWhereHas('thesis', fn($sq) => $sq->where('pembimbing1_id', $dosenId)->orWhere('pembimbing2_id', $dosenId));
            })
            ->whereHas('schedule', fn($q) => $q->where('date', '>=', now()->toDateString()))
            ->orderBy('start_time', 'asc')->get();

        $activeTheses = Thesis::where('status', 'active')
            ->where(fn($q) => $q->where('pembimbing1_id', $dosenId)->orWhere('pembimbing2_id', $dosenId))->get();

        $data['activeThesesCount'] = $activeTheses->count();
        $data['totalActiveStudentsP1'] = $activeTheses->where('pembimbing1_id', $dosenId)->count();
        $data['totalActiveStudentsP2'] = $activeTheses->where('pembimbing2_id', $dosenId)->count();

        $dosenThesisIds = $activeTheses->pluck('id');
        $sessionsThisWeekQuery = MentoringSession::whereIn('thesis_id', $dosenThesisIds)
            ->whereBetween('scheduled_at', [now()->startOfWeek(), now()->endOfWeek()]);

        $data['sessionsThisWeek'] = (clone $sessionsThisWeekQuery)->count();
        $data['pendingSessionsThisWeek'] = (clone $sessionsThisWeekQuery)->where('status', 'pending')->count();
        $data['approvedSessionsThisWeek'] = (clone $sessionsThisWeekQuery)->where('status', 'approved')->count();

        $data['totalCompletedSessions'] = MentoringSession::whereIn('thesis_id', $dosenThesisIds)
            ->where('status', 'completed')->count();

        $data['upcomingSessions'] = MentoringSession::whereIn('thesis_id', $dosenThesisIds)
            ->where('scheduled_at', '>=', now())->whereIn('status', ['pending', 'approved'])
            ->orderBy('scheduled_at', 'asc')->take(5)->get();

        $data['recentLogbooks'] = MentoringSession::whereIn('thesis_id', $dosenThesisIds)
            ->whereNotNull('notes')->where('status', 'completed')
            ->orderBy('scheduled_at', 'desc')->take(5)->get();

        // Average Progress
        $totalProgressSum = 0;
        foreach ($activeTheses as $t) {
            $p = 25; // Judul
            $comp = MentoringSession::where('thesis_id', $t->id)->where('status', 'completed')->count();
            $p += min(25, ($comp / 8) * 25);
            $sem = SeminarApplication::where('thesis_id', $t->id)->first();
            if ($sem && $sem->status === 'approved') $p += 25;
            if ($t->acc_sidang_p1 && $t->acc_sidang_p2) $p += 25;
            $totalProgressSum += $p;
        }
        $data['averageStudentProgress'] = $data['activeThesesCount'] > 0 ? round($totalProgressSum / $data['activeThesesCount']) : 0;

        $data['studentProgressDistribution'] = [
            'Judul' => $data['activeThesesCount'],
            'Bimbingan' => MentoringSession::whereIn('thesis_id', $dosenThesisIds)->where('status', 'completed')->distinct('thesis_id')->count(),
            'ACC Seminar' => Thesis::whereIn('id', $dosenThesisIds)->where('acc_up_p1', true)->where('acc_up_p2', true)->count(),
            'ACC Sidang' => Thesis::whereIn('id', $dosenThesisIds)->where('acc_sidang_p1', true)->where('acc_sidang_p2', true)->count(),
        ];

        $data['monthlyMentoringCounts'] = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $data['monthlyMentoringCounts'][$month->format('M')] = MentoringSession::whereIn('thesis_id', $dosenThesisIds)
                ->where('status', 'completed')->whereYear('scheduled_at', $month->year)->whereMonth('scheduled_at', $month->month)->count();
        }

        return $data;
    }

    public function getAdminData()
    {
        $data = [];
        $data['activeThesesCount'] = Thesis::where('status', 'active')->count();
        $data['sessionsThisWeek'] = MentoringSession::whereBetween('scheduled_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $data['upcomingSessions'] = MentoringSession::where('scheduled_at', '>=', now())
            ->whereIn('status', ['pending', 'approved'])->orderBy('scheduled_at', 'asc')->take(5)->get();
        $data['recentLogbooks'] = MentoringSession::whereNotNull('notes')->where('status', 'completed')
            ->orderBy('scheduled_at', 'desc')->take(5)->get();

        $data['thesisStatusCounts'] = [
            'active' => Thesis::where('status', 'active')->count(),
            'completed' => Thesis::where('status', 'completed')->count(),
            'pending' => Thesis::where('status', 'pending')->count(),
        ];

        $data['monthlyMentoringCounts'] = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $data['monthlyMentoringCounts'][$month->format('M')] = MentoringSession::where('status', 'completed')
                ->whereYear('scheduled_at', $month->year)->whereMonth('scheduled_at', $month->month)->count();
        }

        $p1Counts = Thesis::where('status', 'active')->groupBy('pembimbing1_id')->selectRaw('pembimbing1_id, count(*) as total')->pluck('total', 'pembimbing1_id');
        $p2Counts = Thesis::where('status', 'active')->groupBy('pembimbing2_id')->selectRaw('pembimbing2_id, count(*) as total')->pluck('total', 'pembimbing2_id');
        
        $dosens = User::where('role', 'dosen')->where(function($q) use ($p1Counts, $p2Counts) {
            $q->whereIn('id', $p1Counts->keys())->orWhereIn('id', $p2Counts->keys());
        })->get();

        $data['dosenWorkload'] = [];
        foreach ($dosens as $d) {
            $count = ($p1Counts[$d->id] ?? 0) + ($p2Counts[$d->id] ?? 0);
            if ($count > 0) $data['dosenWorkload'][$d->name] = $count;
        }
        arsort($data['dosenWorkload']);
        $data['dosenWorkload'] = array_slice($data['dosenWorkload'], 0, 10, true);

        // Graduation Stats
        $onTime = 0; $late = 0;
        $completed = Thesis::where('status', 'completed')->with('student')->get();
        foreach ($completed as $t) {
            if ($t->student && $t->student->entry_year) {
                ($t->updated_at->year - $t->student->entry_year <= 4) ? $onTime++ : $late++;
            }
        }
        $data['onTimeStats'] = ['Tepat Waktu' => $onTime, 'Terlambat' => $late];

        $criticalThresholdYear = now()->year - 6;
        $data['studentHealthStats'] = [
            'Normal' => User::where('role', 'mahasiswa')->where('entry_year', '>', $criticalThresholdYear)->count(),
            'Kritis' => User::where('role', 'mahasiswa')->where('entry_year', '<=', $criticalThresholdYear)->whereHas('thesis', fn($q) => $q->where('status', '!=', 'completed'))->count(),
        ];

        $data['cohortCompletionData'] = [];
        $cohortYears = $completed->map(fn($t) => $t->student?->entry_year)->filter()->unique()->sort();
        foreach ($cohortYears as $year) {
            $thesesInCohort = $completed->filter(fn($t) => $t->student?->entry_year == $year);
            $count = $thesesInCohort->count();
            if ($count > 0) {
                $avg = $thesesInCohort->sum(fn($t) => $t->updated_at->year - $year) / $count;
                $data['cohortCompletionData']["Angkatan " . $year] = round($avg, 1);
            }
        }

        // 1. Average Thesis Duration per Wave
        $data['waveDurationStats'] = [];
        $waves = Wave::with(['defenseApplications.thesis' => function($q) {
            $q->where('status', 'completed');
        }])->get();

        foreach ($waves as $wave) {
            $completedTheses = $wave->defenseApplications->map(fn($app) => $app->thesis)->filter(fn($t) => $t && $t->status === 'completed');
            if ($completedTheses->count() > 0) {
                $totalDays = $completedTheses->sum(function($t) {
                    return $t->created_at->diffInDays($t->updated_at);
                });
                $avgMonths = round(($totalDays / $completedTheses->count()) / 30, 1);
                $data['waveDurationStats'][$wave->name] = $avgMonths;
            }
        }

        // 2. Score Distribution
        $data['scoreDistribution'] = ['A' => 0, 'B+' => 0, 'B' => 0, 'C+' => 0, 'C' => 0, 'D' => 0, 'E' => 0];
        $defenseDetails = ThesisDefenseScheduleDetail::whereHas('thesis', fn($q) => $q->where('status', 'completed'))->get();
        
        foreach ($defenseDetails as $detail) {
            $monitoringService = app(\App\Services\MonitoringService::class);
            $scores = $monitoringService->calculateDefenseScores($detail);
            $finalScore = $scores['finalScore'] ?? 0;

            if ($finalScore >= 80) $data['scoreDistribution']['A']++;
            elseif ($finalScore >= 75) $data['scoreDistribution']['B+']++;
            elseif ($finalScore >= 70) $data['scoreDistribution']['B']++;
            elseif ($finalScore >= 65) $data['scoreDistribution']['C+']++;
            elseif ($finalScore >= 60) $data['scoreDistribution']['C']++;
            elseif ($finalScore >= 50) $data['scoreDistribution']['D']++;
            else $data['scoreDistribution']['E']++;
        }

        return $data;
    }

    public function getCommonData()
    {
        return [
            'announcements' => Announcement::where('is_active', true)->orderBy('created_at', 'desc')->take(3)->get()
        ];
    }
}
