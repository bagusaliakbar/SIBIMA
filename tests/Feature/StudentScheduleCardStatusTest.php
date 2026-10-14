<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Thesis;
use App\Models\Wave;
use App\Models\SeminarSchedule;
use App\Models\SeminarScheduleDetail;
use App\Models\SeminarApplication;
use App\Models\ThesisDefenseSchedule;
use App\Models\ThesisDefenseScheduleDetail;
use App\Models\ThesisDefenseApplication;
use App\Models\SeminarRevision;
use App\Models\ThesisDefenseRevision;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentScheduleCardStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_upcoming_seminar_shows_terjadwal_on_student_dashboard()
    {
        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Budi Santoso']);
        $p1 = User::factory()->create(['role' => 'dosen', 'name' => 'Pembimbing 1']);
        $p2 = User::factory()->create(['role' => 'dosen', 'name' => 'Pembimbing 2']);
        $u1 = User::factory()->create(['role' => 'dosen', 'name' => 'Penguji 1']);
        $u2 = User::factory()->create(['role' => 'dosen', 'name' => 'Penguji 2']);
        $admin = User::factory()->create(['role' => 'admin']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Rancang Bangun Sistem Informasi',
            'status' => 'active',
            'pembimbing1_id' => $p1->id,
            'pembimbing2_id' => $p2->id,
            'is_acc_seminar_p1' => true,
            'is_acc_seminar_p2' => true,
            'acc_up_p1' => true,
            'acc_up_p2' => true,
        ]);

        $wave = Wave::create([
            'name' => 'Gelombang 1',
            'is_active' => true,
            'start_date' => now()->subDays(1),
            'end_date' => now()->addDays(10),
        ]);

        SeminarApplication::create([
            'thesis_id' => $thesis->id,
            'wave_id' => $wave->id,
            'status' => 'approved',
            'file_acc_pembimbing' => 'seminar/acc.pdf',
            'file_pembayaran' => 'seminar/pay.pdf',
            'file_kartu_bimbingan' => 'seminar/card.pdf',
            'file_skripsi' => 'seminar/thesis.pdf',
            'file_formulir' => 'seminar/form.pdf',
        ]);

        // Schedule 2 days in the future
        $futureDate = now()->addDays(2)->toDateString();
        $schedule = SeminarSchedule::create([
            'title' => 'Seminar Proposal Gelombang 1',
            'date' => $futureDate,
            'location' => 'Ruang 8',
            'meeting_link' => 'https://meet.google.com/test-seminar',
            'wave_id' => $wave->id,
            'chairman_id' => $p1->id,
            'moderator_id' => $p2->id,
            'created_by' => $admin->id,
        ]);

        $detail = SeminarScheduleDetail::create([
            'seminar_schedule_id' => $schedule->id,
            'thesis_id' => $thesis->id,
            'activity_name' => 'Seminar Proposal',
            'start_time' => now()->addDays(2)->setTime(8, 0),
            'end_time' => now()->addDays(2)->setTime(8, 45),
            'examiner1_id' => $u1->id,
            'examiner2_id' => $u2->id,
            'order' => 1,
        ]);

        $response = $this->actingAs($student)->get(route('dashboard'));
        $response->assertStatus(200);

        // Must show Terjadwal, NOT Selesai badge
        $response->assertSee('Jadwal Seminar Skripsi');
        $response->assertSee('Terjadwal');
        $response->assertDontSee('bg-emerald-100 text-emerald-700 text-[9px] font-extrabold rounded uppercase tracking-widest border border-emerald-200">Selesai</span>', false);
        $response->assertSee('Gabung Sekarang');
    }

    public function test_ongoing_seminar_shows_berlangsung_on_student_dashboard()
    {
        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Budi Santoso']);
        $p1 = User::factory()->create(['role' => 'dosen']);
        $p2 = User::factory()->create(['role' => 'dosen']);
        $u1 = User::factory()->create(['role' => 'dosen']);
        $u2 = User::factory()->create(['role' => 'dosen']);
        $admin = User::factory()->create(['role' => 'admin']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Rancang Bangun Sistem Informasi',
            'status' => 'active',
            'pembimbing1_id' => $p1->id,
            'pembimbing2_id' => $p2->id,
            'is_acc_seminar_p1' => true,
            'is_acc_seminar_p2' => true,
        ]);

        $wave = Wave::create([
            'name' => 'Gelombang 1',
            'is_active' => true,
            'start_date' => now()->subDays(5),
            'end_date' => now()->addDays(5),
        ]);

        $schedule = SeminarSchedule::create([
            'title' => 'Seminar Proposal Gelombang 1',
            'date' => now()->toDateString(),
            'location' => 'Ruang 8',
            'meeting_link' => 'https://meet.google.com/test-seminar',
            'wave_id' => $wave->id,
            'chairman_id' => $p1->id,
            'moderator_id' => $p2->id,
            'created_by' => $admin->id,
        ]);

        SeminarScheduleDetail::create([
            'seminar_schedule_id' => $schedule->id,
            'thesis_id' => $thesis->id,
            'activity_name' => 'Seminar Proposal',
            'start_time' => now()->subMinutes(15),
            'end_time' => now()->addMinutes(30),
            'examiner1_id' => $u1->id,
            'examiner2_id' => $u2->id,
            'order' => 1,
        ]);

        $response = $this->actingAs($student)->get(route('dashboard'));
        $response->assertStatus(200);

        // Must show Berlangsung
        $response->assertSee('Jadwal Seminar Skripsi');
        $response->assertSee('Berlangsung');
        $response->assertSee('Gabung Sekarang (Sedang Berlangsung)');
    }

    public function test_finished_seminar_shows_selesai_on_student_dashboard()
    {
        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Budi Santoso']);
        $p1 = User::factory()->create(['role' => 'dosen']);
        $p2 = User::factory()->create(['role' => 'dosen']);
        $u1 = User::factory()->create(['role' => 'dosen']);
        $u2 = User::factory()->create(['role' => 'dosen']);
        $admin = User::factory()->create(['role' => 'admin']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Rancang Bangun Sistem Informasi',
            'status' => 'active',
            'pembimbing1_id' => $p1->id,
            'pembimbing2_id' => $p2->id,
            'is_acc_seminar_p1' => true,
            'is_acc_seminar_p2' => true,
        ]);

        $wave = Wave::create([
            'name' => 'Gelombang 1',
            'is_active' => true,
            'start_date' => now()->subDays(5),
            'end_date' => now()->addDays(5),
        ]);

        $pastDate = now()->subDays(2)->toDateString();
        $schedule = SeminarSchedule::create([
            'title' => 'Seminar Proposal Gelombang 1',
            'date' => $pastDate,
            'location' => 'Ruang 8',
            'meeting_link' => 'https://meet.google.com/test-seminar',
            'wave_id' => $wave->id,
            'chairman_id' => $p1->id,
            'moderator_id' => $p2->id,
            'created_by' => $admin->id,
        ]);

        $detail = SeminarScheduleDetail::create([
            'seminar_schedule_id' => $schedule->id,
            'thesis_id' => $thesis->id,
            'activity_name' => 'Seminar Proposal',
            'start_time' => now()->subDays(2)->setTime(8, 0),
            'end_time' => now()->subDays(2)->setTime(8, 45),
            'examiner1_id' => $u1->id,
            'examiner2_id' => $u2->id,
            'order' => 1,
        ]);

        // Add revisions / scores
        SeminarRevision::create([
            'seminar_schedule_detail_id' => $detail->id,
            'examiner_id' => $u1->id,
            'score_presentation' => 85,
            'score_explanation' => 85,
            'score_writing' => 85,
            'status' => 'approved',
        ]);
        SeminarRevision::create([
            'seminar_schedule_detail_id' => $detail->id,
            'examiner_id' => $u2->id,
            'score_presentation' => 90,
            'score_explanation' => 90,
            'score_writing' => 90,
            'status' => 'approved',
        ]);

        $response = $this->actingAs($student)->get(route('dashboard'));
        $response->assertStatus(200);

        // Must show Selesai
        $response->assertSee('Jadwal Seminar Skripsi');
        $response->assertSee('bg-emerald-100 text-emerald-700 text-[9px] font-extrabold rounded uppercase tracking-widest border border-emerald-200">Selesai</span>', false);
        $response->assertDontSee('Gabung Sekarang');
    }

    public function test_upcoming_defense_shows_terjadwal_on_student_dashboard()
    {
        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Budi Santoso']);
        $p1 = User::factory()->create(['role' => 'dosen']);
        $p2 = User::factory()->create(['role' => 'dosen']);
        $u1 = User::factory()->create(['role' => 'dosen']);
        $u2 = User::factory()->create(['role' => 'dosen']);
        $admin = User::factory()->create(['role' => 'admin']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Rancang Bangun Sistem Informasi',
            'status' => 'active',
            'pembimbing1_id' => $p1->id,
            'pembimbing2_id' => $p2->id,
            'is_acc_sidang_p1' => true,
            'is_acc_sidang_p2' => true,
        ]);

        $wave = Wave::create([
            'name' => 'Gelombang 1',
            'is_active' => true,
            'start_date' => now()->subDays(1),
            'end_date' => now()->addDays(10),
        ]);

        ThesisDefenseApplication::create([
            'thesis_id' => $thesis->id,
            'wave_id' => $wave->id,
            'status' => 'approved',
            'file_formulir' => 'defense/form.pdf',
            'file_transkrip' => 'defense/transkrip.pdf',
            'file_acc_pembimbing' => 'defense/acc.pdf',
            'file_logbook' => 'defense/log.pdf',
            'file_pembayaran' => 'defense/pay.pdf',
            'file_skripsi' => 'defense/thesis.pdf',
            'file_ktm' => 'defense/ktm.pdf',
            'file_pkkmb_univ' => 'defense/pkkmb.pdf',
            'file_pkkmb_fak' => 'defense/pkkmb_fak.pdf',
            'file_makrab' => 'defense/makrab.pdf',
            'file_cisco' => 'defense/cisco.pdf',
            'file_workshop' => 'defense/workshop.pdf',
            'file_organisasi' => 'defense/org.pdf',
            'file_toefl' => 'defense/toefl.pdf',
            'file_kewirausahaan' => 'defense/kwu.pdf',
            'file_tahsin' => 'defense/tahsin.pdf',
            'file_komputer' => 'defense/komputer.pdf',
            'file_perpus_pinjam' => 'defense/perpus_pinjam.pdf',
            'file_perpus_sumbang' => 'defense/perpus_sumbang.pdf',
            'file_ijazah' => 'defense/ijazah.pdf',
        ]);

        $futureDate = now()->addDays(3)->toDateString();
        $schedule = ThesisDefenseSchedule::create([
            'title' => 'Sidang Skripsi Gelombang 1',
            'date' => $futureDate,
            'location' => 'Ruang Sidang 1',
            'meeting_link' => 'https://meet.google.com/test-defense',
            'wave_id' => $wave->id,
            'chairman_id' => $p1->id,
            'moderator_id' => $p2->id,
            'created_by' => $admin->id,
        ]);

        ThesisDefenseScheduleDetail::create([
            'thesis_defense_schedule_id' => $schedule->id,
            'thesis_id' => $thesis->id,
            'activity_name' => 'Ujian Sidang',
            'start_time' => now()->addDays(3)->setTime(10, 0),
            'end_time' => now()->addDays(3)->setTime(11, 0),
            'examiner1_id' => $u1->id,
            'examiner2_id' => $u2->id,
            'order' => 1,
        ]);

        $response = $this->actingAs($student)->get(route('dashboard'));
        $response->assertStatus(200);

        // Must show Terjadwal, NOT Selesai
        $response->assertSee('Jadwal Sidang Skripsi');
        $response->assertSee('Terjadwal');
        $response->assertDontSee('bg-emerald-100 text-emerald-700 text-[9px] font-extrabold rounded uppercase tracking-widest border border-emerald-200">Selesai</span>', false);
        $response->assertSee('Join Online Meeting');
    }
}
