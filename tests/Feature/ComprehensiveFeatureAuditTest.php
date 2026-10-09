<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Thesis;
use App\Models\MentoringSession;
use App\Models\SeminarApplication;
use App\Models\SeminarSchedule;
use App\Models\SeminarScheduleDetail;
use App\Models\SeminarRevision;
use App\Models\Graduation;
use App\Models\AdvisorDecree;
use App\Models\JournalBookmark;
use App\Models\JournalBookmarkFolder;
use App\Models\BugReport;
use App\Models\Wave;
use App\Models\LetterSetting;
use App\Services\JournalAiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComprehensiveFeatureAuditTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. Public Routes & Verification Tokens (Guest Access)
     */
    public function test_public_routes_and_qr_verification_endpoints()
    {
        // 1.1 Home redirects to login
        $this->get('/')->assertRedirect(route('login'));

        // 1.2 FAQ page accessible publicly
        $this->get('/faq')->assertStatus(200);

        // 1.3 Digital Signature Verification
        $signer = User::factory()->create([
            'role' => 'dosen',
            'signature_token' => 'valid-sign-token-12345',
        ]);

        $this->get(route('signature.verify', 'invalid-token'))
            ->assertStatus(200)
            ->assertSee('tidak valid');

        $this->get(route('signature.verify', 'valid-sign-token-12345'))
            ->assertStatus(200)
            ->assertSee('valid');

        // 1.4 SKL Verification (Graduation)
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $p1 = User::factory()->create(['role' => 'dosen']);
        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Sistem Informasi Skripsi Audit',
            'pembimbing1_id' => $p1->id,
            'status' => 'completed',
        ]);

        $graduation = Graduation::create([
            'thesis_id' => $thesis->id,
            'student_id' => $student->id,
            'status' => 'approved',
            'skl_number' => 'SKL/2026/001',
            'verification_token' => 'skl-token-xyz-987',
            'gpa' => 3.85,
            'predicate' => 'Dengan Pujian (Cum Laude)',
        ]);

        $this->get(route('skl.verify', 'invalid-skl-token'))
            ->assertStatus(200)
            ->assertSee('Dokumen Tidak Valid');

        $this->get(route('skl.verify', 'skl-token-xyz-987'))
            ->assertStatus(200)
            ->assertSee('SKL/2026/001')
            ->assertSee('Dengan Pujian (Cum Laude)');

        // 1.5 SK Pembimbing Verification
        $decree = AdvisorDecree::create([
            'decree_number' => 'SK/FIK/2026/001',
            'title' => 'Penetapan Dosen Pembimbing',
            'academic_year' => '2025/2026',
            'semester' => 'Ganjil',
            'target_type' => 'individual_dosen',
            'dosen_id' => $p1->id,
            'decree_date' => now()->toDateString(),
            'signatory_name' => 'Dekan Fasilkom',
            'theses_data' => [],
            'verification_token' => 'decree-token-abc-555',
            'created_by' => $p1->id,
        ]);

        $this->get(route('sk-pembimbing.verify', 'non-existent-decree'))
            ->assertStatus(200)
            ->assertSee('Dokumen Tidak Valid');

        $this->get(route('sk-pembimbing.verify', 'decree-token-abc-555'))
            ->assertStatus(200)
            ->assertSee('SK/FIK/2026/001');
    }

    /**
     * 2. RBAC Access Control Matrix (Role Authorization Boundaries)
     */
    public function test_rbac_access_control_matrix()
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $dosen = User::factory()->create(['role' => 'dosen']);
        $kaprodi = User::factory()->create(['role' => 'kaprodi']);
        $admin = User::factory()->create(['role' => 'admin']);

        // 2.1 Unauthenticated redirects
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('theses.index'))->assertRedirect(route('login'));
        $this->get(route('chat.index'))->assertRedirect(route('login'));

        // 2.2 Student boundaries
        $this->actingAs($student)->get(route('admin.logs'))->assertStatus(403);
        $this->actingAs($student)->get(route('users.index'))->assertStatus(403);
        $this->actingAs($student)->get(route('wa-broadcasts.index'))->assertStatus(403);
        $this->actingAs($student)->get(route('seminar-examiner.index'))->assertStatus(403);
        $this->actingAs($student)->get(route('monitoring.index'))->assertStatus(403);
        $this->actingAs($student)->get(route('advisor-decrees.create'))->assertStatus(403);

        // 2.3 Dosen boundaries
        $this->actingAs($dosen)->get(route('admin.logs'))->assertStatus(403);
        $this->actingAs($dosen)->get(route('users.index'))->assertStatus(403);
        $this->actingAs($dosen)->get(route('wa-broadcasts.index'))->assertStatus(403);
        $this->actingAs($dosen)->get(route('monitoring.index'))->assertStatus(403);

        // 2.4 Kaprodi elevated access
        $this->actingAs($kaprodi)->get(route('admin.logs'))->assertStatus(200);
        $this->actingAs($kaprodi)->get(route('users.index'))->assertStatus(200);
        $this->actingAs($kaprodi)->get(route('wa-broadcasts.index'))->assertStatus(200);
        $this->actingAs($kaprodi)->get(route('monitoring.index'))->assertStatus(200);

        // 2.5 Admin elevated access
        $this->actingAs($admin)->get(route('admin.logs'))->assertStatus(200);
        $this->actingAs($admin)->get(route('users.index'))->assertStatus(200);
        $this->actingAs($admin)->get(route('wa-broadcasts.index'))->assertStatus(200);
        $this->actingAs($admin)->get(route('monitoring.index'))->assertStatus(200);
    }

    /**
     * 3. Thesis Lifecycle: Proposal, Similarity Check, Assignment, ACC
     */
    public function test_thesis_lifecycle_and_advisor_assignment()
    {
        $student = User::factory()->create(['role' => 'mahasiswa', 'identifier' => '2106001']);
        $p1 = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Pembimbing 1']);
        $p2 = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Pembimbing 2']);
        $admin = User::factory()->create(['role' => 'admin']);

        // 3.1 Student proposal submission
        $response = $this->actingAs($student)->post(route('theses.store'), [
            'title' => 'Sistem Rekomendasi Pemilihan Topik Skripsi Menggunakan Algoritma AHP',
            'abstract' => 'Penelitian ini bertujuan untuk merancang sistem pendukung keputusan...',
            'topic' => 'SPK',
            'pembimbing1_id' => $p1->id,
            'pembimbing2_id' => $p2->id,
        ]);
        $response->assertRedirect();

        $thesis = Thesis::where('student_id', $student->id)->first();
        $this->assertNotNull($thesis);
        $this->assertEquals('pending', $thesis->status);

        // 3.2 Title similarity check
        $checkRes = $this->actingAs($student)->postJson(route('theses.check-title'), [
            'title' => 'Sistem Rekomendasi Pemilihan Topik Skripsi Berbasis AHP',
        ]);
        $checkRes->assertStatus(200);
        $checkRes->assertJsonStructure(['similar']);

        // 3.3 Admin assigns Pembimbing & approves
        $assignRes = $this->actingAs($admin)->post(route('theses.assign', $thesis->id), [
            'pembimbing1_id' => $p1->id,
            'pembimbing2_id' => $p2->id,
            'status' => 'active',
        ]);
        $assignRes->assertRedirect();
        $thesis->refresh();
        $this->assertEquals('active', $thesis->status);
        $this->assertEquals($p1->id, $thesis->pembimbing1_id);
        $this->assertEquals($p2->id, $thesis->pembimbing2_id);

        // 3.4 P1 toggles ACC Seminar UP
        $accRes = $this->actingAs($p1)->post(route('theses.toggle-acc', ['thesis' => $thesis->id, 'type' => 'up']));
        $accRes->assertRedirect();
        $thesis->refresh();
        $this->assertTrue((bool)$thesis->acc_up_p1);

        // 3.5 P1 toggles ACC Sidang
        $accSidangRes = $this->actingAs($p1)->post(route('theses.toggle-acc', ['thesis' => $thesis->id, 'type' => 'sidang']));
        $accSidangRes->assertRedirect();
        $thesis->refresh();
        $this->assertTrue((bool)$thesis->acc_sidang_p1);
    }

    /**
     * 4. Mentoring Sessions, Live Attendance & Feedback Documents
     */
    public function test_mentoring_attendance_and_feedback_flow()
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $dosen = User::factory()->create(['role' => 'dosen']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Implementasi IoT untuk Smart Garden',
            'status' => 'active',
            'pembimbing1_id' => $dosen->id,
        ]);

        // 4.1 Dosen creates mentoring session
        $this->actingAs($dosen)->post(route('mentoring-sessions.store'), [
            'thesis_id' => $thesis->id,
            'scheduled_at' => now()->addDay()->format('Y-m-d H:i'),
            'type' => 'offline',
            'location' => 'Ruang Dosen FIK 201',
            'topic' => 'Diskusi Metodologi dan Flowchart',
            'notes' => 'Siapkan diagram blok rangkaian sensor',
        ])->assertRedirect();

        $session = MentoringSession::where('thesis_id', $thesis->id)->first();
        $this->assertNotNull($session);
        $this->assertEquals('approved', $session->status);

        // 4.2 Student confirms attendance
        $this->actingAs($student)->post(route('mentoring-sessions.confirm-attendance', $session->id), [
            'status' => 'attending',
        ])->assertRedirect();

        $session->refresh();
        $this->assertEquals('attending', $session->student_attendance_status);

        // 4.3 Dosen completes session with feedback
        $this->actingAs($dosen)->patch(route('mentoring-sessions.status', $session->id), [
            'status' => 'completed',
            'feedback' => 'Bagus, lanjutkan perancangan database.',
        ])->assertRedirect();

        $session->refresh();
        $this->assertEquals('completed', $session->status);
        $this->assertEquals('Bagus, lanjutkan perancangan database.', $session->feedback);

        // 4.4 Quick preview logbook returns completed session
        $preview = $this->actingAs($dosen)->getJson(route('theses.logbooks.quick-preview', $thesis->id));
        $preview->assertStatus(200);
        $preview->assertJson([
            'total_completed' => 1,
        ]);
    }

    /**
     * 5. Examination (Seminar Proposal), Multi-Examiner Grading & Revisions
     */
    public function test_seminar_application_and_examiner_grading_flow()
    {
        $student = User::factory()->create(['role' => 'mahasiswa', 'identifier' => '2106099']);
        $p1 = User::factory()->create(['role' => 'dosen']);
        $examiner1 = User::factory()->create(['role' => 'dosen', 'name' => 'Penguji Satu']);
        $examiner2 = User::factory()->create(['role' => 'dosen', 'name' => 'Penguji Dua']);
        $admin = User::factory()->create(['role' => 'admin']);

        $wave = Wave::create([
            'name' => 'Gelombang 1 2026',
            'is_active' => true,
            'start_date' => now()->subMonth(),
            'end_date' => now()->addMonth(),
        ]);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Audit Keamanan Aplikasi Web SIBIMA',
            'status' => 'active',
            'pembimbing1_id' => $p1->id,
            'acc_up_p1' => true,
        ]);

        // 5.1 Create application directly and validate
        $app = SeminarApplication::create([
            'thesis_id' => $thesis->id,
            'file_acc_pembimbing' => 'dummy/acc.pdf',
            'file_pembayaran' => 'dummy/pay.pdf',
            'file_kartu_bimbingan' => 'dummy/card.pdf',
            'file_skripsi' => 'dummy/thesis.pdf',
            'status' => 'pending',
        ]);
        $this->assertNotNull($app);

        // 5.2 Admin validates application
        $this->actingAs($admin)->patch(route('seminar-applications.validate', $app->id), [
            'status' => 'approved',
            'admin_feedback' => 'Berkas lengkap dan memenuhi syarat.',
        ])->assertRedirect();
        $app->refresh();
        $this->assertEquals('approved', $app->status);

        // 5.3 Create schedule & schedule detail
        $schedule = SeminarSchedule::create([
            'title' => 'SEMESTER GENAP GELOMBANG 2 TAHUN AKADEMIK 2025/2026',
            'date' => now()->addDays(2)->toDateString(),
            'chairman_id' => $admin->id,
            'moderator_id' => $p1->id,
            'location' => 'Lab Komputer 1',
            'created_by' => $admin->id,
        ]);

        $detail = SeminarScheduleDetail::create([
            'seminar_schedule_id' => $schedule->id,
            'thesis_id' => $thesis->id,
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
            'examiner1_id' => $examiner1->id,
            'examiner2_id' => $examiner2->id,
            'order' => 1,
        ]);

        // 5.4 Examiner 1 grades the presentation
        $gradeRes = $this->actingAs($examiner1)->post(route('seminar-examiner.store-grading', $detail->id), [
            'score_presentation' => 85,
            'score_explanation' => 90,
            'score_writing' => 80,
        ]);
        $gradeRes->assertRedirect();

        $revision = SeminarRevision::where('seminar_schedule_detail_id', $detail->id)
            ->where('examiner_id', $examiner1->id)
            ->first();
        $this->assertNotNull($revision);
        $this->assertEquals(85, $revision->score_presentation);
        $this->assertEquals(90, $revision->score_explanation);
        $this->assertEquals(80, $revision->score_writing);

        // Total score calculation check: 85*0.25 (21.25) + 90*0.4 (36) + 80*0.35 (28) = 85.25
        $this->assertEquals(85.25, $revision->total_score);

        // 5.5 Examiner 1 approves revision direct
        $this->actingAs($examiner1)->post(route('seminar-examiner.approve-revision-direct', $detail->id))
            ->assertRedirect();
        $revision->refresh();
        $this->assertEquals('approved', $revision->status);
    }

    /**
     * 6. Graduation Clearance & Digital SKL Generation
     */
    public function test_graduation_clearance_and_skl_generation()
    {
        $student = User::factory()->create(['role' => 'mahasiswa', 'identifier' => '2106044']);
        $admin = User::factory()->create(['role' => 'admin']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Rancang Bangun Marketplace Desa',
            'status' => 'completed',
        ]);

        // 6.1 Student applies for Bebas Tanggungan
        $this->actingAs($student)->post(route('student.graduation.store'), [
            'final_thesis_file' => 'https://drive.google.com/file/d/dummy-thesis/view',
            'journal_article_file' => 'https://drive.google.com/file/d/dummy-journal/view',
            'plagiarism_file' => 'https://drive.google.com/file/d/dummy-plagiarism/view',
            'student_notes' => 'Semua berkas bebas tanggungan sudah diunggah.',
        ])->assertRedirect();

        $graduation = Graduation::where('student_id', $student->id)->first();
        $this->assertNotNull($graduation);
        $this->assertEquals('pending', $graduation->status);

        // 6.2 Admin verifies and approves SKL
        LetterSetting::create([
            'type' => 'skl',
            'title' => 'Surat Keterangan Lulus',
            'format' => '[NUMBER]/SKL/FIK-US/[ROMAN_MONTH]/[YEAR]',
            'last_number' => 10,
        ]);

        $this->actingAs($admin)->post(route('graduations.verify', $graduation->id), [
            'action' => 'approve',
            'graduation_date' => now()->toDateString(),
            'gpa' => 3.92,
            'predicate' => 'Dengan Pujian (Cum Laude)',
        ])->assertRedirect();

        $graduation->refresh();
        $this->assertEquals('approved', $graduation->status);
        $this->assertEquals('Dengan Pujian (Cum Laude)', $graduation->predicate);
        $this->assertNotEmpty($graduation->skl_number);
        $this->assertNotEmpty($graduation->verification_token);
    }

    /**
     * 7. Advisor Decrees (Surat Tugas BKD) Generation
     */
    public function test_advisor_duty_letter_generation()
    {
        $dosen = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Pembimbing BKD']);
        $student = User::factory()->create(['role' => 'mahasiswa', 'identifier' => '2106088']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Analisis Sentimen Ulasan Kampus Menggunakan BERT',
            'status' => 'active',
            'pembimbing1_id' => $dosen->id,
        ]);

        LetterSetting::create([
            'type' => 'surat_tugas_pembimbing',
            'title' => 'Surat Tugas BKD',
            'format' => '[NUMBER]/PD.1.2/FIK-US/[ROMAN_MONTH]/[YEAR]',
            'last_number' => 100,
        ]);

        $this->actingAs($dosen)->post(route('advisor-decrees.generate-surat-tugas'), [
            'academic_year' => '2025/2026',
            'semester' => 'Ganjil',
            'decree_date' => now()->toDateString(),
            'selected_theses' => [$thesis->id],
        ])->assertRedirect();

        $decree = AdvisorDecree::where('dosen_id', $dosen->id)->first();
        $this->assertNotNull($decree);
        $this->assertEquals('individual_dosen', $decree->target_type);
        $this->assertEquals(1, $decree->total_students);
        $this->assertStringContainsString('PD.1.2/FIK-US', $decree->decree_number);
    }

    /**
     * 8. Journal AI Keyword Extraction & Bookmarking
     */
    public function test_journal_ai_service_and_bookmarking()
    {
        $user = User::factory()->create(['role' => 'mahasiswa']);
        $aiService = app(JournalAiService::class);

        // 8.1 Keyword extraction from real title
        $keywords = $aiService->extractKeywords('Rancang Bangun Sistem Informasi Inventory Berbasis Web Menggunakan Metode Waterfall');
        $this->assertIsArray($keywords);
        $this->assertNotEmpty($keywords['query']);
        // Stopwords like 'rancang bangun', 'sistem informasi', 'berbasis web' should be cleaned
        $this->assertStringNotContainsString('rancang bangun', strtolower($keywords['query']));
        $this->assertStringContainsString('inventory', strtolower($keywords['query']));

        // 8.2 Bookmark folder creation
        $this->actingAs($user)->postJson(route('repositories.bookmarks.folders.store'), [
            'name' => 'Referensi Bab 2 Pustaka',
            'color' => 'indigo',
        ])->assertStatus(200);

        $folder = JournalBookmarkFolder::where('user_id', $user->id)->first();
        $this->assertNotNull($folder);
        $this->assertEquals('Referensi Bab 2 Pustaka', $folder->name);

        // 8.3 Toggle journal bookmark
        $this->actingAs($user)->post(route('repositories.bookmarks.toggle'), [
            'journal_identifier' => 'doi:10.1016/j.procs.2026.01.001',
            'title' => 'Machine Learning for Academic Progress Forecasting',
            'authors' => ['Akbar, B. A.'],
            'year' => 2026,
            'venue' => 'Procedia Computer Science',
            'source' => 'crossref',
        ])->assertStatus(200);

        $bookmark = JournalBookmark::where('user_id', $user->id)->first();
        $this->assertNotNull($bookmark);
        $this->assertEquals('Machine Learning for Academic Progress Forecasting', $bookmark->title);

        // 8.4 Assign bookmark to folder
        $this->actingAs($user)->patchJson(route('repositories.bookmarks.assign_folder', $bookmark->id), [
            'folder_id' => $folder->id,
        ])->assertStatus(200);
        $bookmark->refresh();
        $this->assertEquals($folder->id, $bookmark->folder_id);
    }

    /**
     * 9. Bug Reporting & Administration Lifecycle
     */
    public function test_bug_reporting_lifecycle()
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $admin = User::factory()->create(['role' => 'admin']);

        // 9.1 Student reports bug
        $this->actingAs($student)->post(route('bug-reports.store'), [
            'title' => 'Tombol Cetak PDF Tidak Berfungsi di Safari',
            'category' => 'functionality',
            'severity' => 'medium',
            'page_url' => 'http://localhost/theses/kanban',
            'description' => 'Saat tombol cetak diklik, modal tidak muncul.',
            'steps_to_reproduce' => '1. Buka safari. 2. Klik cetak.',
        ])->assertRedirect();

        $bug = BugReport::where('user_id', $student->id)->first();
        $this->assertNotNull($bug);
        $this->assertEquals('open', $bug->status);

        // 9.2 Admin updates bug status to resolved
        $this->actingAs($admin)->patch(route('admin.bug-reports.update-status', $bug->id), [
            'status' => 'resolved',
            'admin_notes' => 'Sudah diperbaiki dan kompatibel dengan Safari.',
        ])->assertRedirect();

        $bug->refresh();
        $this->assertEquals('resolved', $bug->status);
        $this->assertEquals($admin->id, $bug->resolved_by);
    }
}
