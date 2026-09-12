<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Thesis;
use App\Models\MentoringSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MentoringFeedbackDocumentTest extends TestCase
{
    use RefreshDatabase;

    public function test_lecturer_can_complete_session_with_feedback_document_url()
    {
        $dosen = User::factory()->create(['role' => 'dosen']);
        $student = User::factory()->create(['role' => 'mahasiswa']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Skripsi Uji Coba Feedback Dokumen',
            'pembimbing1_id' => $dosen->id,
            'status' => 'active',
        ]);

        $session = MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => $dosen->id,
            'topic' => 'Bab 3 Metodologi',
            'scheduled_at' => now()->subHour(),
            'type' => 'offline',
            'status' => 'approved',
        ]);

        $docUrl = 'https://drive.google.com/file/d/123456789/view?usp=sharing';

        $response = $this->actingAs($dosen)
            ->patch(route('mentoring-sessions.status', $session->id), [
                'status' => 'completed',
                'feedback' => 'Perbaiki diagram alir metodologi pada bab 3.',
                'feedback_document_url' => $docUrl,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('mentoring_sessions', [
            'id' => $session->id,
            'status' => 'completed',
            'feedback' => 'Perbaiki diagram alir metodologi pada bab 3.',
            'feedback_document_url' => $docUrl,
        ]);
    }

    public function test_lecturer_can_update_feedback_document_url_on_completed_session()
    {
        $dosen = User::factory()->create(['role' => 'dosen']);
        $student = User::factory()->create(['role' => 'mahasiswa']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Skripsi Uji Coba Feedback Dokumen',
            'pembimbing1_id' => $dosen->id,
            'status' => 'active',
        ]);

        $session = MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => $dosen->id,
            'topic' => 'Bab 4 Hasil',
            'scheduled_at' => now()->subDay(),
            'type' => 'offline',
            'status' => 'completed',
            'feedback' => 'Catatan awal.',
            'feedback_document_url' => 'https://drive.google.com/old_file',
        ]);

        $newDocUrl = 'https://drive.google.com/new_revised_file';

        $response = $this->actingAs($dosen)
            ->patch(route('mentoring-sessions.status', $session->id), [
                'status' => 'completed',
                'feedback' => 'Catatan diperbarui dengan link revisi baru.',
                'feedback_document_url' => $newDocUrl,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Catatan hasil bimbingan berhasil diperbarui.');

        $this->assertDatabaseHas('mentoring_sessions', [
            'id' => $session->id,
            'feedback' => 'Catatan diperbarui dengan link revisi baru.',
            'feedback_document_url' => $newDocUrl,
        ]);
    }

    public function test_invalid_feedback_document_url_fails_validation()
    {
        $dosen = User::factory()->create(['role' => 'dosen']);
        $student = User::factory()->create(['role' => 'mahasiswa']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Skripsi Uji Coba Validasi Dokumen',
            'pembimbing1_id' => $dosen->id,
            'status' => 'active',
        ]);

        $session = MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => $dosen->id,
            'topic' => 'Bab 1 Pendahuluan',
            'scheduled_at' => now()->subHour(),
            'type' => 'offline',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($dosen)
            ->patch(route('mentoring-sessions.status', $session->id), [
                'status' => 'completed',
                'feedback' => 'Catatan bimbingan',
                'feedback_document_url' => 'bukan-sebuah-url-valid',
            ]);

        $response->assertSessionHasErrors('feedback_document_url');
    }

    public function test_student_can_see_feedback_document_url()
    {
        $dosen = User::factory()->create(['role' => 'dosen', 'name' => 'Dr. Pembimbing']);
        $student = User::factory()->create(['role' => 'mahasiswa']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Skripsi Mahasiswa Bimbingan',
            'pembimbing1_id' => $dosen->id,
            'status' => 'active',
        ]);

        $docUrl = 'https://drive.google.com/file/d/test-doc-123';

        $session = MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => $dosen->id,
            'topic' => 'Bimbingan Bab 2',
            'scheduled_at' => now()->subDay(),
            'type' => 'offline',
            'status' => 'completed',
            'feedback' => 'Silakan lihat revisi pada dokumen berikut.',
            'feedback_document_url' => $docUrl,
        ]);

        // Student mentoring sessions page
        $response = $this->actingAs($student)
            ->get(route('mentoring-sessions.index'));

        $response->assertStatus(200);
        $response->assertSee($docUrl, false);
        $response->assertSee('Dokumen Feedback / Koreksi Dosen');

        // Logbook page
        $responseLogbook = $this->actingAs($student)
            ->get(route('logbooks.index'));

        $responseLogbook->assertStatus(200);
        $responseLogbook->assertSee($docUrl, false);
        $responseLogbook->assertSee('Dokumen Koreksi / Feedback Dosen');

        // Thesis logbooks show page (for dosen/admin/etc)
        $responseThesisLogbook = $this->actingAs($dosen)
            ->get(route('theses.logbooks', $thesis->id));

        $responseThesisLogbook->assertStatus(200);
        $responseThesisLogbook->assertSee($docUrl, false);
        $responseThesisLogbook->assertSee('Dokumen Feedback / Koreksi');
    }
}
