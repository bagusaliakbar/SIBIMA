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
use App\Models\Wave; use App\Services\ThesisAnalyticsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

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
                ->where('status', 'completed')->where('is_absent', false)->count();

            $data['pastSessionsCountP1'] = MentoringSession::where('thesis_id', $thesis->id)
                ->where('dosen_id', $thesis->pembimbing1_id)
                ->where('status', 'completed')->where('is_absent', false)->count();

            $data['pastSessionsCountP2'] = MentoringSession::where('thesis_id', $thesis->id)
                ->where('dosen_id', $thesis->pembimbing2_id)
                ->where('status', 'completed')->where('is_absent', false)->count();

            $data['recentLogbooks'] = MentoringSession::where('thesis_id', $thesis->id)
                ->whereNotNull('notes')->where('status', 'completed')
                ->orderBy('scheduled_at', 'desc')->paginate(5, ['*'], 'logbook_page');

            $data['seminar'] = SeminarApplication::where('thesis_id', $thesis->id)->first();
            $data['defense'] = ThesisDefenseApplication::where('thesis_id', $thesis->id)->first();
            $data['graduation'] = \App\Models\Graduation::where('thesis_id', $thesis->id)->first();

            $data['mySeminarSchedule'] = SeminarScheduleDetail::with(['schedule', 'examiner1', 'examiner2', 'thesis.pembimbing1', 'thesis.pembimbing2'])
                ->where('thesis_id', $thesis->id)->first();
            
            $data['myDefenseSchedule'] = ThesisDefenseScheduleDetail::with(['schedule', 'examiner1', 'examiner2', 'thesis.pembimbing1', 'thesis.pembimbing2'])
                ->where('thesis_id', $thesis->id)->first();

            $lastSession = MentoringSession::where('thesis_id', $thesis->id)
                ->where('status', 'completed')->orderBy('scheduled_at', 'desc')->first();
            
            $data['daysSinceLastSession'] = $lastSession ? abs((int)now()->diffInDays($lastSession->scheduled_at, true)) : ($thesis->created_at ? abs((int)now()->diffInDays($thesis->created_at, true)) : null);
            $data['isStale'] = $data['daysSinceLastSession'] !== null && $data['daysSinceLastSession'] > 7;
            $data['isCriticalStale'] = $data['daysSinceLastSession'] !== null && $data['daysSinceLastSession'] > 14;

            // Calculate Progress Data
            $data['progress'] = $this->calculateStudentProgress(
                $thesis, 
                $data['pastSessionsCount'], 
                $data['seminar'], 
                $data['defense'], 
                $data['mySeminarSchedule'], 
                $data['myDefenseSchedule'],
                $data['graduation']
            );

            // Auto-sync Graduation
            if ($thesis->status !== 'completed' && $data['progress']['isGraduated']) {
                $thesis->update(['status' => 'completed']);
                try {
                    \App\Models\ThesisRepository::archiveFromThesis($thesis);
                } catch (\Throwable $e) {
                    // Silently continue
                }
            }
        }

        /** @var array<string, mixed> $topicTrendsResult */
        $topicTrendsResult = [];
        $theses = Thesis::whereNotNull('topic')->with('student')->get();
        $cohorts = $theses->map(fn($t) => $t->student?->entry_year)->filter()->unique()->sort()->values();
        
        foreach ($cohorts as $year) {
            $cohortTheses = $theses->filter(fn($t) => $t->student?->entry_year == $year);
            $total = count($cohortTheses);
            if ($total > 0) {
                $topicTrends = $cohortTheses->groupBy('topic')->map(fn($g) => round((count($g) / $total) * 100, 1));
                $topicTrendsResult['Angkatan ' . $year] = $topicTrends;
            }
        }
        
        $data['topicTrends'] = $topicTrendsResult;
        
        return $data;
    }

    public function calculateStudentProgress($thesis, $pastSessionsCount, $seminar, $defense, $mySeminarSchedule = null, $myDefenseSchedule = null, $graduation = null)
    {
        if ($thesis && $graduation === null) {
            $graduation = \App\Models\Graduation::where('thesis_id', $thesis->id)->first();
        }

        $isGraduated = ($thesis && $thesis->status === 'completed') || ($graduation && $graduation->status === 'approved' && !empty($graduation->skl_number));
        $pastSessions = (int)($pastSessionsCount ?? 0);

        // Revisions Check
        $hasDefenseRevisions = false;
        $allDefenseRevisionsApproved = false;
        if ($thesis) {
            $hasDefenseRevisions = ThesisDefenseRevision::whereHas('detail', function ($q) use ($thesis) {
                $q->where('thesis_id', $thesis->id);
            })->exists();

            if ($hasDefenseRevisions) {
                $allDefenseRevisionsApproved = !ThesisDefenseRevision::whereHas('detail', function ($q) use ($thesis) {
                    $q->where('thesis_id', $thesis->id);
                })->where('status', '!=', 'approved')->exists();
            }
        }

        // Lazy load schedule details if not passed
        if ($thesis && $mySeminarSchedule === null) {
            $mySeminarSchedule = SeminarScheduleDetail::with('schedule')->where('thesis_id', $thesis->id)->first();
        }
        if ($thesis && $myDefenseSchedule === null) {
            $myDefenseSchedule = ThesisDefenseScheduleDetail::with('schedule')->where('thesis_id', $thesis->id)->first();
        }

        // Check if seminar schedule has finished
        $isSeminarScheduleFinished = false;
        if ($mySeminarSchedule) {
            $semDate = \Carbon\Carbon::parse($mySeminarSchedule->schedule->date ?? now())->format('Y-m-d');
            $semEnd = \Carbon\Carbon::parse($semDate . ' ' . \Carbon\Carbon::parse($mySeminarSchedule->end_time)->format('H:i:s'));
            $isSeminarScheduleFinished = $mySeminarSchedule->isGraded()
                || $mySeminarSchedule->isAllRevisionsApproved()
                || ($semEnd->isPast() && $mySeminarSchedule->revisions()->exists());
        }

        // Check if defense schedule has finished
        $isDefenseScheduleFinished = false;
        if ($myDefenseSchedule) {
            $defDate = \Carbon\Carbon::parse($myDefenseSchedule->schedule->date ?? now())->format('Y-m-d');
            $defEnd = \Carbon\Carbon::parse($defDate . ' ' . \Carbon\Carbon::parse($myDefenseSchedule->end_time)->format('H:i:s'));
            $isDefenseScheduleFinished = $myDefenseSchedule->isGraded()
                || ($defEnd->isPast() && $hasDefenseRevisions);
        }

        // 1. Stage 1 (1. Pengajuan Judul): Thesis exists and is active or completed
        $stage1Done = $thesis && in_array($thesis->status, ['active', 'completed']);

        // Check seminar revisions
        $hasSeminarRevisions = false;
        $allSeminarRevisionsApproved = false;
        if ($mySeminarSchedule) {
            $hasSeminarRevisions = $mySeminarSchedule->isRevisionStarted();
            $allSeminarRevisionsApproved = $mySeminarSchedule->isAllRevisionsApproved();
        }

        // Check if student reached sidang or beyond
        $hasReachedSidang = ($defense && in_array($defense->status, ['pending', 'approved', 'completed', 'finished']))
            || ($thesis && (($thesis->is_acc_sidang_p1 && $thesis->is_acc_sidang_p2) || $thesis->isAccSidangFinal()))
            || $myDefenseSchedule !== null
            || $hasDefenseRevisions
            || $isGraduated;

        // 2. Stage 2 (2. Pendaftaran Seminar):
        // Seminar application is approved / scheduled / completed, or has seminar schedule, or has reached beyond
        $hasSeminarScheduled = ($seminar && in_array($seminar->status, ['approved', 'completed', 'finished']))
            || $mySeminarSchedule !== null
            || $hasSeminarRevisions
            || $hasReachedSidang
            || $isGraduated;
        $stage2Done = $stage1Done && $hasSeminarScheduled;

        // 3. Stage 3 (3. Revisi Seminar):
        // Seminar revisions all approved OR student has progressed to sidang / graduated
        $stage3Done = $stage2Done && (
            $allSeminarRevisionsApproved 
            || (!$hasSeminarRevisions && $isSeminarScheduleFinished)
            || $hasReachedSidang 
            || $isGraduated
        );

        // 4. Stage 4 (4. Pendaftaran Sidang):
        // Defense application is approved / scheduled / completed, or has defense schedule, or defense revisions exist, or graduated
        $hasDefenseScheduled = ($defense && in_array($defense->status, ['approved', 'completed', 'finished']))
            || $myDefenseSchedule !== null
            || $hasDefenseRevisions
            || $isGraduated;
        $stage4Done = $stage3Done && $hasDefenseScheduled;

        // 5. Stage 5 (5. Revisi Sidang):
        // Defense exam passed AND (all revisions approved or no revisions needed and exam passed) or graduated
        $defenseExamPassed = ($defense && in_array($defense->status, ['completed', 'finished']))
            || $isDefenseScheduleFinished
            || $hasDefenseRevisions
            || $isGraduated;
        $defenseRevisionsDone = (!$hasDefenseRevisions && $defenseExamPassed) || ($hasDefenseRevisions && $allDefenseRevisionsApproved) || $isGraduated;
        $stage5Done = $stage4Done && $defenseExamPassed && $defenseRevisionsDone;

        // 6. Stage 6 (6. Yudisium & SKL):
        // Bebas Tanggungan approved & SKL official number issued, or graduated
        $stage6Done = ($graduation && $graduation->status === 'approved') || $isGraduated;

        // If graduated, all prior steps are completed
        if ($isGraduated) {
            $stage1Done = true;
            $stage2Done = true;
            $stage3Done = true;
            $stage4Done = true;
            $stage5Done = true;
            $stage6Done = true;
        }

        // Determine current active stage (1 to 6)
        if (!$stage1Done) {
            $currentStage = 1;
            $progressPercent = $thesis ? 10 : 0;
        } elseif (!$stage2Done) {
            $currentStage = 2;
            $progressPercent = 15 + min(15, ($pastSessions / 8) * 15) + ($seminar ? 5 : 0); // 15% - 35%
        } elseif (!$stage3Done) {
            $currentStage = 3;
            $progressPercent = 40 + ($allSeminarRevisionsApproved ? 10 : 5); // 40% - 50%
        } elseif (!$stage4Done) {
            $currentStage = 4;
            $progressPercent = 55 + min(15, ($pastSessions / 8) * 15) + ($defense ? 5 : 0); // 55% - 70%
        } elseif (!$stage5Done) {
            $currentStage = 5;
            $progressPercent = 75 + ($hasDefenseRevisions && $allDefenseRevisionsApproved ? 10 : 0); // 75% - 85%
        } elseif (!$stage6Done) {
            $currentStage = 6;
            $progressPercent = 90 + ($graduation ? 5 : 0); // 90% - 95%
        } else {
            $currentStage = 6;
            $progressPercent = 100;
        }

        // Determine Status Types ('completed', 'active', 'locked')
        $stage1Status = $stage1Done ? 'completed' : 'active';
        $stage2Status = $stage2Done ? 'completed' : ($stage1Done ? 'active' : 'locked');
        $stage3Status = $stage3Done ? 'completed' : ($stage2Done ? 'active' : 'locked');
        $stage4Status = $stage4Done ? 'completed' : ($stage3Done ? 'active' : 'locked');
        $stage5Status = $stage5Done ? 'completed' : ($stage4Done ? 'active' : 'locked');
        $stage6Status = $stage6Done ? 'completed' : ($stage5Done ? 'active' : 'locked');

        $stages = [
            [
                'number' => 1,
                'name' => '1. Pengajuan Judul',
                'short_name' => 'Pengajuan Judul',
                'desc' => 'Pengajuan judul & penetapan pembimbing skripsi',
                'url' => $thesis ? route('theses.index') : route('theses.create'),
                'status_type' => $stage1Status,
                'status_label' => $stage1Status === 'completed' ? 'Selesai' : 'Sedang Berjalan',
                'badge' => $stage1Done ? 'Disetujui' : ($thesis ? 'Menunggu ACC' : null),
                'is_completed' => $stage1Status === 'completed',
                'is_current' => $stage1Status === 'active',
                'is_locked' => false,
            ],
            [
                'number' => 2,
                'name' => '2. Pendaftaran Seminar',
                'short_name' => 'Pendaftaran Seminar',
                'desc' => 'Bimbingan berkala & pendaftaran jadwal Sempro',
                'url' => route('seminar-applications.index'),
                'status_type' => $stage2Status,
                'status_label' => $stage2Status === 'completed' ? 'Selesai' : ($stage2Status === 'active' ? 'Sedang Berjalan' : 'Terkunci'),
                'badge' => $stage2Status === 'completed' ? 'Terjadwal' : ($stage2Status === 'active' ? ($seminar ? ($seminar->status === 'approved' ? 'Terjadwal' : 'Diajukan') : ($pastSessions . '/8 Sesi')) : null),
                'is_completed' => $stage2Status === 'completed',
                'is_current' => $stage2Status === 'active',
                'is_locked' => $stage2Status === 'locked',
            ],
            [
                'number' => 3,
                'name' => '3. Revisi Seminar',
                'short_name' => 'Revisi Seminar',
                'desc' => 'Pelaksanaan ujian Sempro & approval revisi penguji',
                'url' => route('student-seminar-revisions.index'),
                'status_type' => $stage3Status,
                'status_label' => $stage3Status === 'completed' ? 'Selesai' : ($stage3Status === 'active' ? 'Sedang Berjalan' : 'Terkunci'),
                'badge' => $stage3Status === 'completed' ? 'Revisi Selesai' : ($stage3Status === 'active' ? ($allSeminarRevisionsApproved ? 'ACC' : 'Proses Revisi') : null),
                'is_completed' => $stage3Status === 'completed',
                'is_current' => $stage3Status === 'active',
                'is_locked' => $stage3Status === 'locked',
            ],
            [
                'number' => 4,
                'name' => '4. Pendaftaran Sidang',
                'short_name' => 'Pendaftaran Sidang',
                'desc' => 'Bimbingan Bab 4-5 & pendaftaran sidang skripsi',
                'url' => route('thesis-defense-applications.index'),
                'status_type' => $stage4Status,
                'status_label' => $stage4Status === 'completed' ? 'Selesai' : ($stage4Status === 'active' ? 'Sedang Berjalan' : 'Terkunci'),
                'badge' => $stage4Status === 'completed' ? 'Terjadwal' : ($stage4Status === 'active' ? ($defense ? ($defense->status === 'approved' ? 'Terjadwal' : 'Diajukan') : 'Bimbingan') : null),
                'is_completed' => $stage4Status === 'completed',
                'is_current' => $stage4Status === 'active',
                'is_locked' => $stage4Status === 'locked',
            ],
            [
                'number' => 5,
                'name' => '5. Revisi Sidang',
                'short_name' => 'Revisi Sidang',
                'desc' => 'Ujian sidang skripsi & approval revisi dosen penguji',
                'url' => route('student-defense-revisions.index'),
                'status_type' => $stage5Status,
                'status_label' => $stage5Status === 'completed' ? 'Selesai' : ($stage5Status === 'active' ? 'Sedang Berjalan' : 'Terkunci'),
                'badge' => $stage5Status === 'completed' ? 'Revisi Selesai' : ($stage5Status === 'active' ? ($hasDefenseRevisions ? ($allDefenseRevisionsApproved ? 'Revisi ACC' : 'Proses Revisi') : 'Persiapan') : null),
                'is_completed' => $stage5Status === 'completed',
                'is_current' => $stage5Status === 'active',
                'is_locked' => $stage5Status === 'locked',
            ],
            [
                'number' => 6,
                'name' => '6. Yudisium & SKL',
                'short_name' => 'Yudisium & SKL',
                'desc' => 'Verifikasi bebas tanggungan & Surat Keterangan Lulus',
                'url' => route('student.graduation'),
                'status_type' => $stage6Status,
                'status_label' => $stage6Status === 'completed' ? 'Selesai' : ($stage6Status === 'active' ? 'Sedang Berjalan' : 'Terkunci'),
                'badge' => $stage6Status === 'completed' ? 'SKL Terbit' : ($stage6Status === 'active' ? ($graduation ? ($graduation->status === 'approved' ? 'Lulus' : ($graduation->status === 'pending' ? 'Verifikasi' : 'Bebas Tanggungan')) : 'Bebas Tanggungan') : null),
                'is_completed' => $stage6Status === 'completed',
                'is_current' => $stage6Status === 'active',
                'is_locked' => $stage6Status === 'locked',
            ],
        ];

        return [
            'percent' => $progressPercent,
            'isGraduated' => $isGraduated,
            'seminarDone' => $stage3Done,
            'defenseDone' => $defenseExamPassed,
            'revisionDone' => $defenseRevisionsDone,
            'currentStage' => $currentStage,
            'stages' => $stages,
        ];
    }

    public function getDosenData($user)
    {
        $dosenId = $user->id;
        /** @var array<string, mixed> $data */
        $data = [];

        $activeWave = Wave::getCurrentActive();

        $seminarQuery = SeminarScheduleDetail::with(['schedule', 'thesis.student', 'revisions.messages'])
            ->where(function($q) use ($dosenId) {
                $q->where('examiner1_id', $dosenId)->orWhere('examiner2_id', $dosenId)
                ->orWhereHas('thesis', fn($sq) => $sq->where('pembimbing1_id', $dosenId)->orWhere('pembimbing2_id', $dosenId));
            });

        if ($activeWave) {
            $seminarQuery->where(function($q) use ($activeWave, $dosenId) {
                $q->whereHas('schedule', fn($sq) => $sq->where('wave_id', $activeWave->id))
                  ->orWhereHas('schedule', fn($sq) => $sq->where('date', '>=', now()->toDateString()))
                  ->orWhereHas('revisions', fn($rq) => $rq->where('examiner_id', $dosenId)->where('status', '!=', 'approved'));
            });
        }

        $data['examinerSeminarSchedules'] = $seminarQuery->orderBy('start_time', 'asc')->get()
            ->reject(function (SeminarScheduleDetail $detail) use ($dosenId) {
                $rev = $detail->getRevisionFor($dosenId);
                $semDate = \Carbon\Carbon::parse($detail->schedule->date)->format('Y-m-d');
                $semEnd = \Carbon\Carbon::parse($semDate . ' ' . \Carbon\Carbon::parse($detail->end_time)->format('H:i:s'));

                if ($semEnd->isFuture()) {
                    return false;
                }

                $needsGrade = !$detail->isGraded();
                $needsRev = $rev && $rev->status !== 'approved';

                if ($needsGrade || $needsRev) {
                    return false;
                }

                return true;
            });

        $defenseQuery = ThesisDefenseScheduleDetail::with(['schedule', 'thesis.student', 'revisions.messages'])
            ->where(function($q) use ($dosenId) {
                $q->where('examiner1_id', $dosenId)->orWhere('examiner2_id', $dosenId)
                ->orWhereHas('thesis', fn($sq) => $sq->where('pembimbing1_id', $dosenId)->orWhere('pembimbing2_id', $dosenId));
            });

        if ($activeWave) {
            $defenseQuery->where(function($q) use ($activeWave, $dosenId) {
                $q->whereHas('schedule', fn($sq) => $sq->where('wave_id', $activeWave->id))
                  ->orWhereHas('schedule', fn($sq) => $sq->where('date', '>=', now()->toDateString()))
                  ->orWhereHas('revisions', fn($rq) => $rq->where('examiner_id', $dosenId)->where('status', '!=', 'approved'));
            });
        }

        $data['examinerDefenseSchedules'] = $defenseQuery->orderBy('start_time', 'asc')->get()
            ->reject(function (ThesisDefenseScheduleDetail $detail) use ($dosenId) {
                $rev = $detail->getRevisionFor($dosenId);
                $defDate = \Carbon\Carbon::parse($detail->schedule->date)->format('Y-m-d');
                $defEnd = \Carbon\Carbon::parse($defDate . ' ' . \Carbon\Carbon::parse($detail->end_time)->format('H:i:s'));

                if ($defEnd->isFuture()) {
                    return false;
                }

                $needsGrade = !$detail->isGradingComplete();
                $needsRev = $rev && $rev->status !== 'approved';

                if ($needsGrade || $needsRev) {
                    return false;
                }

                return true;
            });

        $activeTheses = Thesis::where('status', '!=', 'completed')
            ->where('status', '!=', 'rejected')
            ->where(fn($q) => $q->where('pembimbing1_id', $dosenId)->orWhere('pembimbing2_id', $dosenId))
            ->with(['student', 'mentoringSessions' => function($q) {
                $q->where('status', 'completed')->where('is_absent', false)->orderBy('scheduled_at', 'desc');
            }])
            ->get();

        $stalledIds = [];
        foreach ($activeTheses as $t) {
            $completedCount = $t->mentoringSessions ? $t->mentoringSessions->count() : 0;
            if ($completedCount === 0) {
                $stalledIds[] = $t->id;
            } else {
                $lastSession = $t->mentoringSessions->first();
                if ($lastSession && $lastSession->scheduled_at) {
                    $daysSince = (int) \Carbon\Carbon::parse($lastSession->scheduled_at)->startOfDay()->diffInDays(now()->startOfDay());
                    if ($daysSince > 14) {
                        $stalledIds[] = $t->id;
                    }
                }
            }
        }
        $stalledCount = count($stalledIds);
        $data['stalledThesesCount'] = $stalledCount;
        $data['stalledCount'] = $stalledCount;

        $data['activeThesesCount'] = $activeTheses->count();
        $data['totalActiveStudentsP1'] = $activeTheses->where('pembimbing1_id', $dosenId)->count();
        $data['totalActiveStudentsP2'] = $activeTheses->where('pembimbing2_id', $dosenId)->count();

        $dosenThesisIds = $activeTheses->pluck('id');
        $sessionsThisWeekQuery = MentoringSession::whereIn('thesis_id', $dosenThesisIds)
            ->where('dosen_id', $dosenId)
            ->whereBetween('scheduled_at', [now()->startOfWeek(), now()->endOfWeek()]);

        $data['sessionsThisWeek'] = (clone $sessionsThisWeekQuery)->count();
        $data['pendingSessionsThisWeek'] = (clone $sessionsThisWeekQuery)->where('status', 'pending')->count();
        $data['approvedSessionsThisWeek'] = (clone $sessionsThisWeekQuery)->where('status', 'approved')->count();

        $data['totalCompletedSessions'] = MentoringSession::whereIn('thesis_id', $dosenThesisIds)
            ->where('dosen_id', $dosenId)
            ->where('status', 'completed')->where('is_absent', false)->count();

        $data['upcomingSessions'] = MentoringSession::whereIn('thesis_id', $dosenThesisIds)
            ->where('dosen_id', $dosenId)
            ->where('scheduled_at', '>=', now())->whereIn('status', ['pending', 'approved'])
            ->orderBy('scheduled_at', 'asc')->take(5)->get();

        $data['recentLogbooks'] = MentoringSession::whereIn('thesis_id', $dosenThesisIds)
            ->where('dosen_id', $dosenId)
            ->whereNotNull('notes')->where('status', 'completed')
            ->orderBy('scheduled_at', 'desc')->paginate(5, ['*'], 'logbook_page');

        // Average Progress
        $totalProgressSum = 0;
        foreach ($activeTheses as $t) {
            $comp = MentoringSession::where('thesis_id', $t->id)->where('status', 'completed')->where('is_absent', false)->count();
            $sem = SeminarApplication::where('thesis_id', $t->id)->first();
            $def = ThesisDefenseApplication::where('thesis_id', $t->id)->first();
            
            $prog = $this->calculateStudentProgress($t, $comp, $sem, $def);
            $totalProgressSum += $prog['percent'];
        }
        $data['averageStudentProgress'] = $data['activeThesesCount'] > 0 ? round($totalProgressSum / $data['activeThesesCount']) : 0;

        $judulCount = 0;
        $bimbinganCount = 0;
        $accSeminarCount = 0;
        $accSidangCount = 0;

        foreach ($activeTheses as $t) {
            if ($t->acc_sidang_p1 && $t->acc_sidang_p2) {
                $accSidangCount++;
            } elseif ($t->acc_up_p1 && $t->acc_up_p2) {
                $accSeminarCount++;
            } elseif (MentoringSession::where('thesis_id', $t->id)->where('status', 'completed')->where('is_absent', false)->exists()) {
                $bimbinganCount++;
            } else {
                $judulCount++;
            }
        }

        $data['studentProgressDistribution'] = [
            'Judul' => $judulCount,
            'Bimbingan' => $bimbinganCount,
            'ACC Seminar' => $accSeminarCount,
            'ACC Sidang' => $accSidangCount,
        ];

        $monthlyMentoringCounts = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyMentoringCounts[$month->format('M')] = MentoringSession::whereIn('thesis_id', $dosenThesisIds)
                ->where('dosen_id', $dosenId)
                ->where('status', 'completed')->where('is_absent', false)->whereYear('scheduled_at', $month->year)->whereMonth('scheduled_at', $month->month)->count();
        }
        $data['monthlyMentoringCounts'] = $monthlyMentoringCounts;

        /** @var array<string, mixed> $topicTrendsResult */
        $topicTrendsResult = [];
        $theses = Thesis::whereNotNull('topic')->with('student')->get();
        $cohorts = $theses->map(fn($t) => $t->student?->entry_year)->filter()->unique()->sort()->values();
        
        foreach ($cohorts as $year) {
            $cohortTheses = $theses->filter(fn($t) => $t->student?->entry_year == $year);
            $total = count($cohortTheses);
            if ($total > 0) {
                $topicTrends = $cohortTheses->groupBy('topic')->map(fn($g) => round((count($g) / $total) * 100, 1));
                $topicTrendsResult['Angkatan ' . $year] = $topicTrends;
            }
        }
        
        // Early Warning System: Inactive Mentoring Students (> 7 days)
        $inactiveThesesList = [];
        $dosenWarningCount = 0;
        $dosenCriticalCount = 0;

        foreach ($activeTheses as $t) {
            $days = $t->getDaysSinceLastMentoringForDosen($dosenId);
            if ($days !== null && $days > 7 && !$t->isAccSidangFinal()) {
                $status = $days > 14 ? 'critical' : 'warning';
                if ($status === 'critical') {
                    $dosenCriticalCount++;
                } else {
                    $dosenWarningCount++;
                }

                $student = $t->student;
                $phone = $student ? preg_replace('/[^0-9]/', '', $student->phone_number ?? '') : '';
                if (str_starts_with($phone, '0')) {
                    $phone = '62' . substr($phone, 1);
                }

                $roleText = $t->pembimbing1_id == $dosenId ? 'Pembimbing 1' : 'Pembimbing 2';
                $waMsg = "Halo {$student->name} ({$student->identifier}), saya {$user->name} sebagai {$roleText} skripsi Anda di SIBIMA. Menurut catatan sistem, Anda belum melakukan bimbingan selama {$days} hari. Sesuai standar prodi, mahasiswa wajib bimbingan minimal 1x per minggu. Silakan segera jadwalkan sesi bimbingan berikutnya.";

                $inactiveThesesList[] = [
                    'thesis_id' => $t->id,
                    'student_id' => $student->id ?? null,
                    'student_name' => $student->name ?? '-',
                    'student_npm' => $student->identifier ?? '-',
                    'title' => $t->display_title,
                    'role_text' => $roleText,
                    'days_inactive' => $days,
                    'status' => $status,
                    'phone' => $phone,
                    'wa_link' => $phone ? "https://wa.me/{$phone}?text=" . urlencode($waMsg) : null,
                ];
            }
        }

        usort($inactiveThesesList, fn($a, $b) => $b['days_inactive'] <=> $a['days_inactive']);

        $data['earlyWarning'] = [
            'total_inactive' => count($inactiveThesesList),
            'stalled_count' => $stalledCount,
            'warning_count' => $dosenWarningCount,
            'critical_count' => $dosenCriticalCount,
            'students' => $inactiveThesesList,
        ];

        $data['topicTrends'] = $topicTrendsResult;

        // Actionable Task Center for Dosen
        $pendingMentoringCount = MentoringSession::where('dosen_id', $dosenId)->where('status', 'pending')->count();
        $needInputList = [];
        $needReviewList = [];

        // 1. Seminar Exam Details as Examiner
        $semDetails = SeminarScheduleDetail::with(['schedule', 'thesis.student', 'revisions.messages'])
            ->where(function($q) use ($dosenId) {
                $q->where('examiner1_id', $dosenId)->orWhere('examiner2_id', $dosenId);
            })->get();

        foreach ($semDetails as $detail) {
            $student = $detail->thesis?->student;
            if (!$student) continue;
            $semDate = \Carbon\Carbon::parse($detail->schedule->date ?? now())->format('Y-m-d');
            $semEnd = \Carbon\Carbon::parse($semDate . ' ' . \Carbon\Carbon::parse($detail->end_time)->format('H:i:s'));
            $rev = $detail->revisions->where('examiner_id', $dosenId)->first();
            if ($rev && $rev->status === 'approved') continue;

            $hasNotes = $rev && (!empty(trim($rev->revision_notes ?? '')) || !empty($rev->revision_file) || $rev->messages->where('sender_id', $dosenId)->isNotEmpty());

            if ($semEnd->isPast() && !$hasNotes && !$detail->isGraded()) {
                $needInputList[] = [
                    'type' => 'seminar',
                    'category' => 'Seminar Proposal',
                    'title' => 'Input Catatan & Nilai Seminar',
                    'student_name' => $student->name,
                    'student_identifier' => $student->identifier,
                    'thesis_title' => $detail->thesis?->title ?? '-',
                    'detail_id' => $detail->id,
                    'url' => route('seminar-examiner.show', $detail->id),
                    'date' => $detail->schedule->date,
                    'time' => \Carbon\Carbon::parse($detail->start_time)->format('H:i') . ' - ' . \Carbon\Carbon::parse($detail->end_time)->format('H:i') . ' WIB',
                    'location' => $detail->schedule->location ?? '-',
                ];
            } elseif ($rev) {
                $lastMsg = $rev->messages->sortBy('created_at')->last();
                $isResubmitted = ($rev->status === 'resubmitted') || ($lastMsg && $lastMsg->sender_id === $student->id);
                if ($isResubmitted) {
                    $needReviewList[] = [
                        'type' => 'seminar',
                        'category' => 'Seminar Proposal',
                        'title' => 'Tinjau Revisi Seminar',
                        'student_name' => $student->name,
                        'student_identifier' => $student->identifier,
                        'thesis_title' => $detail->thesis?->title ?? '-',
                        'detail_id' => $detail->id,
                        'url' => route('seminar-examiner.show', $detail->id),
                        'date' => $rev->resubmitted_at ?? $rev->updated_at,
                        'time' => \Carbon\Carbon::parse($detail->start_time)->format('H:i') . ' WIB',
                        'location' => $detail->schedule->location ?? '-',
                    ];
                }
            }
        }

        // 2. Defense Exam Details as Examiner
        $defDetails = ThesisDefenseScheduleDetail::with(['schedule', 'thesis.student', 'revisions.messages'])
            ->where(function($q) use ($dosenId) {
                $q->where('examiner1_id', $dosenId)->orWhere('examiner2_id', $dosenId);
            })->get();

        foreach ($defDetails as $detail) {
            $student = $detail->thesis?->student;
            if (!$student) continue;
            $defDate = \Carbon\Carbon::parse($detail->schedule->date ?? now())->format('Y-m-d');
            $defEnd = \Carbon\Carbon::parse($defDate . ' ' . \Carbon\Carbon::parse($detail->end_time)->format('H:i:s'));
            $rev = $detail->revisions->where('examiner_id', $dosenId)->first();
            if ($rev && $rev->status === 'approved') continue;

            $hasNotes = $rev && (!empty(trim($rev->revision_notes ?? '')) || !empty($rev->revision_file) || $rev->messages->where('sender_id', $dosenId)->isNotEmpty());

            if ($defEnd->isPast() && !$hasNotes && !$detail->isGradingComplete()) {
                $needInputList[] = [
                    'type' => 'defense',
                    'category' => 'Sidang Skripsi',
                    'title' => 'Input Catatan & Nilai Sidang',
                    'student_name' => $student->name,
                    'student_identifier' => $student->identifier,
                    'thesis_title' => $detail->thesis?->title ?? '-',
                    'detail_id' => $detail->id,
                    'url' => route('defense-examiner.show', $detail->id),
                    'date' => $detail->schedule->date,
                    'time' => \Carbon\Carbon::parse($detail->start_time)->format('H:i') . ' - ' . \Carbon\Carbon::parse($detail->end_time)->format('H:i') . ' WIB',
                    'location' => $detail->schedule->location ?? '-',
                ];
            } elseif ($rev) {
                $lastMsg = $rev->messages->sortBy('created_at')->last();
                $isResubmitted = ($rev->status === 'resubmitted') || ($lastMsg && $lastMsg->sender_id === $student->id);
                if ($isResubmitted) {
                    $needReviewList[] = [
                        'type' => 'defense',
                        'category' => 'Sidang Skripsi',
                        'title' => 'Tinjau Revisi Sidang',
                        'student_name' => $student->name,
                        'student_identifier' => $student->identifier,
                        'thesis_title' => $detail->thesis?->title ?? '-',
                        'detail_id' => $detail->id,
                        'url' => route('defense-examiner.show', $detail->id),
                        'date' => $rev->resubmitted_at ?? $rev->updated_at,
                        'time' => \Carbon\Carbon::parse($detail->start_time)->format('H:i') . ' WIB',
                        'location' => $detail->schedule->location ?? '-',
                    ];
                }
            }
        }

        $data['dosenActionCenter'] = [
            'pending_mentoring_count' => $pendingMentoringCount,
            'need_input_revisions' => $needInputList,
            'need_input_count' => count($needInputList),
            'need_review_revisions' => $needReviewList,
            'need_review_count' => count($needReviewList),
            'total_actions' => $pendingMentoringCount + count($needInputList) + count($needReviewList),
            'total_action_needed' => $pendingMentoringCount + count($needInputList) + count($needReviewList),
        ];

        // Active Wave Info for Dosen
        $activeWaveInfo = null;
        if ($activeWave) {
            $endDate = \Carbon\Carbon::parse($activeWave->end_date)->endOfDay();
            $daysRemaining = (int)now()->startOfDay()->diffInDays($endDate, false);
            $semCount = SeminarApplication::where('wave_id', $activeWave->id)->count();
            $defCount = ThesisDefenseApplication::where('wave_id', $activeWave->id)->count();
            $activeWaveInfo = [
                'id' => $activeWave->id,
                'name' => $activeWave->name,
                'academic_year' => $activeWave->academic_year,
                'semester' => $activeWave->semester,
                'start_date' => $activeWave->start_date,
                'end_date' => $activeWave->end_date,
                'days_remaining' => $daysRemaining,
                'is_active' => $activeWave->is_active,
                'is_expired' => $endDate->isPast(),
                'seminar_count' => $semCount,
                'seminar_applicant_count' => $semCount,
                'defense_count' => $defCount,
                'defense_applicant_count' => $defCount,
                'total_applicant_count' => $semCount + $defCount,
            ];
        }
        $data['activeWaveInfo'] = $activeWaveInfo;
        
        return $data;
    }

    public function getAdminData()
    {
        /** @var array<string, mixed> $data */
        $data = [];
        $data['activeThesesCount'] = Thesis::where('status', 'active')->count();
        $data['sessionsThisWeek'] = MentoringSession::whereBetween('scheduled_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $data['upcomingSessions'] = MentoringSession::where('scheduled_at', '>=', now())
            ->whereIn('status', ['pending', 'approved'])->orderBy('scheduled_at', 'asc')->take(5)->get();
        $data['recentLogbooks'] = MentoringSession::whereNotNull('notes')->where('status', 'completed')
            ->orderBy('scheduled_at', 'desc')->paginate(5, ['*'], 'logbook_page');

        $data['totalCompletedSessions'] = MentoringSession::where('status', 'completed')->where('is_absent', false)->count();

        $activeTheses = Thesis::where('status', 'active')->get();
        $totalProgressSum = 0;
        foreach ($activeTheses as $t) {
            $comp = MentoringSession::where('thesis_id', $t->id)->where('status', 'completed')->where('is_absent', false)->count();
            $sem = SeminarApplication::where('thesis_id', $t->id)->first();
            $def = ThesisDefenseApplication::where('thesis_id', $t->id)->first();
            
            $prog = $this->calculateStudentProgress($t, $comp, $sem, $def);
            $totalProgressSum += $prog['percent'];
        }
        $data['averageStudentProgress'] = $data['activeThesesCount'] > 0 ? round($totalProgressSum / $data['activeThesesCount']) : 0;

        $data['thesisStatusCounts'] = [
            'active' => Thesis::where('status', 'active')->count(),
            'completed' => Thesis::where('status', 'completed')->count(),
            'pending' => Thesis::where('status', 'pending')->count(),
        ];

        $monthlyMentoringCounts = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyMentoringCounts[$month->format('M')] = MentoringSession::where('status', 'completed')
                ->where('is_absent', false)
                ->whereYear('scheduled_at', $month->year)->whereMonth('scheduled_at', $month->month)->count();
        }
        $data['monthlyMentoringCounts'] = $monthlyMentoringCounts;

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

        $cohortCompletionData = [];
        $cohortYears = $completed->map(fn($t) => $t->student?->entry_year)->filter()->unique()->sort();
        foreach ($cohortYears as $year) {
            $thesesInCohort = $completed->filter(fn($t) => $t->student?->entry_year == $year);
            $count = $thesesInCohort->count();
            if ($count > 0) {
                $avg = $thesesInCohort->sum(fn($t) => $t->updated_at->year - $year) / $count;
                $cohortCompletionData["Angkatan " . $year] = round($avg, 1);
            }
        }
        $data['cohortCompletionData'] = $cohortCompletionData;

        // 1. Average Thesis Duration per Wave
        $waveDurationStats = [];
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
                $waveDurationStats[$wave->name] = $avgMonths;
            }
        }
        $data['waveDurationStats'] = $waveDurationStats;

        // 2. Score Distribution
        $data['scoreDistribution'] = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0];
        $defenseDetails = ThesisDefenseScheduleDetail::whereHas('thesis', fn($q) => $q->where('status', 'completed'))->get();
        
        foreach ($defenseDetails as $detail) {
            $monitoringService = app(\App\Services\MonitoringService::class);
            $scores = $monitoringService->calculateDefenseScores($detail);
            $finalScore = $scores['finalScore'] ?? 0;

            if ($finalScore >= 80) $data['scoreDistribution']['A']++;
            elseif ($finalScore >= 70) $data['scoreDistribution']['B']++;
            elseif ($finalScore >= 60) $data['scoreDistribution']['C']++;
            elseif ($finalScore >= 50) $data['scoreDistribution']['D']++;
            else $data['scoreDistribution']['E']++;
        }

        /** @var array<string, mixed> $topicTrendsResult */
        $topicTrendsResult = [];
        $theses = Thesis::whereNotNull('topic')->with('student')->get();
        $cohorts = $theses->map(fn($t) => $t->student?->entry_year)->filter()->unique()->sort()->values();
        
        foreach ($cohorts as $year) {
            $cohortTheses = $theses->filter(fn($t) => $t->student?->entry_year == $year);
            $total = count($cohortTheses);
            if ($total > 0) {
                $topicTrends = $cohortTheses->groupBy('topic')->map(fn($g) => round((count($g) / $total) * 100, 1));
                $topicTrendsResult['Angkatan ' . $year] = $topicTrends;
            }
        }

        // Early Warning System: Global Inactive Mentoring Students (> 7 days)
        $allActiveTheses = Thesis::where('status', '!=', 'completed')
            ->where('status', '!=', 'rejected')
            ->with(['student', 'pembimbing1', 'pembimbing2', 'mentoringSessions' => function($q) {
                $q->where('status', 'completed')->where('is_absent', false)->orderBy('scheduled_at', 'desc');
            }])
            ->get();
        $globalInactiveList = [];
        $globalWarningCount = 0;
        $globalCriticalCount = 0;
        $globalStalledIds = [];

        foreach ($allActiveTheses as $t) {
            $completedCount = $t->mentoringSessions ? $t->mentoringSessions->count() : 0;
            if ($completedCount === 0) {
                $globalStalledIds[] = $t->id;
            } else {
                $lastSession = $t->mentoringSessions->first();
                if ($lastSession && $lastSession->scheduled_at) {
                    $daysSince = (int) \Carbon\Carbon::parse($lastSession->scheduled_at)->startOfDay()->diffInDays(now()->startOfDay());
                    if ($daysSince > 14) {
                        $globalStalledIds[] = $t->id;
                    }
                }
            }

            $days = $t->days_since_last_mentoring;
            if ($days !== null && $days > 7 && !$t->isAccSidangFinal()) {
                $status = $days > 14 ? 'critical' : 'warning';
                if ($status === 'critical') {
                    $globalCriticalCount++;
                } else {
                    $globalWarningCount++;
                }

                $student = $t->student;
                $phone = $student ? preg_replace('/[^0-9]/', '', $student->phone_number ?? '') : '';
                if (str_starts_with($phone, '0')) {
                    $phone = '62' . substr($phone, 1);
                }

                $p1Name = $t->pembimbing1->name ?? 'Belum ditentukan';
                $waMsg = "Halo {$student->name} ({$student->identifier}), saya Admin/Kaprodi SIBIMA. Berdasarkan sistem, Anda belum melakukan bimbingan skripsi selama {$days} hari. Sesuai standar prodi, mahasiswa wajib bimbingan minimal 1x per minggu. Silakan segera jadwalkan bimbingan dengan dosen pembimbing Anda ({$p1Name}).";

                $globalInactiveList[] = [
                    'thesis_id' => $t->id,
                    'student_id' => $student->id ?? null,
                    'student_name' => $student->name ?? '-',
                    'student_npm' => $student->identifier ?? '-',
                    'title' => $t->display_title,
                    'p1_name' => $p1Name,
                    'p2_name' => $t->pembimbing2->name ?? '-',
                    'days_inactive' => $days,
                    'status' => $status,
                    'phone' => $phone,
                    'wa_link' => $phone ? "https://wa.me/{$phone}?text=" . urlencode($waMsg) : null,
                ];
            }
        }

        $globalStalledCount = count($globalStalledIds);
        $data['stalledThesesCount'] = $globalStalledCount;
        $data['stalledCount'] = $globalStalledCount;

        usort($globalInactiveList, fn($a, $b) => $b['days_inactive'] <=> $a['days_inactive']);

        $data['earlyWarning'] = [
            'total_inactive' => count($globalInactiveList),
            'stalled_count' => $globalStalledCount,
            'warning_count' => $globalWarningCount,
            'critical_count' => $globalCriticalCount,
            'students' => $globalInactiveList,
        ];

        $data['topicTrends'] = $topicTrendsResult;

        // Pending Operational Approvals for Admin & Kaprodi
        $thesesPending = Thesis::where('status', 'pending')->count();
        $unassignedTheses = Thesis::where('status', 'active')->whereNull('pembimbing1_id')->count();
        $seminarsPending = SeminarApplication::where('status', 'pending')->count();
        $defensesPending = ThesisDefenseApplication::where('status', 'pending')->count();
        $graduationsPending = \App\Models\Graduation::where('status', 'pending')->count();

        $data['adminApprovals'] = [
            'theses' => $thesesPending,
            'pending_theses' => $thesesPending,
            'unassigned_theses' => $unassignedTheses,
            'seminars' => $seminarsPending,
            'pending_seminars' => $seminarsPending,
            'defenses' => $defensesPending,
            'pending_defenses' => $defensesPending,
            'graduations' => $graduationsPending,
            'pending_graduations' => $graduationsPending,
            'total' => $thesesPending + $unassignedTheses + $seminarsPending + $defensesPending + $graduationsPending,
            'total_pending' => $thesesPending + $unassignedTheses + $seminarsPending + $defensesPending + $graduationsPending,
        ];

        // Active Wave Info for Admin & Kaprodi
        $activeWave = Wave::getCurrentActive();
        $activeWaveInfo = null;
        if ($activeWave) {
            $endDate = \Carbon\Carbon::parse($activeWave->end_date)->endOfDay();
            $daysRemaining = (int)now()->startOfDay()->diffInDays($endDate, false);
            $semCount = SeminarApplication::where('wave_id', $activeWave->id)->count();
            $defCount = ThesisDefenseApplication::where('wave_id', $activeWave->id)->count();
            $activeWaveInfo = [
                'id' => $activeWave->id,
                'name' => $activeWave->name,
                'academic_year' => $activeWave->academic_year,
                'semester' => $activeWave->semester,
                'start_date' => $activeWave->start_date,
                'end_date' => $activeWave->end_date,
                'days_remaining' => $daysRemaining,
                'is_active' => $activeWave->is_active,
                'is_expired' => $endDate->isPast(),
                'seminar_count' => $semCount,
                'seminar_applicant_count' => $semCount,
                'defense_count' => $defCount,
                'defense_applicant_count' => $defCount,
                'total_applicant_count' => $semCount + $defCount,
            ];
        }
        $data['activeWaveInfo'] = $activeWaveInfo;
        
        return $data;
    }

    public function getCommonData()
    {
        return Cache::remember('sibima_dashboard_common_data', 15, function () {
            // Omit avatar blob to keep cached payload ultra lightweight (< 2 KB) and prevent MySQL cache column truncation
            $allUsers = User::select('id', 'name', 'role', 'identifier', 'email')->get();
            $onlineUsers = $allUsers->filter(fn($u) => $u->is_online)->values();

            return [
                'announcements' => Announcement::where('is_active', true)->orderBy('created_at', 'desc')->take(3)->get(),
                'onlineUsers' => $onlineUsers,
                'onlineUsersCount' => $onlineUsers->count(),
                'onlineUsersByRole' => [
                    'mahasiswa' => $onlineUsers->where('role', 'mahasiswa')->count(),
                    'dosen' => $onlineUsers->where('role', 'dosen')->count(),
                    'kaprodi' => $onlineUsers->where('role', 'kaprodi')->count(),
                    'admin' => $onlineUsers->where('role', 'admin')->count(),
                ]
            ];
        });
    }
}
