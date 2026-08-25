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
}
