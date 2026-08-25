<?php

namespace Tests\Feature;

use App\Models\SeminarSchedule;
use App\Models\Thesis;
use App\Models\ThesisDefenseSchedule;
use App\Models\User;
use App\Models\Wave;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OptionalChairmanModeratorTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_update_seminar_schedule_without_chairman_and_moderator()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $dosen1 = User::factory()->create(['role' => 'dosen']);
        $examiner1 = User::factory()->create(['role' => 'dosen']);
        $examiner2 = User::factory()->create(['role' => 'dosen']);
        $wave = Wave::create([
            'name' => 'Gelombang 1',
            'academic_year' => '2026/2027',
            'semester' => 'ganjil',
            'is_active' => true,
        ]);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Skripsi Uji Coba Tanpa Ketua dan Moderator',
            'pembimbing1_id' => $dosen1->id,
            'status' => 'acc_up',
        ]);

        $postData = [
            'title' => 'Jadwal Seminar Proposal Tanpa Ketua & Moderator',
            'date' => '2026-09-10',
            'chairman_id' => '', // optional/empty
            'moderator_id' => '', // optional/empty
            'location' => 'Ruang 301',
            'details' => [
                [
                    'start_time' => '09:00',
                    'end_time' => '10:00',
                    'thesis_id' => $thesis->id,
                    'examiner1_id' => $examiner1->id,
                    'examiner2_id' => $examiner2->id,
                ]
            ]
        ];

        $response = $this->actingAs($admin)->post(route('seminar-schedules.store'), $postData);
        $response->assertRedirect(route('seminar-schedules.index'));
        $response->assertSessionHas('success');

        $schedule = SeminarSchedule::where('title', 'Jadwal Seminar Proposal Tanpa Ketua & Moderator')->first();
        $this->assertNotNull($schedule);
        $this->assertNull($schedule->chairman_id);
        $this->assertNull($schedule->moderator_id);

        // Index page renders with '-'
        $indexRes = $this->actingAs($admin)->get(route('seminar-schedules.index'));
        $indexRes->assertStatus(200);

        // Show page renders with '-'
        $showRes = $this->actingAs($admin)->get(route('seminar-schedules.show', $schedule));
        $showRes->assertStatus(200);

        // PDF export works without error
        $pdfRes = $this->actingAs($admin)->get(route('seminar-schedules.export-pdf', $schedule));
        $pdfRes->assertStatus(200);

        // Test update without chairman and moderator
        $updateData = $postData;
        $updateData['title'] = 'Jadwal Seminar Proposal Updated';
        $updateRes = $this->actingAs($admin)->put(route('seminar-schedules.update', $schedule), $updateData);
        $updateRes->assertRedirect(route('seminar-schedules.index'));
        
        $schedule->refresh();
        $this->assertSame('Jadwal Seminar Proposal Updated', $schedule->title);
        $this->assertNull($schedule->chairman_id);
        $this->assertNull($schedule->moderator_id);
    }

    public function test_admin_can_create_and_update_thesis_defense_schedule_without_chairman_and_moderator()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $dosen1 = User::factory()->create(['role' => 'dosen']);
        $examiner1 = User::factory()->create(['role' => 'dosen']);
        $examiner2 = User::factory()->create(['role' => 'dosen']);
        $wave = Wave::create([
            'name' => 'Gelombang 1',
            'academic_year' => '2026/2027',
            'semester' => 'ganjil',
            'is_active' => true,
        ]);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Skripsi Sidang Tanpa Ketua dan Moderator',
            'pembimbing1_id' => $dosen1->id,
            'status' => 'acc_kompre',
        ]);

        $postData = [
            'title' => 'Jadwal Sidang Skripsi Tanpa Ketua & Moderator',
            'date' => '2026-09-12',
            'chairman_id' => null, // optional/null
            'moderator_id' => null, // optional/null
            'location' => 'Lab Komputer 1',
            'details' => [
                [
                    'start_time' => '13:00',
                    'end_time' => '14:30',
                    'thesis_id' => $thesis->id,
                    'examiner1_id' => $examiner1->id,
                    'examiner2_id' => $examiner2->id,
                ]
            ]
        ];

        $response = $this->actingAs($admin)->post(route('thesis-defense-schedules.store'), $postData);
        $response->assertRedirect(route('thesis-defense-schedules.index'));
        $response->assertSessionHas('success');

        $schedule = ThesisDefenseSchedule::where('title', 'Jadwal Sidang Skripsi Tanpa Ketua & Moderator')->first();
        $this->assertNotNull($schedule);
        $this->assertNull($schedule->chairman_id);
        $this->assertNull($schedule->moderator_id);

        // Index page renders with '-'
        $indexRes = $this->actingAs($admin)->get(route('thesis-defense-schedules.index'));
        $indexRes->assertStatus(200);

        // Show page renders with '-'
        $showRes = $this->actingAs($admin)->get(route('thesis-defense-schedules.show', $schedule));
        $showRes->assertStatus(200);

        // PDF export works without error
        $pdfRes = $this->actingAs($admin)->get(route('thesis-defense-schedules.export-pdf', $schedule));
        $pdfRes->assertStatus(200);

        // Test update without chairman and moderator
        $updateData = $postData;
        $updateData['title'] = 'Jadwal Sidang Skripsi Updated';
        $updateRes = $this->actingAs($admin)->put(route('thesis-defense-schedules.update', $schedule), $updateData);
        $updateRes->assertRedirect(route('thesis-defense-schedules.index'));

        $schedule->refresh();
        $this->assertSame('Jadwal Sidang Skripsi Updated', $schedule->title);
        $this->assertNull($schedule->chairman_id);
        $this->assertNull($schedule->moderator_id);
    }

    public function test_dosen_can_be_moderator_or_chairman_and_also_examiner_in_same_schedule_without_conflict()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $dosenTazkia = User::factory()->create(['role' => 'dosen', 'name' => 'Tazkia Salsabila']);
        $dosenOther = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Penguji 1']);
        $wave = Wave::create([
            'name' => 'Gelombang 4',
            'academic_year' => '2025/2026',
            'semester' => 'genap',
            'is_active' => true,
        ]);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Skripsi Mas Fatli Jiha',
            'pembimbing1_id' => $dosenOther->id,
            'status' => 'acc_kompre',
        ]);

        // Dosen Tazkia is Moderator AND Penguji 2 on the same schedule
        $postData = [
            'title' => 'SEMESTER GENAP GELOMBANG 4 TAHUN AKADEMIK 2025/2026',
            'date' => '2026-08-25',
            'chairman_id' => $dosenOther->id,
            'moderator_id' => $dosenTazkia->id, // Tazkia as Moderator
            'location' => 'Ruang Sidang 1',
            'details' => [
                [
                    'start_time' => '08:00',
                    'end_time' => '09:00',
                    'thesis_id' => $thesis->id,
                    'examiner1_id' => $dosenOther->id,
                    'examiner2_id' => $dosenTazkia->id, // Tazkia ALSO as Penguji 2
                ]
            ]
        ];

        $response = $this->actingAs($admin)->post(route('thesis-defense-schedules.store'), $postData);
        $response->assertRedirect(route('thesis-defense-schedules.index'));
        $response->assertSessionHas('success');

        $schedule = ThesisDefenseSchedule::where('title', 'SEMESTER GENAP GELOMBANG 4 TAHUN AKADEMIK 2025/2026')->first();
        $this->assertNotNull($schedule);
        $this->assertSame($dosenTazkia->id, $schedule->moderator_id);
        $this->assertSame($dosenTazkia->id, $schedule->details->first()->examiner2_id);

        // Check availability API endpoint: Checking Tazkia for a slot where she's not conflicting with any other schedule
        $checkAvailabilityResponse = $this->actingAs($admin)->postJson(route('check-dosen-availability'), [
            'dosen_ids' => [$dosenOther->id, $dosenTazkia->id],
            'date' => '2026-08-25',
            'start_time' => '08:00',
            'end_time' => '09:00',
            'schedule_type' => 'defense',
            'current_schedule_id' => $schedule->id,
        ]);

        $checkAvailabilityResponse->assertStatus(200);
        $checkAvailabilityResponse->assertJson([
            'has_conflict' => false,
            'conflicts' => [],
        ]);
    }
}

