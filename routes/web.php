<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/verify-signature/{token}', [App\Http\Controllers\SignatureController::class, 'verify'])->name('signature.verify');
Route::get('/verify/document/{token}', [App\Http\Controllers\DocumentVerificationController::class, 'verify'])->name('document.verify');

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Secure File Download Route
    Route::get('/download-private-file', [\App\Http\Controllers\DownloadController::class, 'downloadPrivateFile'])
        ->name('download.private');

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
    
    // Data Migrasi Skripsi (Bypass)
    Route::middleware(['role:admin,kaprodi'])->group(function () {
        Route::get('/theses/migration/template', [App\Http\Controllers\ThesisController::class, 'downloadTemplate'])->name('theses.migration.template');
        Route::post('/theses/migration/import', [App\Http\Controllers\ThesisController::class, 'importExcel'])->name('theses.migration.import');
        Route::get('/theses/migration', [App\Http\Controllers\ThesisController::class, 'createMigration'])->name('theses.migration.create');
        Route::post('/theses/migration', [App\Http\Controllers\ThesisController::class, 'storeMigration'])->name('theses.migration.store');
    });
    Route::get('/theses/kanban', [App\Http\Controllers\ThesisController::class, 'kanban'])->name('theses.kanban');
    Route::post('/theses/check-title', [App\Http\Controllers\ThesisController::class, 'checkTitle'])->name('theses.check-title');
    Route::resource('theses', App\Http\Controllers\ThesisController::class);
    Route::post('/theses/{thesis}/assign', [App\Http\Controllers\ThesisController::class, 'assignPembimbing'])->name('theses.assign');
    // Seminar Applications
    Route::get('/seminar-applications', [App\Http\Controllers\SeminarApplicationController::class, 'index'])->name('seminar-applications.index');
    Route::post('/seminar-applications', [App\Http\Controllers\SeminarApplicationController::class, 'store'])->name('seminar-applications.store');
    Route::post('/seminar-applications/upload-template', [App\Http\Controllers\SeminarApplicationController::class, 'uploadTemplate'])->name('seminar-applications.upload-template');
    Route::patch('/seminar-applications/{application}/validate', [App\Http\Controllers\SeminarApplicationController::class, 'validateApplication'])->name('seminar-applications.validate');
    Route::get('/seminar-applications/{application}/download-zip', [App\Http\Controllers\SeminarApplicationController::class, 'downloadZip'])->name('seminar-applications.download-zip');
    Route::delete('/seminar-applications/{application}', [App\Http\Controllers\SeminarApplicationController::class, 'destroy'])->name('seminar-applications.destroy');

    // Digital Repository
    Route::get('/repositories', [App\Http\Controllers\ThesisRepositoryController::class, 'index'])->name('repositories.index');
    Route::middleware(['role:admin,kaprodi'])->group(function () {
        Route::get('/repositories/import', [App\Http\Controllers\ThesisRepositoryController::class, 'createImport'])->name('repositories.import.create');
        Route::post('/repositories/import', [App\Http\Controllers\ThesisRepositoryController::class, 'storeImport'])->name('repositories.import.store');
        Route::get('/repositories/template', [App\Http\Controllers\ThesisRepositoryController::class, 'downloadTemplate'])->name('repositories.template');
    });

    // Seminar Examiner Routes
    Route::get('/seminar-examiner', [App\Http\Controllers\SeminarExaminerController::class, 'index'])->name('seminar-examiner.index');
    Route::get('/seminar-examiner/{detail}', [App\Http\Controllers\SeminarExaminerController::class, 'show'])->name('seminar-examiner.show');
    Route::get('/seminar-examiner/{detail}/grading', [App\Http\Controllers\SeminarExaminerController::class, 'grading'])->name('seminar-examiner.grading');
    Route::post('/seminar-examiner/{detail}/grading', [App\Http\Controllers\SeminarExaminerController::class, 'storeGrading'])->name('seminar-examiner.store-grading');
    Route::post('/seminar-examiner/{detail}/revision', [App\Http\Controllers\SeminarExaminerController::class, 'storeRevision'])->name('seminar-examiner.store-revision');
    Route::post('/seminar-examiner/revisions/{revision}/approve', [App\Http\Controllers\SeminarExaminerController::class, 'approveRevision'])->name('seminar-examiner.approve-revision');
    Route::get('/seminar-examiner/{detail}/export-berita-acara', [App\Http\Controllers\SeminarExaminerController::class, 'exportBeritaAcara'])->name('seminar-examiner.export-berita-acara');

    // Thesis Defense Examiner Routes
    Route::get('/defense-examiner', [App\Http\Controllers\ThesisDefenseExaminerController::class, 'index'])->name('defense-examiner.index');
    Route::get('/defense-examiner/{detail}', [App\Http\Controllers\ThesisDefenseExaminerController::class, 'show'])->name('defense-examiner.show');
    Route::get('/defense-examiner/{detail}/grading', [App\Http\Controllers\ThesisDefenseExaminerController::class, 'grading'])->name('defense-examiner.grading');
    Route::post('/defense-examiner/{detail}/grading', [App\Http\Controllers\ThesisDefenseExaminerController::class, 'storeGrading'])->name('defense-examiner.store-grading');
    Route::post('/defense-examiner/{detail}/revision', [App\Http\Controllers\ThesisDefenseExaminerController::class, 'storeRevision'])->name('defense-examiner.store-revision');
    Route::post('/defense-examiner/revisions/{revision}/approve', [App\Http\Controllers\ThesisDefenseExaminerController::class, 'approveRevision'])->name('defense-examiner.approve-revision');
    Route::post('/defense-examiner/{detail}/approve-direct', [App\Http\Controllers\ThesisDefenseExaminerController::class, 'approveRevisionDirect'])->name('defense-examiner.approve-revision-direct');
    Route::get('/defense-examiner/{detail}/export-berita-acara', [App\Http\Controllers\ThesisDefenseExaminerController::class, 'exportBeritaAcara'])->name('defense-examiner.export-berita-acara');

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
    Route::get('/student/history', [App\Http\Controllers\StudentHistoryController::class, 'index'])->name('student.history');

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

    // Seminar & Defense Schedules (Accessible by Admin, Kaprodi, and Dosen)
    Route::resource('seminar-schedules', App\Http\Controllers\SeminarScheduleController::class);
    Route::get('/seminar-schedules/{seminar_schedule}/export-pdf', [App\Http\Controllers\SeminarScheduleController::class, 'exportPdf'])->name('seminar-schedules.export-pdf');

    Route::resource('thesis-defense-schedules', App\Http\Controllers\ThesisDefenseScheduleController::class);
    Route::get('/thesis-defense-schedules/{thesis_defense_schedule}/export-pdf', [App\Http\Controllers\ThesisDefenseScheduleController::class, 'exportPdf'])->name('thesis-defense-schedules.export-pdf');

    // Admin & Kaprodi Routes
    Route::middleware(['role:admin,kaprodi'])->group(function () {
        // Announcements
        Route::get('/announcements', [App\Http\Controllers\AnnouncementController::class, 'index'])->name('announcements.index');
        Route::post('/announcements', [App\Http\Controllers\AnnouncementController::class, 'store'])->name('announcements.store');
        Route::patch('/announcements/{announcement}', [App\Http\Controllers\AnnouncementController::class, 'update'])->name('announcements.update');
        Route::delete('/announcements/{announcement}', [App\Http\Controllers\AnnouncementController::class, 'destroy'])->name('announcements.destroy');
        Route::post('/announcements/{announcement}/toggle', [App\Http\Controllers\AnnouncementController::class, 'toggleStatus'])->name('announcements.toggle');

        // System Logs
        Route::get('/admin/logs/export', [App\Http\Controllers\ActivityLogController::class, 'export'])->name('admin.logs.export');
        Route::get('/admin/logs', [App\Http\Controllers\ActivityLogController::class, 'index'])->name('admin.logs');

        // Monitoring
        Route::get('/monitoring/revisions', [App\Http\Controllers\MonitoringController::class, 'revisions'])->name('monitoring.revisions');
        Route::get('/monitoring/defense-revisions', [App\Http\Controllers\MonitoringController::class, 'defenseRevisions'])->name('monitoring.defense-revisions');
        Route::get('/monitoring/defense-scores/export-excel', [App\Http\Controllers\MonitoringController::class, 'exportDefenseScoresExcel'])->name('monitoring.defense-scores.export-excel');
        Route::get('/monitoring/defense-scores/export-pdf', [App\Http\Controllers\MonitoringController::class, 'exportDefenseScoresPdf'])->name('monitoring.defense-scores.export-pdf');
        Route::get('/monitoring/advanced-reporting', [App\Http\Controllers\AdvancedReportingController::class, 'index'])->name('monitoring.advanced-reporting');
        Route::get('/monitoring/defense-scores/{detail}/berita-acara', [App\Http\Controllers\MonitoringController::class, 'exportBeritaAcara'])->name('monitoring.defense-scores.berita-acara');
        Route::get('/monitoring/seminar-scores/{detail}/berita-acara', [App\Http\Controllers\MonitoringController::class, 'exportBeritaAcaraSeminar'])->name('monitoring.seminar-scores.berita-acara');
        Route::get('/monitoring/defense-scores', [App\Http\Controllers\MonitoringController::class, 'defenseScores'])->name('monitoring.defense-scores');
        Route::get('/monitoring/critical', [App\Http\Controllers\MonitoringController::class, 'criticalStudents'])->name('monitoring.critical');
        Route::get('/monitoring/batch-export-berita-acara', [App\Http\Controllers\MonitoringController::class, 'batchExportBeritaAcara'])->name('monitoring.batch-export-berita-acara');
        Route::get('/monitoring/export', [App\Http\Controllers\MonitoringController::class, 'export'])->name('monitoring.export');
        Route::get('/monitoring', [App\Http\Controllers\MonitoringController::class, 'index'])->name('monitoring.index');

        // Document Generation & Settings
        Route::get('/documents/sk-penguji-seminar/{schedule}', [App\Http\Controllers\DocumentController::class, 'generateSKTimPengujiSeminar'])->name('documents.sk-penguji-seminar');
        Route::get('/documents/sk-penguji-sidang/{schedule}', [App\Http\Controllers\DocumentController::class, 'generateSKTimPengujiSidang'])->name('documents.sk-penguji-sidang');
        Route::get('/admin/letter-settings', [App\Http\Controllers\LetterSettingController::class, 'index'])->name('admin.letter-settings.index');
        Route::put('/admin/letter-settings/{letterSetting}', [App\Http\Controllers\LetterSettingController::class, 'update'])->name('admin.letter-settings.update');

        // User Management
        Route::get('/users/export', [App\Http\Controllers\UserController::class, 'export'])->name('users.export');
        Route::post('/users/import', [App\Http\Controllers\UserController::class, 'import'])->name('users.import');
        Route::post('/users/{user}/toggle', [App\Http\Controllers\UserController::class, 'toggleStatus'])->name('users.toggle');
        Route::resource('users', App\Http\Controllers\UserController::class)->except(['show']);

        // Wave Management
        Route::post('/waves/{wave}/toggle', [App\Http\Controllers\WaveController::class, 'toggle'])->name('waves.toggle');
        Route::resource('waves', App\Http\Controllers\WaveController::class)->except(['show', 'create', 'edit']);


    });

    // Notifications
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    // Calendar
    Route::get('/calendar', [App\Http\Controllers\CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/events', [App\Http\Controllers\CalendarController::class, 'events'])->name('calendar.events');

    // Conflict Check
    Route::post('/check-dosen-availability', [App\Http\Controllers\ScheduleConflictController::class, 'checkDosenAvailability'])->name('check-dosen-availability');
});

require __DIR__ . '/auth.php';
