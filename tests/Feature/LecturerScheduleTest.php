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
}
