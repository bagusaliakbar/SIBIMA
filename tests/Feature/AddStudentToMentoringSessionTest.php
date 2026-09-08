<?php

namespace Tests\Feature;

use App\Models\MentoringSession;
use App\Models\Thesis;
use App\Models\User;
use App\Notifications\MentoringScheduledByDosenNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AddStudentToMentoringSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_dosen_can_view_available_missed_students_on_edit_page()
    {
        $dosen = User::factory()->create(['role' => 'dosen']);
        $studentA = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa A']);
        $studentB = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa B']);

        $thesisA = Thesis::create([
            'student_id' => $studentA->id,
            'title' => 'Skripsi Mahasiswa A',
            'status' => 'active',
            'pembimbing1_id' => $dosen->id,
        ]);

        $thesisB = Thesis::create([
            'student_id' => $studentB->id,
            'title' => 'Skripsi Mahasiswa B',
            'status' => 'active',
            'pembimbing1_id' => $dosen->id,
        ]);

        $session = MentoringSession::create([
            'thesis_id' => $thesisA->id,
            'dosen_id' => $dosen->id,
            'scheduled_at' => now()->addDays(2),
            'topic' => 'Bimbingan Bab 1',
            'type' => 'offline',
            'location' => 'Ruang Dosen',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($dosen)->get(route('mentoring-sessions.edit', $session));

        $response->assertStatus(200);
        $response->assertSee('Tambah Mahasiswa Terlewat');
        $response->assertSee('Mahasiswa B');
    }

    public function test_dosen_can_add_missed_student_to_existing_mentoring_session()
    {
        Notification::fake();

        $dosen = User::factory()->create(['role' => 'dosen', 'name' => 'Dr. Budi Dosen']);
        $studentA = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Ahmad Santoso', 'phone_number' => '081234567890']);
        $studentB = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Rina Wijaya', 'phone_number' => '081234567891']);

        $thesisA = Thesis::create([
            'student_id' => $studentA->id,
            'title' => 'Sistem IoT untuk Smart Campus',
            'status' => 'active',
            'pembimbing1_id' => $dosen->id,
        ]);

        $thesisB = Thesis::create([
            'student_id' => $studentB->id,
            'title' => 'Penerapan Machine Learning',
            'status' => 'active',
            'pembimbing1_id' => $dosen->id,
        ]);

        $scheduledAt = now()->addDays(3)->startOfHour();

        $sessionA = MentoringSession::create([
            'thesis_id' => $thesisA->id,
            'dosen_id' => $dosen->id,
            'scheduled_at' => $scheduledAt,
            'topic' => 'Review Metodologi Penelitian',
            'type' => 'online',
            'location' => 'https://meet.google.com/test-room',
            'notes' => 'Siapkan slide presentasi',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($dosen)->post(route('mentoring-sessions.add-students', $sessionA), [
            'thesis_ids' => [$thesisB->id],
        ]);

        $response->assertRedirect(route('mentoring-sessions.edit', $sessionA));
        $response->assertSessionHas('success');

        // Check that a new mentoring session was created for student B
        $this->assertDatabaseHas('mentoring_sessions', [
            'thesis_id' => $thesisB->id,
            'dosen_id' => $dosen->id,
            'scheduled_at' => $scheduledAt->format('Y-m-d H:i:s'),
            'topic' => 'Review Metodologi Penelitian',
            'type' => 'online',
            'location' => 'https://meet.google.com/test-room',
            'notes' => 'Siapkan slide presentasi',
            'status' => 'approved',
            'student_attendance_status' => 'pending',
        ]);

        // Notification must be sent to Student B
        Notification::assertSentTo(
            $studentB,
            MentoringScheduledByDosenNotification::class,
            function ($notification) {
                return $notification->session->topic === 'Review Metodologi Penelitian';
            }
        );

        // Viewing edit page now reflects a group session with both students
        $editResponse = $this->actingAs($dosen)->get(route('mentoring-sessions.edit', $sessionA));
        $editResponse->assertSee('Sesi Bimbingan Bersama (2 Mahasiswa Terdaftar)');
        $editResponse->assertSee('Ahmad Santoso');
        $editResponse->assertSee('Rina Wijaya');
    }

    public function test_cannot_add_student_with_conflicting_schedule()
    {
        $dosen = User::factory()->create(['role' => 'dosen']);
        $otherDosen = User::factory()->create(['role' => 'dosen']);
        $studentA = User::factory()->create(['role' => 'mahasiswa']);
        $studentB = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Siti Aminah']);

        $thesisA = Thesis::create([
            'student_id' => $studentA->id,
            'title' => 'Skripsi A',
            'status' => 'active',
            'pembimbing1_id' => $dosen->id,
        ]);

        $thesisB = Thesis::create([
            'student_id' => $studentB->id,
            'title' => 'Skripsi B',
            'status' => 'active',
            'pembimbing1_id' => $dosen->id,
        ]);

        $scheduledAt = now()->addDays(2)->startOfHour();

        $sessionA = MentoringSession::create([
            'thesis_id' => $thesisA->id,
            'dosen_id' => $dosen->id,
            'scheduled_at' => $scheduledAt,
            'topic' => 'Bimbingan Bab 2',
            'type' => 'offline',
            'status' => 'approved',
        ]);

        // Student B already has another session at the same time
        MentoringSession::create([
            'thesis_id' => $thesisB->id,
            'dosen_id' => $otherDosen->id,
            'scheduled_at' => $scheduledAt,
            'topic' => 'Bimbingan Lain',
            'type' => 'online',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($dosen)->post(route('mentoring-sessions.add-students', $sessionA), [
            'thesis_ids' => [$thesisB->id],
        ]);

        $response->assertSessionHasErrors('thesis_ids');
    }

    public function test_mahasiswa_cannot_add_students_to_session()
    {
        $dosen = User::factory()->create(['role' => 'dosen']);
        $studentA = User::factory()->create(['role' => 'mahasiswa']);
        $studentB = User::factory()->create(['role' => 'mahasiswa']);

        $thesisA = Thesis::create([
            'student_id' => $studentA->id,
            'title' => 'Skripsi A',
            'status' => 'active',
            'pembimbing1_id' => $dosen->id,
        ]);

        $thesisB = Thesis::create([
            'student_id' => $studentB->id,
            'title' => 'Skripsi B',
            'status' => 'active',
            'pembimbing1_id' => $dosen->id,
        ]);

        $sessionA = MentoringSession::create([
            'thesis_id' => $thesisA->id,
            'dosen_id' => $dosen->id,
            'scheduled_at' => now()->addDays(2),
            'topic' => 'Bimbingan',
            'type' => 'offline',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($studentA)->post(route('mentoring-sessions.add-students', $sessionA), [
            'thesis_ids' => [$thesisB->id],
        ]);

        $response->assertStatus(403);
    }
}
