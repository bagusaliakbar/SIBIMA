<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Thesis;
use App\Models\MentoringSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThesisWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_thesis_workflow_from_submission_to_mentoring()
    {
        // 1. Setup Roles
        $admin = User::factory()->create(['role' => 'admin']);
        $p1 = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen P1']);
        $p2 = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen P2']);
        $student = User::factory()->create(['role' => 'mahasiswa', 'identifier' => '123456']);

        // 2. Student Submits Thesis
        $response = $this->actingAs($student)
            ->post(route('theses.store'), [
                'title' => 'Optimasi Sistem Akademik SIBIMA',
                'abstract' => 'Deskripsi penelitian tentang optimasi sistem...',
                'requested_pembimbing1_id' => $p1->id,
                'requested_pembimbing2_id' => $p2->id,
            ]);

        $response->assertRedirect();
        
        $thesis = Thesis::where('student_id', $student->id)->first();
        $this->assertNotNull($thesis);
        $this->assertEquals('Optimasi Sistem Akademik SIBIMA', $thesis->title);

        // 3. Admin Assigns Supervisors
        $response = $this->actingAs($admin)
            ->post(route('theses.assign', $thesis->id), [
                'pembimbing1_id' => $p1->id,
                'pembimbing2_id' => $p2->id,
            ]);

        $response->assertRedirect();
        
        $thesis->refresh();
        $this->assertEquals($p1->id, $thesis->pembimbing1_id);
        $this->assertEquals($p2->id, $thesis->pembimbing2_id);

        // 4. Student Creates Mentoring Session
        $response = $this->actingAs($student)
            ->post(route('mentoring-sessions.store'), [
                'thesis_id' => $thesis->id,
                'topic' => 'Diskusi Bab 1: Pendahuluan',
                'notes' => 'Menyiapkan latar belakang masalah.',
                'scheduled_at' => now()->addDays(2)->format('Y-m-d H:i'),
                'type' => 'offline',
                'dosen_id' => $p1->id,
            ]);

        $response->assertRedirect();
        
        $session = MentoringSession::where('thesis_id', $thesis->id)->first();
        $this->assertEquals('pending', $session->status);

        // 5. Supervisor Approves Mentoring Session
        $response = $this->actingAs($p1)
            ->patch(route('mentoring-sessions.status', $session->id), [
                'status' => 'approved',
            ]);

        $response->assertRedirect();
        
        $session->refresh();
        $this->assertEquals('approved', $session->status);
    }

    public function test_student_cannot_schedule_conflicting_mentoring_session()
    {
        $p1 = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen P1']);
        $p2 = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen P2']);
        $student = User::factory()->create(['role' => 'mahasiswa']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'pembimbing1_id' => $p1->id,
            'pembimbing2_id' => $p2->id,
            'title' => 'Judul Test Skripsi',
            'abstract' => 'Abstrak Test Skripsi',
            'status' => 'active',
        ]);

        $scheduledAt = now()->addDays(2)->format('Y-m-d H:i');

        // Create first session with P1
        $this->actingAs($student)
            ->post(route('mentoring-sessions.store'), [
                'thesis_id' => $thesis->id,
                'topic' => 'Bimbingan Pertama dengan P1',
                'scheduled_at' => $scheduledAt,
                'type' => 'online',
                'dosen_id' => $p1->id,
            ])
            ->assertRedirect();

        // Try creating second session at same time with P2
        $response = $this->actingAs($student)
            ->post(route('mentoring-sessions.store'), [
                'thesis_id' => $thesis->id,
                'topic' => 'Bimbingan Kedua dengan P2 di waktu sama',
                'scheduled_at' => $scheduledAt,
                'type' => 'online',
                'dosen_id' => $p2->id,
            ]);

        $response->assertSessionHasErrors('scheduled_at');
    }

    public function test_thesis_show_redirects_to_logbooks()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Judul Skripsi Pengujian',
            'abstract' => 'Abstrak Pengujian',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get(route('theses.show', $thesis->id));
        $response->assertRedirect(route('theses.logbooks', $thesis->id));

        $response = $this->actingAs($student)->get(route('theses.show', $thesis->id));
        $response->assertRedirect(route('theses.logbooks', $thesis->id));
    }
}
