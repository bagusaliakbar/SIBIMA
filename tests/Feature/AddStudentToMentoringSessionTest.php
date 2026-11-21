<?php

namespace Tests\Feature;

use App\Models\MentoringSession;
use App\Models\Thesis;
use App\Models\User;
use App\Notifications\MentoringRescheduledNotification;
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

    public function test_edit_page_stages_missed_students_inside_main_form_without_immediate_post_form()
    {
        $dosen = User::factory()->create(['role' => 'dosen']);
        $studentA = User::factory()->create(['role' => 'mahasiswa']);
        $studentB = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Terlewat']);

        $thesisA = Thesis::create([
            'student_id' => $studentA->id,
            'title' => 'Skripsi A',
            'status' => 'active',
            'pembimbing1_id' => $dosen->id,
        ]);

        Thesis::create([
            'student_id' => $studentB->id,
            'title' => 'Skripsi B',
            'status' => 'active',
            'pembimbing1_id' => $dosen->id,
        ]);

        $session = MentoringSession::create([
            'thesis_id' => $thesisA->id,
            'dosen_id' => $dosen->id,
            'scheduled_at' => now()->addDays(2),
            'topic' => 'Bimbingan Bab 1',
            'type' => 'offline',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($dosen)->get(route('mentoring-sessions.edit', $session));

        $response->assertStatus(200);
        $response->assertSee('name="add_thesis_ids[]"', false);
        $response->assertDontSee(route('mentoring-sessions.add-students', $session->id), false);
    }

    public function test_dosen_can_reschedule_and_add_missed_students_atomically_with_single_accurate_notification()
    {
        Notification::fake();

        $dosen = User::factory()->create(['role' => 'dosen', 'name' => 'Dr. Budi Dosen']);
        $studentA = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Ahmad Santoso']);
        $studentB = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Rina Wijaya']);
        $studentC = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Dedi Pratama']);

        $thesisA = Thesis::create(['student_id' => $studentA->id, 'title' => 'Skripsi A', 'status' => 'active', 'pembimbing1_id' => $dosen->id]);
        $thesisB = Thesis::create(['student_id' => $studentB->id, 'title' => 'Skripsi B', 'status' => 'active', 'pembimbing1_id' => $dosen->id]);
        $thesisC = Thesis::create(['student_id' => $studentC->id, 'title' => 'Skripsi C', 'status' => 'active', 'pembimbing1_id' => $dosen->id]);

        $oldTime = now()->addDays(2)->setTime(9, 0, 0);
        $sessionA = MentoringSession::create([
            'thesis_id' => $thesisA->id,
            'dosen_id' => $dosen->id,
            'scheduled_at' => $oldTime,
            'topic' => 'Topik Lama',
            'type' => 'offline',
            'location' => 'Ruang Lama',
            'status' => 'approved',
            'student_attendance_status' => 'attending',
        ]);

        $newDate = now()->addDays(5)->format('Y-m-d');
        $newScheduledAt = "{$newDate} 14:30:00";

        $response = $this->actingAs($dosen)->put(route('mentoring-sessions.update', $sessionA), [
            'scheduled_date' => $newDate,
            'scheduled_hour' => '14',
            'scheduled_minute' => '30',
            'topic' => 'Topik Baru Hasil Reschedule',
            'type' => 'online',
            'location' => 'https://meet.google.com/new-room-abc',
            'notes' => 'Bawa revisi terbaru',
            'add_thesis_ids' => [$thesisB->id, $thesisC->id],
        ]);

        $response->assertRedirect(route('mentoring-sessions.index'));
        $response->assertSessionHas('success');

        // Existing session A updated to new schedule
        $sessionA->refresh();
        $this->assertEquals($newScheduledAt, $sessionA->scheduled_at->format('Y-m-d H:i:s'));
        $this->assertEquals('Topik Baru Hasil Reschedule', $sessionA->topic);
        $this->assertEquals('pending', $sessionA->student_attendance_status);

        // New students B and C created directly on NEW schedule (never on old schedule)
        foreach ([$thesisB, $thesisC] as $thesis) {
            $this->assertDatabaseHas('mentoring_sessions', [
                'thesis_id' => $thesis->id,
                'dosen_id' => $dosen->id,
                'scheduled_at' => $newScheduledAt,
                'topic' => 'Topik Baru Hasil Reschedule',
                'type' => 'online',
                'location' => 'https://meet.google.com/new-room-abc',
                'notes' => 'Bawa revisi terbaru',
                'status' => 'approved',
                'student_attendance_status' => 'pending',
            ]);
        }

        // Student A receives exactly 1 reschedule notification (and NOT new-schedule notification)
        Notification::assertSentToTimes($studentA, MentoringRescheduledNotification::class, 1);
        Notification::assertNotSentTo($studentA, MentoringScheduledByDosenNotification::class);

        // Students B & C receive exactly 1 scheduled notification with NEW time (and NOT duplicate reschedule notification)
        foreach ([$studentB, $studentC] as $newStudent) {
            Notification::assertSentToTimes($newStudent, MentoringScheduledByDosenNotification::class, 1);
            Notification::assertNotSentTo($newStudent, MentoringRescheduledNotification::class);
            Notification::assertSentTo(
                $newStudent,
                MentoringScheduledByDosenNotification::class,
                fn ($n) => $n->session->scheduled_at->format('Y-m-d H:i:s') === $newScheduledAt
                    && $n->session->topic === 'Topik Baru Hasil Reschedule'
            );
        }
    }

    public function test_adding_missed_students_via_update_without_changing_schedule_only_notifies_new_students()
    {
        Notification::fake();

        $dosen = User::factory()->create(['role' => 'dosen']);
        $studentA = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Lama']);
        $studentB = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Baru']);

        $thesisA = Thesis::create(['student_id' => $studentA->id, 'title' => 'Skripsi A', 'status' => 'active', 'pembimbing1_id' => $dosen->id]);
        $thesisB = Thesis::create(['student_id' => $studentB->id, 'title' => 'Skripsi B', 'status' => 'active', 'pembimbing1_id' => $dosen->id]);

        $scheduledAt = now()->addDays(3)->setTime(10, 0, 0);
        $sessionA = MentoringSession::create([
            'thesis_id' => $thesisA->id,
            'dosen_id' => $dosen->id,
            'scheduled_at' => $scheduledAt,
            'topic' => 'Bimbingan Tetap',
            'type' => 'offline',
            'location' => 'Ruang 101',
            'notes' => 'Catatan tetap',
            'status' => 'approved',
            'student_attendance_status' => 'attending',
        ]);

        $response = $this->actingAs($dosen)->put(route('mentoring-sessions.update', $sessionA), [
            'scheduled_date' => $scheduledAt->format('Y-m-d'),
            'scheduled_hour' => '10',
            'scheduled_minute' => '00',
            'topic' => 'Bimbingan Tetap',
            'type' => 'offline',
            'location' => 'Ruang 101',
            'notes' => 'Catatan tetap',
            'add_thesis_ids' => [$thesisB->id],
        ]);

        $response->assertRedirect(route('mentoring-sessions.index'));
        $response->assertSessionHas('success');

        // Student A's attendance status preserved & NOT spammed with reschedule notification
        $sessionA->refresh();
        $this->assertEquals('attending', $sessionA->student_attendance_status);
        Notification::assertNotSentTo($studentA, MentoringRescheduledNotification::class);

        // Student B created & notified once
        $this->assertDatabaseHas('mentoring_sessions', [
            'thesis_id' => $thesisB->id,
            'scheduled_at' => $scheduledAt->format('Y-m-d H:i:s'),
        ]);
        Notification::assertSentToTimes($studentB, MentoringScheduledByDosenNotification::class, 1);
    }

    public function test_reschedule_and_add_missed_student_fails_atomically_on_new_time_conflict()
    {
        Notification::fake();

        $dosen = User::factory()->create(['role' => 'dosen']);
        $otherDosen = User::factory()->create(['role' => 'dosen']);
        $studentA = User::factory()->create(['role' => 'mahasiswa']);
        $studentB = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Siti Bentrok']);

        $thesisA = Thesis::create(['student_id' => $studentA->id, 'title' => 'Skripsi A', 'status' => 'active', 'pembimbing1_id' => $dosen->id]);
        $thesisB = Thesis::create(['student_id' => $studentB->id, 'title' => 'Skripsi B', 'status' => 'active', 'pembimbing1_id' => $dosen->id]);

        $oldTime = now()->addDays(2)->setTime(9, 0, 0);
        $newDate = now()->addDays(4)->format('Y-m-d');
        $newTime = "{$newDate} 15:00:00";

        $sessionA = MentoringSession::create([
            'thesis_id' => $thesisA->id,
            'dosen_id' => $dosen->id,
            'scheduled_at' => $oldTime,
            'topic' => 'Topik Awal',
            'type' => 'offline',
            'status' => 'approved',
        ]);

        // Student B conflicts at the NEW rescheduled time
        MentoringSession::create([
            'thesis_id' => $thesisB->id,
            'dosen_id' => $otherDosen->id,
            'scheduled_at' => $newTime,
            'topic' => 'Jadwal Lain',
            'type' => 'online',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($dosen)->put(route('mentoring-sessions.update', $sessionA), [
            'scheduled_date' => $newDate,
            'scheduled_hour' => '15',
            'scheduled_minute' => '00',
            'topic' => 'Topik Baru',
            'type' => 'online',
            'add_thesis_ids' => [$thesisB->id],
        ]);

        $response->assertSessionHasErrors('add_thesis_ids');

        // Existing session A must remain unchanged & zero notifications sent
        $sessionA->refresh();
        $this->assertEquals($oldTime->format('Y-m-d H:i:s'), $sessionA->scheduled_at->format('Y-m-d H:i:s'));
        Notification::assertNothingSent();
    }
}
