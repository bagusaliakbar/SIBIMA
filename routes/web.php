<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/verify-signature/{token}', [App\Http\Controllers\SignatureController::class, 'verify'])->name('signature.verify');

Route::get('/dashboard', function () {
    $thesis = null;
    $upcomingSessions = collect();
    $pastSessionsCount = 0;
    $pastSessionsCountP1 = 0;
    $pastSessionsCountP2 = 0;
    $recentLogbooks = collect();
    $activeThesesCount = 0;
    $sessionsThisWeek = 0;
    $seminar = null;
    $defense = null;

    $thesisStatusCounts = [];
    $monthlyMentoringCounts = [];
    $studentProgressDistribution = [];
    $dosenWorkload = [];

    // Initialize Dosen specific variables to avoid compact error for other roles
    $totalActiveStudentsP1 = 0;
    $totalActiveStudentsP2 = 0;
    $pendingSessionsThisWeek = 0;
    $approvedSessionsThisWeek = 0;
    $totalCompletedSessions = 0;
    $averageStudentProgress = 0;
    $isStale = false;
    $daysSinceLastSession = null;

    $mySeminarSchedule = null;
    $myDefenseSchedule = null;
    $examinerSeminarSchedules = collect();
    $examinerDefenseSchedules = collect();

    $onTimeStats = null;
    $studentHealthStats = null;

    if (Auth::user()->role === 'mahasiswa') {
        $thesis = \App\Models\Thesis::with(['pembimbing1', 'pembimbing2'])
            ->where('student_id', Auth::id())->first();

        if ($thesis) {
            $upcomingSessions = \App\Models\MentoringSession::where('thesis_id', $thesis->id)
                ->where('scheduled_at', '>=', now())
                ->whereIn('status', ['pending', 'approved'])
                ->orderBy('scheduled_at', 'asc')
                ->take(5)
                ->get();

            $pastSessionsCount = \App\Models\MentoringSession::where('thesis_id', $thesis->id)
                ->where('status', 'completed')
                ->count();

            $pastSessionsCountP1 = \App\Models\MentoringSession::where('thesis_id', $thesis->id)
                ->where('dosen_id', $thesis->pembimbing1_id)
                ->where('status', 'completed')
                ->count();

            $pastSessionsCountP2 = \App\Models\MentoringSession::where('thesis_id', $thesis->id)
                ->where('dosen_id', $thesis->pembimbing2_id)
                ->where('status', 'completed')
                ->count();

            $recentLogbooks = \App\Models\MentoringSession::where('thesis_id', $thesis->id)
                ->whereNotNull('notes')
                ->where('status', 'completed')
                ->orderBy('scheduled_at', 'desc')
                ->take(5)
                ->get();

            $seminar = \App\Models\SeminarApplication::where('thesis_id', $thesis->id)->first();
            $defense = \App\Models\ThesisDefenseApplication::where('thesis_id', $thesis->id)->first();

            $mySeminarSchedule = \App\Models\SeminarScheduleDetail::with(['schedule', 'examiner1', 'examiner2'])
                ->where('thesis_id', $thesis->id)
                ->first();
            
            $myDefenseSchedule = \App\Models\ThesisDefenseScheduleDetail::with(['schedule', 'examiner1', 'examiner2'])
                ->where('thesis_id', $thesis->id)
                ->first();

            // Deadline Alert Logic
            $lastSession = \App\Models\MentoringSession::where('thesis_id', $thesis->id)
                ->where('status', 'completed')
                ->orderBy('scheduled_at', 'desc')
                ->first();
            
            $daysSinceLastSession = $lastSession ? (int)now()->diffInDays($lastSession->scheduled_at) : null;
            $isStale = $daysSinceLastSession !== null && $daysSinceLastSession >= 14;

            // Auto-sync Graduation Status
            if ($thesis->status !== 'completed') {
                $defenseSchedule = \App\Models\ThesisDefenseScheduleDetail::where('thesis_id', $thesis->id)->first();
                if ($defenseSchedule && $defenseSchedule->isRevisionAllApproved()) {
                    $thesis->update(['status' => 'completed']);
                    $thesis->refresh(); // Update the local $thesis object
                }
            }
        }
    } elseif (Auth::user()->role === 'dosen') {
        $dosenId = Auth::id();

        $examinerSeminarSchedules = \App\Models\SeminarScheduleDetail::with(['schedule', 'thesis.student'])
            ->where(function($q) use ($dosenId) {
                $q->where('examiner1_id', $dosenId)
                ->orWhere('examiner2_id', $dosenId)
                ->orWhereHas('thesis', function($sq) use ($dosenId) {
                    $sq->where('pembimbing1_id', $dosenId)
                      ->orWhere('pembimbing2_id', $dosenId);
                });
            })
            ->whereHas('schedule', function($q) {
                $q->where('date', '>=', now()->toDateString());
            })
            ->orderBy('start_time', 'asc')
            ->get();

        $examinerDefenseSchedules = \App\Models\ThesisDefenseScheduleDetail::with(['schedule', 'thesis.student'])
            ->where(function($q) use ($dosenId) {
                $q->where('examiner1_id', $dosenId)
                ->orWhere('examiner2_id', $dosenId)
                ->orWhereHas('thesis', function($sq) use ($dosenId) {
                    $sq->where('pembimbing1_id', $dosenId)
                      ->orWhere('pembimbing2_id', $dosenId);
                });
            })
            ->whereHas('schedule', function($q) {
                $q->where('date', '>=', now()->toDateString());
            })
            ->orderBy('start_time', 'asc')
            ->get();

        $activeTheses = \App\Models\Thesis::where('status', 'active')
            ->where(function ($q) use ($dosenId) {
                $q->where('pembimbing1_id', $dosenId)
                    ->orWhere('pembimbing2_id', $dosenId);
            })->get();

        $activeThesesCount = $activeTheses->count();
        $totalActiveStudentsP1 = $activeTheses->where('pembimbing1_id', $dosenId)->count();
        $totalActiveStudentsP2 = $activeTheses->where('pembimbing2_id', $dosenId)->count();

        $dosenThesisIds = $activeTheses->pluck('id');

        $sessionsThisWeekQuery = \App\Models\MentoringSession::whereIn('thesis_id', $dosenThesisIds)
            ->whereBetween('scheduled_at', [now()->startOfWeek(), now()->endOfWeek()]);

        $sessionsThisWeek = (clone $sessionsThisWeekQuery)->count();
        $pendingSessionsThisWeek = (clone $sessionsThisWeekQuery)->where('status', 'pending')->count();
        $approvedSessionsThisWeek = (clone $sessionsThisWeekQuery)->where('status', 'approved')->count();

        $totalCompletedSessions = \App\Models\MentoringSession::whereIn('thesis_id', $dosenThesisIds)
            ->where('status', 'completed')
            ->count();

        $upcomingSessions = \App\Models\MentoringSession::whereIn('thesis_id', $dosenThesisIds)
            ->where('scheduled_at', '>=', now())
            ->whereIn('status', ['pending', 'approved'])
            ->orderBy('scheduled_at', 'asc')
            ->take(5)
            ->get();

        $recentLogbooks = \App\Models\MentoringSession::whereIn('thesis_id', $dosenThesisIds)
            ->whereNotNull('notes')
            ->where('status', 'completed')
            ->orderBy('scheduled_at', 'desc')
            ->take(5)
            ->get();

        // Calculate average progress
        $totalProgressSum = 0;
        foreach ($activeTheses as $t) {
            $p = 25; // Already has title
            $comp = \App\Models\MentoringSession::where('thesis_id', $t->id)->where('status', 'completed')->count();
            $p += min(25, ($comp / 8) * 25);
            $sem = \App\Models\SeminarApplication::where('thesis_id', $t->id)->first();
            if ($sem && $sem->status === 'approved')
                $p += 25;
            if ($t->acc_sidang_p1 && $t->acc_sidang_p2)
                $p += 25;
            $totalProgressSum += $p;
        }
        $averageStudentProgress = $activeThesesCount > 0 ? round($totalProgressSum / $activeThesesCount) : 0;

        // Analytics for Dosen Chart
        $studentProgressDistribution = [
            'Judul' => $activeThesesCount,
            'Bimbingan' => \App\Models\MentoringSession::whereIn('thesis_id', $dosenThesisIds)->where('status', 'completed')->distinct('thesis_id')->count(),
            'ACC Seminar' => \App\Models\Thesis::whereIn('id', $dosenThesisIds)->where('acc_up_p1', true)->where('acc_up_p2', true)->count(),
            'ACC Sidang' => \App\Models\Thesis::whereIn('id', $dosenThesisIds)->where('acc_sidang_p1', true)->where('acc_sidang_p2', true)->count(),
        ];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyMentoringCounts[$month->format('M')] = \App\Models\MentoringSession::whereIn('thesis_id', $dosenThesisIds)
                ->where('status', 'completed')
                ->whereYear('scheduled_at', $month->year)
                ->whereMonth('scheduled_at', $month->month)
                ->count();
        }

    } elseif (Auth::user()->role === 'admin') {
        $activeThesesCount = \App\Models\Thesis::where('status', 'active')->count();

        $sessionsThisWeek = \App\Models\MentoringSession::whereBetween('scheduled_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        $upcomingSessions = \App\Models\MentoringSession::where('scheduled_at', '>=', now())
            ->whereIn('status', ['pending', 'approved'])
            ->orderBy('scheduled_at', 'asc')
            ->take(5)
            ->get();

        $recentLogbooks = \App\Models\MentoringSession::whereNotNull('notes')
            ->where('status', 'completed')
            ->orderBy('scheduled_at', 'desc')
            ->take(5)
            ->get();

        // Analytics for Admin
        $thesisStatusCounts = [
            'active' => \App\Models\Thesis::where('status', 'active')->count(),
            'completed' => \App\Models\Thesis::where('status', 'completed')->count(),
            'pending' => \App\Models\Thesis::where('status', 'pending')->count(),
        ];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyMentoringCounts[$month->format('M')] = \App\Models\MentoringSession::where('status', 'completed')
                ->whereYear('scheduled_at', $month->year)
                ->whereMonth('scheduled_at', $month->month)
                ->count();
        }

        // Workload Data for Admin
        $dosens = \App\Models\User::where('role', 'dosen')->get();
        foreach ($dosens as $d) {
            $count = \App\Models\Thesis::where('status', 'active')
                ->where(function ($q) use ($d) {
                    $q->where('pembimbing1_id', $d->id)
                        ->orWhere('pembimbing2_id', $d->id);
                })->count();
            if ($count > 0) {
                $dosenWorkload[$d->name] = $count;
            }
        }
        arsort($dosenWorkload);
        $dosenWorkload = array_slice($dosenWorkload, 0, 10, true);

        // On-time Graduation Statistics
        $onTimeGraduates = 0;
        $lateGraduates = 0;
        $completedTheses = \App\Models\Thesis::where('status', 'completed')->with('student')->get();
        foreach ($completedTheses as $t) {
            if ($t->student && $t->student->entry_year) {
                $graduationYear = $t->updated_at->year;
                if ($graduationYear - $t->student->entry_year <= 4) {
                    $onTimeGraduates++;
                } else {
                    $lateGraduates++;
                }
            }
        }
        $onTimeStats = [
            'Tepat Waktu' => $onTimeGraduates,
            'Terlambat' => $lateGraduates,
        ];

        // Critical Students (Semester 13-14 or more)
        // Semester 1 is entry_year. Year 7 is Semester 13-14.
        $criticalThresholdYear = now()->year - 6;
        $criticalStudentsCount = \App\Models\User::where('role', 'mahasiswa')
            ->where('entry_year', '<=', $criticalThresholdYear)
            ->whereHas('thesis', function($q) {
                $q->where('status', '!=', 'completed');
            })->count();

        $studentHealthStats = [
            'Normal' => \App\Models\User::where('role', 'mahasiswa')->where('entry_year', '>', $criticalThresholdYear)->count(),
            'Kritis' => $criticalStudentsCount,
        ];

        // Completion Time per Cohort
        $cohortCompletionData = [];
        $completedTheses = \App\Models\Thesis::where('status', 'completed')->with('student')->get();
        $cohortYears = $completedTheses->map(fn($t) => $t->student?->entry_year)->filter()->unique()->sort();
        
        foreach ($cohortYears as $year) {
            $thesesInCohort = $completedTheses->filter(fn($t) => $t->student?->entry_year == $year);
            $totalDuration = 0;
            $count = 0;
            foreach ($thesesInCohort as $t) {
                $graduationDuration = $t->updated_at->year - $year;
                $totalDuration += $graduationDuration;
                $count++;
            }
            if ($count > 0) {
                $cohortCompletionData["Angkatan " . $year] = round($totalDuration / $count, 1);
            }
        }
    }
    $announcements = \App\Models\Announcement::where('is_active', true)->orderBy('created_at', 'desc')->take(3)->get();

    return view('dashboard', compact(
        'thesis',
        'upcomingSessions',
        'pastSessionsCount',
        'pastSessionsCountP1',
        'pastSessionsCountP2',
        'recentLogbooks',
        'activeThesesCount',
        'sessionsThisWeek',
        'announcements',
        'seminar',
        'defense',
        'thesisStatusCounts',
        'monthlyMentoringCounts',
        'studentProgressDistribution',
        'totalActiveStudentsP1',
        'totalActiveStudentsP2',
        'pendingSessionsThisWeek',
        'approvedSessionsThisWeek',
        'totalCompletedSessions',
        'averageStudentProgress',
        'dosenWorkload',
        'isStale',
        'daysSinceLastSession',
        'mySeminarSchedule',
        'myDefenseSchedule',
        'examinerSeminarSchedules',
        'examinerDefenseSchedules',
        'onTimeStats',
        'studentHealthStats',
        'cohortCompletionData'
    ));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/signature', [ProfileController::class, 'updateSignature'])->name('profile.signature.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Chat Routes
    Route::get('/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{user}', [App\Http\Controllers\ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{user}', [App\Http\Controllers\ChatController::class, 'store'])->name('chat.store');

    Route::get('/theses/export-excel', [App\Http\Controllers\ThesisController::class, 'exportExcel'])->name('theses.export-excel');
    Route::get('/theses/export-pdf', [App\Http\Controllers\ThesisController::class, 'exportPdf'])->name('theses.export-pdf');
    Route::post('/theses/{thesis}/toggle-acc/{type}', [App\Http\Controllers\ThesisController::class, 'toggleAcc'])->name('theses.toggle-acc');
    Route::resource('theses', App\Http\Controllers\ThesisController::class);
    Route::post('/theses/{thesis}/assign', [App\Http\Controllers\ThesisController::class, 'assignPembimbing'])->name('theses.assign');
    // Seminar Applications
    Route::get('/seminar-applications', [App\Http\Controllers\SeminarApplicationController::class, 'index'])->name('seminar-applications.index');
    Route::post('/seminar-applications', [App\Http\Controllers\SeminarApplicationController::class, 'store'])->name('seminar-applications.store');
    Route::post('/seminar-applications/upload-template', [App\Http\Controllers\SeminarApplicationController::class, 'uploadTemplate'])->name('seminar-applications.upload-template');
    Route::patch('/seminar-applications/{application}/validate', [App\Http\Controllers\SeminarApplicationController::class, 'validateApplication'])->name('seminar-applications.validate');
    Route::get('/seminar-applications/{application}/download-zip', [App\Http\Controllers\SeminarApplicationController::class, 'downloadZip'])->name('seminar-applications.download-zip');
    Route::delete('/seminar-applications/{application}', [App\Http\Controllers\SeminarApplicationController::class, 'destroy'])->name('seminar-applications.destroy');

    // Seminar Examiner Routes
    Route::get('/seminar-examiner', [App\Http\Controllers\SeminarExaminerController::class, 'index'])->name('seminar-examiner.index');
    Route::get('/seminar-examiner/{detail}', [App\Http\Controllers\SeminarExaminerController::class, 'show'])->name('seminar-examiner.show');
    Route::post('/seminar-examiner/{detail}/revision', [App\Http\Controllers\SeminarExaminerController::class, 'storeRevision'])->name('seminar-examiner.store-revision');
    Route::post('/seminar-examiner/revisions/{revision}/approve', [App\Http\Controllers\SeminarExaminerController::class, 'approveRevision'])->name('seminar-examiner.approve-revision');

    // Thesis Defense Examiner Routes
    Route::get('/defense-examiner', [App\Http\Controllers\ThesisDefenseExaminerController::class, 'index'])->name('defense-examiner.index');
    Route::get('/defense-examiner/{detail}', [App\Http\Controllers\ThesisDefenseExaminerController::class, 'show'])->name('defense-examiner.show');
    Route::get('/defense-examiner/{detail}/grading', [App\Http\Controllers\ThesisDefenseExaminerController::class, 'grading'])->name('defense-examiner.grading');
    Route::post('/defense-examiner/{detail}/grading', [App\Http\Controllers\ThesisDefenseExaminerController::class, 'storeGrading'])->name('defense-examiner.store-grading');
    Route::post('/defense-examiner/{detail}/revision', [App\Http\Controllers\ThesisDefenseExaminerController::class, 'storeRevision'])->name('defense-examiner.store-revision');
    Route::post('/defense-examiner/revisions/{revision}/approve', [App\Http\Controllers\ThesisDefenseExaminerController::class, 'approveRevision'])->name('defense-examiner.approve-revision');
    Route::post('/defense-examiner/{detail}/approve-direct', [App\Http\Controllers\ThesisDefenseExaminerController::class, 'approveRevisionDirect'])->name('defense-examiner.approve-revision-direct');

    // Student Seminar Revision Routes
    Route::get('/student-seminar-revisions', [App\Http\Controllers\StudentSeminarRevisionController::class, 'index'])->name('student-seminar-revisions.index');
    Route::get('/student-seminar-revisions/{revision}', [App\Http\Controllers\StudentSeminarRevisionController::class, 'show'])->name('student-seminar-revisions.show');
    Route::post('/student-seminar-revisions/{revision}/reply', [App\Http\Controllers\StudentSeminarRevisionController::class, 'storeReply'])->name('student-seminar-revisions.store-reply');
    Route::get('/student-seminar-revisions/{revision}/print', [App\Http\Controllers\StudentSeminarRevisionController::class, 'printPdf'])->name('student-seminar-revisions.print-pdf');

    // Student Thesis Defense Revision Routes
    Route::get('/student-defense-revisions', [App\Http\Controllers\StudentThesisDefenseRevisionController::class, 'index'])->name('student-defense-revisions.index');
    Route::get('/student-defense-revisions/{revision}', [App\Http\Controllers\StudentThesisDefenseRevisionController::class, 'show'])->name('student-defense-revisions.show');
    Route::post('/student-defense-revisions/{revision}/reply', [App\Http\Controllers\StudentThesisDefenseRevisionController::class, 'storeReply'])->name('student-defense-revisions.store-reply');
    Route::get('/student-defense-revisions/{revision}/print', [App\Http\Controllers\StudentThesisDefenseRevisionController::class, 'printPdf'])->name('student-defense-revisions.print-pdf');

    // Thesis Defense Applications (Sidang)
    Route::get('/thesis-defense-applications', [App\Http\Controllers\ThesisDefenseApplicationController::class, 'index'])->name('thesis-defense-applications.index');
    Route::post('/thesis-defense-applications', [App\Http\Controllers\ThesisDefenseApplicationController::class, 'store'])->name('thesis-defense-applications.store');
    Route::post('/thesis-defense-applications/upload-template', [App\Http\Controllers\ThesisDefenseApplicationController::class, 'uploadTemplate'])->name('thesis-defense-applications.upload-template');
    Route::patch('/thesis-defense-applications/{application}/validate', [App\Http\Controllers\ThesisDefenseApplicationController::class, 'validateApplication'])->name('thesis-defense-applications.validate');
    Route::get('/thesis-defense-applications/{application}/download-zip', [App\Http\Controllers\ThesisDefenseApplicationController::class, 'downloadZip'])->name('thesis-defense-applications.download-zip');
    Route::delete('/thesis-defense-applications/{application}', [App\Http\Controllers\ThesisDefenseApplicationController::class, 'destroy'])->name('thesis-defense-applications.destroy');

    Route::resource('mentoring-sessions', App\Http\Controllers\MentoringSessionController::class);
    Route::patch('/mentoring-sessions/{session}/status', [App\Http\Controllers\MentoringSessionController::class, 'updateStatus'])->name('mentoring-sessions.status');
    Route::post('/mentoring-sessions/{session}/upload-document', [App\Http\Controllers\MentoringSessionController::class, 'uploadDocument'])->name('mentoring-sessions.upload-document');
    Route::delete('/mentoring-sessions/{session}/document', [App\Http\Controllers\MentoringSessionController::class, 'deleteDocument'])->name('mentoring-sessions.delete-document');

    // Logbooks
    Route::get('/logbooks', [App\Http\Controllers\LogbookController::class, 'index'])->name('logbooks.index');
    Route::get('/logbooks/export-pdf', [App\Http\Controllers\LogbookController::class, 'exportPdf'])->name('logbooks.export-pdf');
    Route::get('/theses/{thesis}/logbooks', [App\Http\Controllers\LogbookController::class, 'show'])->name('theses.logbooks');
    Route::get('/theses/{thesis}/logbooks/export-pdf', [App\Http\Controllers\LogbookController::class, 'exportPdf'])->name('theses.logbooks.export-pdf');

    // Announcements (Admin Only)
    Route::get('/announcements', [App\Http\Controllers\AnnouncementController::class, 'index'])->name('announcements.index');
    Route::post('/announcements', [App\Http\Controllers\AnnouncementController::class, 'store'])->name('announcements.store');
    Route::patch('/announcements/{announcement}', [App\Http\Controllers\AnnouncementController::class, 'update'])->name('announcements.update');
    Route::delete('/announcements/{announcement}', [App\Http\Controllers\AnnouncementController::class, 'destroy'])->name('announcements.destroy');
    Route::post('/announcements/{announcement}/toggle', [App\Http\Controllers\AnnouncementController::class, 'toggleStatus'])->name('announcements.toggle');

    // System Logs (Admin Only)
    Route::get('/admin/logs/export', [App\Http\Controllers\ActivityLogController::class, 'export'])->name('admin.logs.export');
    Route::get('/admin/logs', [App\Http\Controllers\ActivityLogController::class, 'index'])->name('admin.logs');

    // Monitoring (Admin Only)
    Route::get('/monitoring/revisions', [App\Http\Controllers\MonitoringController::class, 'revisions'])->name('monitoring.revisions');
    Route::get('/monitoring/defense-revisions', [App\Http\Controllers\MonitoringController::class, 'defenseRevisions'])->name('monitoring.defense-revisions');
    Route::get('/monitoring/defense-scores/export-excel', [App\Http\Controllers\MonitoringController::class, 'exportDefenseScoresExcel'])->name('monitoring.defense-scores.export-excel');
    Route::get('/monitoring/defense-scores/export-pdf', [App\Http\Controllers\MonitoringController::class, 'exportDefenseScoresPdf'])->name('monitoring.defense-scores.export-pdf');
    Route::get('/monitoring/defense-scores/{detail}/berita-acara', [App\Http\Controllers\MonitoringController::class, 'exportBeritaAcara'])->name('monitoring.defense-scores.berita-acara');
    Route::get('/monitoring/defense-scores', [App\Http\Controllers\MonitoringController::class, 'defenseScores'])->name('monitoring.defense-scores');
    Route::get('/monitoring/critical', [App\Http\Controllers\MonitoringController::class, 'criticalStudents'])->name('monitoring.critical');
    Route::get('/monitoring/export', [App\Http\Controllers\MonitoringController::class, 'export'])->name('monitoring.export');
    Route::get('/monitoring', [App\Http\Controllers\MonitoringController::class, 'index'])->name('monitoring.index');

    // Seminar Schedule (Admin Only)
    Route::resource('seminar-schedules', App\Http\Controllers\SeminarScheduleController::class);
    Route::get('/seminar-schedules/{seminar_schedule}/export-pdf', [App\Http\Controllers\SeminarScheduleController::class, 'exportPdf'])->name('seminar-schedules.export-pdf');

    // Thesis Defense Schedule (Sidang) (Admin Only)
    Route::resource('thesis-defense-schedules', App\Http\Controllers\ThesisDefenseScheduleController::class);
    Route::get('/thesis-defense-schedules/{thesis_defense_schedule}/export-pdf', [App\Http\Controllers\ThesisDefenseScheduleController::class, 'exportPdf'])->name('thesis-defense-schedules.export-pdf');

    // User Management (Admin Only)
    Route::get('/users/export', [App\Http\Controllers\UserController::class, 'export'])->name('users.export');
    Route::post('/users/import', [App\Http\Controllers\UserController::class, 'import'])->name('users.import');
    Route::post('/users/{user}/toggle', [App\Http\Controllers\UserController::class, 'toggleStatus'])->name('users.toggle');
    Route::resource('users', App\Http\Controllers\UserController::class)->except(['show']);

    // Wave Management (Admin Only)
    Route::post('/waves/{wave}/toggle', [App\Http\Controllers\WaveController::class, 'toggle'])->name('waves.toggle');
    Route::resource('waves', App\Http\Controllers\WaveController::class)->except(['show', 'create', 'edit']);

    // Notifications
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    // Conflict Check
    Route::post('/check-dosen-availability', [App\Http\Controllers\ScheduleConflictController::class, 'checkDosenAvailability'])->name('check-dosen-availability');
});

require __DIR__ . '/auth.php';
