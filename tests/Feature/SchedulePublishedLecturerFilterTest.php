<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Thesis;
use App\Models\SeminarSchedule;
use App\Models\SeminarScheduleDetail;
use App\Notifications\SchedulePublished;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SchedulePublishedLecturerFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_lecturer_only_receives_their_own_students_in_wa_notification()
    {
        $this->seed(\Database\Seeders\WaTemplateSeeder::class);
        // 3 Lecturers
        $dosenA = User::factory()->create(['role' => 'dosen', 'name' => 'Bpk Dosen A']);
        $dosenB = User::factory()->create(['role' => 'dosen', 'name' => 'Ibu Dosen B']);
        $dosenC = User::factory()->create(['role' => 'dosen', 'name' => 'Bpk Dosen C']);

        // 2 Students
        $mhs1 = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Satu']);
        $mhs2 = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Dua']);

        // Thesis 1: Mhs1, Pembimbing = Dosen B, Penguji = Dosen A
        $thesis1 = Thesis::create([
            'student_id' => $mhs1->id,
            'title' => 'Judul Skripsi 1',
            'pembimbing1_id' => $dosenB->id,
            'status' => 'active'
        ]);

        // Thesis 2: Mhs2, Pembimbing = Dosen A, Penguji = Dosen C
        $thesis2 = Thesis::create([
            'student_id' => $mhs2->id,
            'title' => 'Judul Skripsi 2',
            'pembimbing1_id' => $dosenA->id,
            'status' => 'active'
        ]);

        $schedule = SeminarSchedule::create([
            'title' => 'Seminar Proposal Gelombang 1',
            'date' => '2026-08-15',
            'location' => 'Lab Komputer A',
            'created_by' => $dosenA->id,
        ]);

        SeminarScheduleDetail::create([
            'seminar_schedule_id' => $schedule->id,
            'thesis_id' => $thesis1->id,
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'examiner1_id' => $dosenA->id,
            'order' => 1
        ]);

        SeminarScheduleDetail::create([
            'seminar_schedule_id' => $schedule->id,
            'thesis_id' => $thesis2->id,
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'examiner1_id' => $dosenC->id,
            'order' => 2
        ]);

        $notification = new SchedulePublished($schedule, 'Seminar');

        // Test Dosen B (Only pembimbing for Mhs1, NOT involved in Mhs2)
        $msgB = $notification->toFonnte($dosenB);
        $this->assertStringContainsString('Mahasiswa Satu', $msgB);
        $this->assertStringContainsString('Pembimbing 1', $msgB);
        $this->assertStringNotContainsString('Mahasiswa Dua', $msgB);
        $this->assertStringContainsString('1 Mahasiswa', $msgB);

        // Test Dosen C (Only examiner for Mhs2, NOT involved in Mhs1)
        $msgC = $notification->toFonnte($dosenC);
        $this->assertStringContainsString('Mahasiswa Dua', $msgC);
        $this->assertStringContainsString('Penguji 1', $msgC);
        $this->assertStringNotContainsString('Mahasiswa Satu', $msgC);
        $this->assertStringContainsString('1 Mahasiswa', $msgC);

        // Test Dosen A (Examiner for Mhs1 AND Pembimbing for Mhs2)
        $msgA = $notification->toFonnte($dosenA);
        $this->assertStringContainsString('Mahasiswa Satu', $msgA);
        $this->assertStringContainsString('Mahasiswa Dua', $msgA);
        $this->assertStringContainsString('2 Mahasiswa', $msgA);
    }

    public function test_chairman_presides_over_session_with_clear_roles()
    {
        $this->seed(\Database\Seeders\WaTemplateSeeder::class);

        $chairman = User::factory()->create(['role' => 'dosen', 'name' => 'Bpk Ketua Sidang']);
        $examiner = User::factory()->create(['role' => 'dosen', 'name' => 'Bpk Penguji']);
        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Sidang']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Judul Sidang',
            'status' => 'active'
        ]);

        $schedule = SeminarSchedule::create([
            'title' => 'Sidang Skripsi Sesi Pagi',
            'date' => '2026-08-16',
            'location' => 'Ruang Sidang Utama',
            'chairman_id' => $chairman->id,
            'created_by' => $chairman->id,
        ]);

        SeminarScheduleDetail::create([
            'seminar_schedule_id' => $schedule->id,
            'thesis_id' => $thesis->id,
            'start_time' => '08:30:00',
            'end_time' => '09:30:00',
            'examiner1_id' => $examiner->id,
            'order' => 1
        ]);

        $notification = new SchedulePublished($schedule, 'Sidang');

        $msgChairman = $notification->toFonnte($chairman);
        $this->assertStringContainsString('Ketua Sidang', $msgChairman);
        $this->assertStringContainsString('Mahasiswa Sidang', $msgChairman);
        $this->assertStringContainsString('1 Mahasiswa', $msgChairman);
    }
}
