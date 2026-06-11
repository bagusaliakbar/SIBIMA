<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Thesis;
use App\Models\MentoringSession;
use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_mentoring_history_is_displayed_in_student_history_page()
    {
        // 1. Setup Student & Thesis
        $student = User::factory()->create(['role' => 'mahasiswa', 'identifier' => '123456']);
        $dosen = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Pembimbing']);
        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Optimasi Sistem Akademik SIBIMA',
            'pembimbing1_id' => $dosen->id,
            'status' => 'active',
        ]);

        // 2. Create a mentoring session
        $session = MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => $dosen->id,
            'topic' => 'Diskusi Bab 1',
            'type' => 'offline',
            'scheduled_at' => now()->addDays(2),
            'status' => 'pending',
        ]);

        // 3. Log a mentoring activity (e.g., Pengajuan Bimbingan) as the subject MentoringSession
        ActivityLog::log('Pengajuan Bimbingan', 'Mahasiswa mengajukan bimbingan', 'Bimbingan', $session);

        // 4. Act as Student and get student/history page
        $response = $this->actingAs($student)->get(route('student.history'));

        // 5. Assert status and see the logged activity in the response
        $response->assertStatus(200);
        $response->assertSee('Pengajuan Bimbingan');
        $response->assertSee('Mahasiswa mengajukan bimbingan');
    }

    public function test_student_can_delete_mentoring_session_document()
    {
        // 1. Setup Student & Thesis
        $student = User::factory()->create(['role' => 'mahasiswa', 'identifier' => '123456']);
        $dosen = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Pembimbing']);
        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Optimasi Sistem Akademik SIBIMA',
            'pembimbing1_id' => $dosen->id,
            'status' => 'active',
        ]);

        // 2. Create a mentoring session with a document
        $session = MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => $dosen->id,
            'topic' => 'Diskusi Bab 1',
            'type' => 'offline',
            'scheduled_at' => now()->addDays(2),
            'status' => 'pending',
            'document_path' => 'session-documents/fake_doc.pdf',
            'document_original_name' => 'fake_doc.pdf',
        ]);

        // 3. Act as Student and send delete request
        $response = $this->actingAs($student)->delete(route('mentoring-sessions.delete-document', $session->id));

        // 4. Assert status/redirection
        $response->assertRedirect();
        
        // 5. Assert database fields are nullified
        $session->refresh();
        $this->assertNull($session->document_path);
        $this->assertNull($session->document_original_name);
    }
}
