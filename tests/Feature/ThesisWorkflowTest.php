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
                'title' => 'Diskusi Bab 1: Pendahuluan',
                'notes' => 'Menyiapkan latar belakang masalah.',
                'scheduled_at' => now()->addDays(2)->format('Y-m-d H:i'),
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
}
