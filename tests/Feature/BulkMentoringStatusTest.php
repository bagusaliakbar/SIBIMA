<?php

namespace Tests\Feature;

use App\Models\MentoringSession;
use App\Models\Thesis;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BulkMentoringStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_dosen_can_bulk_mark_sessions_as_absent(): void
    {
        $dosen = User::factory()->create(['role' => 'dosen']);
        $student1 = User::factory()->create(['role' => 'mahasiswa']);
        $student2 = User::factory()->create(['role' => 'mahasiswa']);

        $thesis1 = Thesis::create(['title' => 'Skripsi A', 'student_id' => $student1->id, 'pembimbing1_id' => $dosen->id, 'status' => 'active']);
        $thesis2 = Thesis::create(['title' => 'Skripsi B', 'student_id' => $student2->id, 'pembimbing1_id' => $dosen->id, 'status' => 'active']);

        $session1 = MentoringSession::create([
            'thesis_id' => $thesis1->id,
            'dosen_id' => $dosen->id,
            'topic' => 'Topik 1',
            'type' => 'offline',
            'scheduled_at' => now()->addHours(2),
            'status' => 'approved',
            'is_absent' => false,
        ]);

        $session2 = MentoringSession::create([
            'thesis_id' => $thesis2->id,
            'dosen_id' => $dosen->id,
            'topic' => 'Topik 2',
            'type' => 'offline',
            'scheduled_at' => now()->addHours(2),
            'status' => 'approved',
            'is_absent' => false,
        ]);

        $response = $this->actingAs($dosen)->post(route('mentoring-sessions.bulk-status'), [
            'session_ids' => [$session1->id, $session2->id],
            'status' => 'absent',
            'feedback' => 'Tidak hadir tanpa konfirmasi.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $session1->refresh();
        $session2->refresh();

        $this->assertEquals('completed', $session1->status);
        $this->assertTrue((bool) $session1->is_absent);
        $this->assertEquals('completed', $session2->status);
        $this->assertTrue((bool) $session2->is_absent);
    }

    public function test_dosen_can_bulk_complete_sessions_with_feedback(): void
    {
        $dosen = User::factory()->create(['role' => 'dosen']);
        $student1 = User::factory()->create(['role' => 'mahasiswa']);
        $student2 = User::factory()->create(['role' => 'mahasiswa']);

        $thesis1 = Thesis::create(['title' => 'Skripsi A', 'student_id' => $student1->id, 'pembimbing1_id' => $dosen->id, 'status' => 'active']);
        $thesis2 = Thesis::create(['title' => 'Skripsi B', 'student_id' => $student2->id, 'pembimbing1_id' => $dosen->id, 'status' => 'active']);

        $session1 = MentoringSession::create([
            'thesis_id' => $thesis1->id,
            'dosen_id' => $dosen->id,
            'topic' => 'Topik 1',
            'type' => 'offline',
            'scheduled_at' => now()->addHours(2),
            'status' => 'approved',
            'is_absent' => false,
        ]);

        $session2 = MentoringSession::create([
            'thesis_id' => $thesis2->id,
            'dosen_id' => $dosen->id,
            'topic' => 'Topik 2',
            'type' => 'offline',
            'scheduled_at' => now()->addHours(2),
            'status' => 'approved',
            'is_absent' => false,
        ]);

        $response = $this->actingAs($dosen)->post(route('mentoring-sessions.bulk-status'), [
            'session_ids' => [$session1->id, $session2->id],
            'status' => 'completed',
            'feedback' => 'Bimbingan kelompok selesai. Revisi latar belakang.',
            'feedback_document_url' => 'https://drive.google.com/test-file',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $session1->refresh();
        $session2->refresh();

        $this->assertEquals('completed', $session1->status);
        $this->assertFalse((bool) $session1->is_absent);
        $this->assertEquals('Bimbingan kelompok selesai. Revisi latar belakang.', $session1->feedback);
        $this->assertEquals('https://drive.google.com/test-file', $session1->feedback_document_url);

        $this->assertEquals('completed', $session2->status);
        $this->assertFalse((bool) $session2->is_absent);
        $this->assertEquals('Bimbingan kelompok selesai. Revisi latar belakang.', $session2->feedback);
    }

    public function test_unauthorized_dosen_cannot_bulk_update_other_lecturers_sessions(): void
    {
        $dosenA = User::factory()->create(['role' => 'dosen']);
        $dosenB = User::factory()->create(['role' => 'dosen']);
        $student = User::factory()->create(['role' => 'mahasiswa']);

        $thesis = Thesis::create(['title' => 'Skripsi A', 'student_id' => $student->id, 'pembimbing1_id' => $dosenA->id, 'status' => 'active']);

        $session = MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => $dosenA->id,
            'topic' => 'Topik A',
            'type' => 'offline',
            'scheduled_at' => now()->addHours(2),
            'status' => 'approved',
            'is_absent' => false,
        ]);

        // Dosen B attempts to update Dosen A's session
        $response = $this->actingAs($dosenB)->post(route('mentoring-sessions.bulk-status'), [
            'session_ids' => [$session->id],
            'status' => 'absent',
        ]);

        $response->assertForbidden();
    }

    public function test_bulk_status_validation_fails_with_empty_sessions(): void
    {
        $dosen = User::factory()->create(['role' => 'dosen']);

        $response = $this->actingAs($dosen)->post(route('mentoring-sessions.bulk-status'), [
            'session_ids' => [],
            'status' => 'absent',
        ]);

        $response->assertSessionHasErrors('session_ids');
    }

    public function test_mentoring_sessions_view_renders_bulk_attendance_elements(): void
    {
        $dosen = User::factory()->create(['role' => 'dosen']);
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $thesis = Thesis::create(['title' => 'Skripsi Test', 'student_id' => $student->id, 'pembimbing1_id' => $dosen->id, 'status' => 'active']);

        $session = MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => $dosen->id,
            'topic' => 'Topik Uji',
            'type' => 'offline',
            'scheduled_at' => now()->addHours(2),
            'status' => 'approved',
            'is_absent' => false,
        ]);

        $response = $this->actingAs($dosen)->get(route('mentoring-sessions.index'));
        $response->assertOk();
        $response->assertSee('selectedSessionIds');
        $response->assertSee('Pilih Sesi Ini');
        $response->assertSee('Absen Massal');
        $response->assertSee('Selesai Massal');
        $response->assertSee('Dipilih untuk aksi massal');
    }
}
