<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Thesis;
use App\Models\Wave;
use App\Models\SeminarSchedule;
use App\Models\SeminarScheduleDetail;
use App\Models\ThesisDefenseSchedule;
use App\Models\ThesisDefenseScheduleDetail;
use App\Models\SeminarRevision;
use App\Models\ThesisDefenseRevision;
use App\Models\SeminarApplication;
use App\Models\ThesisDefenseApplication;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LecturerScheduleTest extends TestCase
{
    use RefreshDatabase;

    public function test_finished_seminar_shows_selesai_status_on_lecturer_dashboard_and_examiner_page()
    {
        // 1. Setup Lecturer & Student
        $dosen = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Penguji']);
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Sistem Informasi Monitoring SPPG',
            'status' => 'active',
        ]);

        // Create Wave
        $wave = Wave::create([
            'name' => 'Gelombang 1',
            'is_active' => true,
            'start_date' => now()->subDays(1),
            'end_date' => now()->addDays(5),
        ]);

        $otherDosen = User::factory()->create(['role' => 'dosen']);
        $admin = User::factory()->create(['role' => 'admin']);

        // 2. Create Seminar Schedule & Detail
        $schedule = SeminarSchedule::create([
            'title' => 'Seminar Proposal Gelombang 1',
            'date' => now()->toDateString(),
            'location' => 'Online Channel 1',
            'meeting_link' => 'https://meet.google.com/abc-defg-hij',
            'wave_id' => $wave->id,
            'chairman_id' => $dosen->id,
            'moderator_id' => $otherDosen->id,
            'created_by' => $admin->id,
        ]);

        $detail = SeminarScheduleDetail::create([
            'seminar_schedule_id' => $schedule->id,
            'thesis_id' => $thesis->id,
            'activity_name' => 'Seminar Proposal',
            'start_time' => now()->subHours(2),
            'end_time' => now()->subHours(1),
            'examiner1_id' => $dosen->id,
            'examiner2_id' => $otherDosen->id,
            'order' => 1,
        ]);

        $response = $this->actingAs($dosen)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertDontSee($thesis->student->name);

        // 4. Visit Seminar Examiner page
        $response = $this->actingAs($dosen)->get(route('seminar-examiner.index'));
        $response->assertStatus(200);
        $response->assertSee('Selesai');
        $response->assertDontSee('Klik Buka Link');
    }

    public function test_finished_defense_shows_selesai_status_on_lecturer_dashboard_and_examiner_page()
    {
        // 1. Setup Lecturer & Student
        $dosen = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Penguji']);
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Sistem Informasi Monitoring SPPG',
            'status' => 'active',
        ]);

        // Create Wave
        $wave = Wave::create([
            'name' => 'Gelombang 1',
            'is_active' => true,
            'start_date' => now()->subDays(1),
            'end_date' => now()->addDays(5),
        ]);

        $otherDosen = User::factory()->create(['role' => 'dosen']);
        $admin = User::factory()->create(['role' => 'admin']);

        // 2. Create Defense Schedule & Detail
        $schedule = ThesisDefenseSchedule::create([
            'title' => 'Sidang Akhir Gelombang 1',
            'date' => now()->toDateString(),
            'location' => 'Online Channel 1',
            'meeting_link' => 'https://meet.google.com/abc-defg-hij',
            'wave_id' => $wave->id,
            'chairman_id' => $dosen->id,
            'moderator_id' => $otherDosen->id,
            'created_by' => $admin->id,
        ]);

        $detail = ThesisDefenseScheduleDetail::create([
            'thesis_defense_schedule_id' => $schedule->id,
            'thesis_id' => $thesis->id,
            'activity_name' => 'Ujian Sidang',
            'start_time' => now()->subHours(2),
            'end_time' => now()->subHours(1),
            'examiner1_id' => $dosen->id,
            'examiner2_id' => $otherDosen->id,
            'order' => 1,
        ]);

        // 3. Act as Dosen, visit Dashboard
        $response = $this->actingAs($dosen)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertDontSee($thesis->student->name);

        // 4. Visit Defense Examiner page
        $response = $this->actingAs($dosen)->get(route('defense-examiner.index'));
        $response->assertStatus(200);
        $response->assertSee('Selesai');
        $response->assertDontSee('Klik Buka Link');
    }

    public function test_seminar_in_future_with_approved_revisions_shows_selesai_status()
    {
        // 1. Setup Lecturer & Student
        $dosen = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Penguji']);
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Sistem Informasi Monitoring SPPG',
            'status' => 'active',
            'pembimbing1_id' => User::factory()->create(['role' => 'dosen'])->id,
            'pembimbing2_id' => User::factory()->create(['role' => 'dosen'])->id,
        ]);

        $wave = Wave::create([
            'name' => 'Gelombang 1',
            'is_active' => true,
            'start_date' => now()->subDays(1),
            'end_date' => now()->addDays(5),
        ]);

        $otherDosen = User::factory()->create(['role' => 'dosen']);
        $admin = User::factory()->create(['role' => 'admin']);

        // 2. Create Seminar Schedule (in the future)
        $schedule = SeminarSchedule::create([
            'title' => 'Seminar Proposal Gelombang 1',
            'date' => now()->addDays(3)->toDateString(),
            'location' => 'Online Channel 1',
            'meeting_link' => 'https://meet.google.com/abc-defg-hij',
            'wave_id' => $wave->id,
            'chairman_id' => $dosen->id,
            'moderator_id' => $otherDosen->id,
            'created_by' => $admin->id,
        ]);

        $detail = SeminarScheduleDetail::create([
            'seminar_schedule_id' => $schedule->id,
            'thesis_id' => $thesis->id,
            'activity_name' => 'Seminar Proposal',
            'start_time' => now()->addDays(3)->setTime(9, 0),
            'end_time' => now()->addDays(3)->setTime(10, 0),
            'examiner1_id' => $dosen->id,
            'examiner2_id' => $otherDosen->id,
            'order' => 1,
        ]);

        // 3. Create approved revisions for both examiners
        SeminarRevision::create([
            'seminar_schedule_detail_id' => $detail->id,
            'examiner_id' => $dosen->id,
            'revision_notes' => 'Catatan revisi',
            'status' => 'approved',
        ]);
        SeminarRevision::create([
            'seminar_schedule_detail_id' => $detail->id,
            'examiner_id' => $otherDosen->id,
            'revision_notes' => 'Catatan revisi',
            'status' => 'approved',
        ]);

        // 4. Act as Dosen, visit dashboard
        $response = $this->actingAs($dosen)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertDontSee($thesis->student->name);

        // 5. Visit seminar examiner page
        $response = $this->actingAs($dosen)->get(route('seminar-examiner.index'));
        $response->assertStatus(200);
        $response->assertSee('Selesai');
        $response->assertDontSee('Klik Buka Link');
    }

    public function test_defense_in_future_with_approved_revisions_shows_selesai_status()
    {
        // 1. Setup Lecturer & Student
        $dosen = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Penguji']);
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $p1 = User::factory()->create(['role' => 'dosen']);
        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Sistem Informasi Monitoring SPPG',
            'status' => 'active',
            'pembimbing1_id' => $p1->id,
            'pembimbing2_id' => User::factory()->create(['role' => 'dosen'])->id,
        ]);

        $wave = Wave::create([
            'name' => 'Gelombang 1',
            'is_active' => true,
            'start_date' => now()->subDays(1),
            'end_date' => now()->addDays(5),
        ]);

        $otherDosen = User::factory()->create(['role' => 'dosen']);
        $admin = User::factory()->create(['role' => 'admin']);

        // 2. Create Defense Schedule (in the future)
        $schedule = ThesisDefenseSchedule::create([
            'title' => 'Sidang Akhir Gelombang 1',
            'date' => now()->addDays(3)->toDateString(),
            'location' => 'Online Channel 1',
            'meeting_link' => 'https://meet.google.com/abc-defg-hij',
            'wave_id' => $wave->id,
            'chairman_id' => $dosen->id,
            'moderator_id' => $otherDosen->id,
            'created_by' => $admin->id,
        ]);

        $detail = ThesisDefenseScheduleDetail::create([
            'thesis_defense_schedule_id' => $schedule->id,
            'thesis_id' => $thesis->id,
            'activity_name' => 'Ujian Sidang',
            'start_time' => now()->addDays(3)->setTime(9, 0),
            'end_time' => now()->addDays(3)->setTime(10, 0),
            'examiner1_id' => $dosen->id,
            'examiner2_id' => $otherDosen->id,
            'order' => 1,
        ]);

        // 3. Create approved revisions for required examiners (examiner1, examiner2, and pembimbing 1)
        ThesisDefenseRevision::create([
            'thesis_defense_schedule_detail_id' => $detail->id,
            'examiner_id' => $dosen->id,
            'revision_notes' => 'Catatan',
            'status' => 'approved',
        ]);
        ThesisDefenseRevision::create([
            'thesis_defense_schedule_detail_id' => $detail->id,
            'examiner_id' => $otherDosen->id,
            'revision_notes' => 'Catatan',
            'status' => 'approved',
        ]);
        ThesisDefenseRevision::create([
            'thesis_defense_schedule_detail_id' => $detail->id,
            'examiner_id' => $p1->id,
            'revision_notes' => 'Catatan',
            'status' => 'approved',
        ]);

        // 4. Act as Dosen, visit dashboard
        $response = $this->actingAs($dosen)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertDontSee($thesis->student->name);

        // 5. Visit defense examiner page
        $response = $this->actingAs($dosen)->get(route('defense-examiner.index'));
        $response->assertStatus(200);
        $response->assertSee('Selesai');
        $response->assertDontSee('Klik Buka Link');
    }

    public function test_only_active_wave_schedules_shown_on_lecturer_dashboard()
    {
        // 1. Setup Lecturer & Student
        $dosen = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Penguji']);
        $student1 = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mhs Active Wave']);
        $student2 = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mhs Inactive Wave']);
        
        $thesis1 = Thesis::create([
            'student_id' => $student1->id,
            'title' => 'Judul Skripsi Mhs 1',
            'status' => 'active',
        ]);
        $thesis2 = Thesis::create([
            'student_id' => $student2->id,
            'title' => 'Judul Skripsi Mhs 2',
            'status' => 'active',
        ]);

        // Active Wave
        $activeWave = Wave::create([
            'name' => 'Gelombang Aktif',
            'is_active' => true,
            'start_date' => now()->subDays(1),
            'end_date' => now()->addDays(5),
        ]);

        // Inactive Wave
        $inactiveWave = Wave::create([
            'name' => 'Gelombang Non-Aktif',
            'is_active' => false,
            'start_date' => now()->subDays(10),
            'end_date' => now()->subDays(5),
        ]);

        $otherDosen = User::factory()->create(['role' => 'dosen']);
        $admin = User::factory()->create(['role' => 'admin']);

        // Seminar for Active Wave (in future so it's not finished)
        $scheduleActive = SeminarSchedule::create([
            'title' => 'Seminar Proposal Gel Aktif',
            'date' => now()->addDays(2)->toDateString(),
            'location' => 'Online Channel 1',
            'wave_id' => $activeWave->id,
            'chairman_id' => $dosen->id,
            'moderator_id' => $otherDosen->id,
            'created_by' => $admin->id,
        ]);

        SeminarScheduleDetail::create([
            'seminar_schedule_id' => $scheduleActive->id,
            'thesis_id' => $thesis1->id,
            'activity_name' => 'Seminar Proposal',
            'start_time' => now()->addDays(2)->setTime(9, 0),
            'end_time' => now()->addDays(2)->setTime(10, 0),
            'examiner1_id' => $dosen->id,
            'examiner2_id' => $otherDosen->id,
            'order' => 1,
        ]);

        // Seminar for Inactive Wave (in future so it's not finished)
        $scheduleInactive = SeminarSchedule::create([
            'title' => 'Seminar Proposal Gel Non-Aktif',
            'date' => now()->addDays(2)->toDateString(),
            'location' => 'Online Channel 2',
            'wave_id' => $inactiveWave->id,
            'chairman_id' => $dosen->id,
            'moderator_id' => $otherDosen->id,
            'created_by' => $admin->id,
        ]);

        SeminarScheduleDetail::create([
            'seminar_schedule_id' => $scheduleInactive->id,
            'thesis_id' => $thesis2->id,
            'activity_name' => 'Seminar Proposal',
            'start_time' => now()->addDays(2)->setTime(11, 0),
            'end_time' => now()->addDays(2)->setTime(12, 0),
            'examiner1_id' => $dosen->id,
            'examiner2_id' => $otherDosen->id,
            'order' => 2,
        ]);

        // Act as Dosen, visit dashboard
        $response = $this->actingAs($dosen)->get(route('dashboard'));
        $response->assertStatus(200);
        
        // Should see schedule for active wave
        $response->assertSee('Mhs Active Wave');
        
        // Should NOT see schedule for inactive wave
        $response->assertDontSee('Mhs Inactive Wave');
    }

    public function test_seminar_examiner_page_displays_dokumen_skripsi_column_and_allows_examiner_access()
    {
        Storage::fake('local');
        $filePath = 'SeminarApplication_files/test_seminar_doc.pdf';
        Storage::disk(config('filesystems.default'))->put($filePath, 'sample content');

        $dosen = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Penguji 1']);
        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Seminar Test']);
        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Analisis Perancangan Sistem Cerdas',
            'status' => 'active',
        ]);

        $wave = Wave::create([
            'name' => 'Gelombang Seminar 1',
            'is_active' => true,
            'start_date' => now()->subDays(1),
            'end_date' => now()->addDays(5),
        ]);

        $admin = User::factory()->create(['role' => 'admin']);

        $application = SeminarApplication::create([
            'thesis_id' => $thesis->id,
            'wave_id' => $wave->id,
            'file_skripsi' => $filePath,
            'file_acc_pembimbing' => 'dummy.pdf',
            'file_pembayaran' => 'dummy.pdf',
            'file_kartu_bimbingan' => 'dummy.pdf',
            'file_formulir' => 'dummy.pdf',
            'status' => 'approved',
        ]);

        $schedule = SeminarSchedule::create([
            'title' => 'Jadwal Seminar Hari Ini',
            'date' => now()->toDateString(),
            'location' => 'Ruang 101',
            'wave_id' => $wave->id,
            'created_by' => $admin->id,
        ]);

        $detail = SeminarScheduleDetail::create([
            'seminar_schedule_id' => $schedule->id,
            'thesis_id' => $thesis->id,
            'activity_name' => 'Seminar Proposal',
            'start_time' => now()->setTime(10, 0),
            'end_time' => now()->setTime(11, 0),
            'examiner1_id' => $dosen->id,
            'order' => 1,
        ]);

        // 1. Visit seminar examiner index page
        $response = $this->actingAs($dosen)->get(route('seminar-examiner.index'));
        $response->assertStatus(200);
        $response->assertSee('Dokumen Skripsi');
        $response->assertSee('Dokumen');
        $response->assertSee(route('download.private', ['path' => $filePath, 'inline' => 1]));

        // 2. Examiner can download/view the file securely without 403
        $downloadResponse = $this->actingAs($dosen)->get(route('download.private', ['path' => $filePath, 'inline' => 1]));
        $downloadResponse->assertStatus(200);
    }

    public function test_defense_examiner_page_displays_dokumen_skripsi_column_and_allows_examiner_access()
    {
        Storage::fake('local');
        $filePath = 'ThesisDefenseApplication_files/test_defense_doc.pdf';
        Storage::disk(config('filesystems.default'))->put($filePath, 'sample defense content');

        $dosen = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Penguji Sidang 1']);
        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Sidang Test']);
        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Implementasi Machine Learning untuk Prediksi',
            'status' => 'active',
        ]);

        $wave = Wave::create([
            'name' => 'Gelombang Sidang 1',
            'is_active' => true,
            'start_date' => now()->subDays(1),
            'end_date' => now()->addDays(5),
        ]);

        $admin = User::factory()->create(['role' => 'admin']);

        $application = ThesisDefenseApplication::create([
            'thesis_id' => $thesis->id,
            'wave_id' => $wave->id,
            'file_skripsi' => $filePath,
            'file_formulir' => 'dummy.pdf',
            'file_transkrip' => 'dummy.pdf',
            'file_acc_pembimbing' => 'dummy.pdf',
            'file_logbook' => 'dummy.pdf',
            'file_pembayaran' => 'dummy.pdf',
            'file_ktm' => 'dummy.pdf',
            'file_pkkmb_univ' => 'dummy.pdf',
            'file_pkkmb_fak' => 'dummy.pdf',
            'file_makrab' => 'dummy.pdf',
            'file_cisco' => 'dummy.pdf',
            'file_workshop' => 'dummy.pdf',
            'file_organisasi' => 'dummy.pdf',
            'file_toefl' => 'dummy.pdf',
            'file_kewirausahaan' => 'dummy.pdf',
            'file_tahsin' => 'dummy.pdf',
            'file_komputer' => 'dummy.pdf',
            'file_perpus_pinjam' => 'dummy.pdf',
            'file_perpus_sumbang' => 'dummy.pdf',
            'file_ijazah' => 'dummy.pdf',
            'status' => 'approved',
        ]);

        $schedule = ThesisDefenseSchedule::create([
            'title' => 'Jadwal Sidang Hari Ini',
            'date' => now()->toDateString(),
            'location' => 'Ruang Sidang 2',
            'wave_id' => $wave->id,
            'created_by' => $admin->id,
        ]);

        $detail = ThesisDefenseScheduleDetail::create([
            'thesis_defense_schedule_id' => $schedule->id,
            'thesis_id' => $thesis->id,
            'activity_name' => 'Sidang Skripsi',
            'start_time' => now()->setTime(13, 0),
            'end_time' => now()->setTime(14, 0),
            'examiner1_id' => $dosen->id,
            'order' => 1,
        ]);

        // 1. Visit defense examiner index page
        $response = $this->actingAs($dosen)->get(route('defense-examiner.index'));
        $response->assertStatus(200);
        $response->assertSee('Dokumen Skripsi');
        $response->assertSee('Dokumen');
        $response->assertSee(route('download.private', ['path' => $filePath, 'inline' => 1]));

        // 2. Examiner can download/view the file securely without 403
        $downloadResponse = $this->actingAs($dosen)->get(route('download.private', ['path' => $filePath, 'inline' => 1]));
        $downloadResponse->assertStatus(200);
    }

    public function test_batch_pdf_export_seminar_schedules_downloads_successfully()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $dosen = User::factory()->create(['role' => 'dosen']);
        $student = User::factory()->create(['role' => 'mahasiswa']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Sistem Batch PDF Seminar',
            'status' => 'active',
        ]);

        $wave = Wave::create([
            'name' => 'Gelombang 1 2026/2027',
            'is_active' => true,
            'start_date' => now()->subDays(1),
            'end_date' => now()->addDays(5),
        ]);

        $schedule1 = SeminarSchedule::create([
            'title' => 'Seminar Proposal Ruang 8',
            'date' => now()->toDateString(),
            'location' => 'Ruang 8',
            'wave_id' => $wave->id,
            'chairman_id' => $dosen->id,
            'created_by' => $admin->id,
        ]);

        SeminarScheduleDetail::create([
            'seminar_schedule_id' => $schedule1->id,
            'thesis_id' => $thesis->id,
            'activity_name' => 'Seminar Proposal',
            'start_time' => now()->setTime(8, 0),
            'end_time' => now()->setTime(9, 0),
            'order' => 1,
        ]);

        $schedule2 = SeminarSchedule::create([
            'title' => 'Seminar Proposal Ruang 9',
            'date' => now()->toDateString(),
            'location' => 'Ruang 9',
            'wave_id' => $wave->id,
            'chairman_id' => $dosen->id,
            'created_by' => $admin->id,
        ]);

        // Test export batch PDF as admin
        $response = $this->actingAs($admin)->get(route('seminar-schedules.export-batch-pdf', [
            'wave_id' => $wave->id,
        ]));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));

        // Test export batch PDF as dosen
        $dosenResponse = $this->actingAs($dosen)->get(route('seminar-schedules.export-batch-pdf', [
            'wave_id' => $wave->id,
        ]));

        $dosenResponse->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $dosenResponse->headers->get('Content-Type'));

        // Test unassigned dosen can export individual schedule PDF without 403
        $unassignedDosen = User::factory()->create(['role' => 'dosen']);
        $singlePdfResponse = $this->actingAs($unassignedDosen)->get(route('seminar-schedules.export-pdf', $schedule1));
        $singlePdfResponse->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $singlePdfResponse->headers->get('Content-Type'));
    }

    public function test_batch_pdf_export_thesis_defense_schedules_downloads_successfully()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $dosen = User::factory()->create(['role' => 'dosen']);
        $student = User::factory()->create(['role' => 'mahasiswa']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Sistem Batch PDF Sidang',
            'status' => 'active',
        ]);

        $wave = Wave::create([
            'name' => 'Gelombang 1 2026/2027',
            'is_active' => true,
            'start_date' => now()->subDays(1),
            'end_date' => now()->addDays(5),
        ]);

        $schedule1 = ThesisDefenseSchedule::create([
            'title' => 'Sidang Skripsi Ruang 8',
            'date' => now()->toDateString(),
            'location' => 'Ruang 8',
            'wave_id' => $wave->id,
            'created_by' => $admin->id,
        ]);

        ThesisDefenseScheduleDetail::create([
            'thesis_defense_schedule_id' => $schedule1->id,
            'thesis_id' => $thesis->id,
            'activity_name' => 'Sidang Skripsi',
            'start_time' => now()->setTime(8, 0),
            'end_time' => now()->setTime(9, 30),
            'examiner1_id' => $dosen->id,
            'order' => 1,
        ]);

        $schedule2 = ThesisDefenseSchedule::create([
            'title' => 'Sidang Skripsi Ruang 9',
            'date' => now()->toDateString(),
            'location' => 'Ruang 9',
            'wave_id' => $wave->id,
            'created_by' => $admin->id,
        ]);

        // Test export batch PDF as admin
        $response = $this->actingAs($admin)->get(route('thesis-defense-schedules.export-batch-pdf', [
            'wave_id' => $wave->id,
        ]));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));

        // Test export batch PDF as dosen
        $dosenResponse = $this->actingAs($dosen)->get(route('thesis-defense-schedules.export-batch-pdf', [
            'wave_id' => $wave->id,
        ]));

        $dosenResponse->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $dosenResponse->headers->get('Content-Type'));

        // Test unassigned dosen can export individual schedule PDF without 403
        $unassignedDosen = User::factory()->create(['role' => 'dosen']);
        $singlePdfResponse = $this->actingAs($unassignedDosen)->get(route('thesis-defense-schedules.export-pdf', $schedule1));
        $singlePdfResponse->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $singlePdfResponse->headers->get('Content-Type'));
    }

    public function test_my_schedules_summary_only_shows_lecturers_students_and_exams()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $myDosen = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Penguji Saya']);
        $otherDosen = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Lain']);
        
        $student1 = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Bimbingan Saya']);
        $student2 = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Orang Lain']);

        $thesis1 = Thesis::create([
            'student_id' => $student1->id,
            'title' => 'Judul Skripsi Bimbingan Saya',
            'pembimbing1_id' => $myDosen->id,
            'status' => 'active',
        ]);

        $thesis2 = Thesis::create([
            'student_id' => $student2->id,
            'title' => 'Judul Skripsi Mahasiswa Lain',
            'pembimbing1_id' => $otherDosen->id,
            'status' => 'active',
        ]);

        $wave = Wave::create([
            'name' => 'Gelombang 1 2026/2027',
            'is_active' => true,
            'start_date' => now()->subDays(1),
            'end_date' => now()->addDays(5),
        ]);

        $schedule = SeminarSchedule::create([
            'title' => 'Seminar Proposal Ruang 8',
            'date' => now()->toDateString(),
            'location' => 'Ruang 8',
            'wave_id' => $wave->id,
            'chairman_id' => $otherDosen->id,
            'created_by' => $admin->id,
        ]);

        // Detail 1: My student
        SeminarScheduleDetail::create([
            'seminar_schedule_id' => $schedule->id,
            'thesis_id' => $thesis1->id,
            'activity_name' => 'Seminar Proposal',
            'start_time' => now()->setTime(8, 0),
            'end_time' => now()->setTime(9, 0),
            'examiner1_id' => $otherDosen->id,
            'order' => 1,
        ]);

        // Detail 2: Other student (not examined or mentored by myDosen)
        SeminarScheduleDetail::create([
            'seminar_schedule_id' => $schedule->id,
            'thesis_id' => $thesis2->id,
            'activity_name' => 'Seminar Proposal',
            'start_time' => now()->setTime(9, 0),
            'end_time' => now()->setTime(10, 0),
            'examiner1_id' => $otherDosen->id,
            'order' => 2,
        ]);

        // 1. Web view with my_schedules=1: should see thesis1, should NOT see thesis2
        $response = $this->actingAs($myDosen)->get(route('seminar-schedules.index', [
            'my_schedules' => 1,
            'filter_date' => 'all',
        ]));
        $response->assertStatus(200);
        $response->assertSee('Mahasiswa Bimbingan Saya');
        $response->assertSee('Judul Skripsi Bimbingan Saya');
        $response->assertDontSee('Mahasiswa Orang Lain');
        $response->assertDontSee('Judul Skripsi Mahasiswa Lain');

        // 2. Single PDF export with my_schedules=1: should succeed (200)
        $singlePdf = $this->actingAs($myDosen)->get(route('seminar-schedules.export-pdf', [
            'seminar_schedule' => $schedule->id,
            'my_schedules' => 1,
        ]));
        $singlePdf->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $singlePdf->headers->get('Content-Type'));

        // 3. Batch PDF export with my_schedules=1: should succeed (200)
        $batchPdf = $this->actingAs($myDosen)->get(route('seminar-schedules.export-batch-pdf', [
            'wave_id' => $wave->id,
            'my_schedules' => 1,
        ]));
        $batchPdf->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $batchPdf->headers->get('Content-Type'));
    }
}
