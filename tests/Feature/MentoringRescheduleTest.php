<?php

namespace Tests\Feature;

use App\Channels\FonnteChannel;
use App\Models\MentoringSession;
use App\Models\Thesis;
use App\Models\User;
use App\Notifications\MentoringRescheduledNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MentoringRescheduleTest extends TestCase
{
    use RefreshDatabase;

    public function test_dosen_can_view_edit_mentoring_page()
    {
        $dosen = User::factory()->create(['role' => 'dosen']);
        $student = User::factory()->create(['role' => 'mahasiswa']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Sistem Informasi Monitoring Bimbingan',
            'status' => 'active',
            'pembimbing1_id' => $dosen->id,
        ]);

        $session = MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => $dosen->id,
            'scheduled_at' => now()->addDays(2),
            'topic' => 'Revisi Bab 1',
            'type' => 'offline',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($dosen)->get(route('mentoring-sessions.edit', $session));

        $response->assertStatus(200);
        $response->assertSee('Edit / Reschedule Jadwal Bimbingan');
        $response->assertSee('Revisi Bab 1');
    }

    public function test_dosen_can_reschedule_mentoring_session_and_notify_student()
    {
        Notification::fake();

        $dosen = User::factory()->create(['role' => 'dosen', 'name' => 'Dr. Hendra, M.Kom']);
        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Budi Santoso', 'phone_number' => '081234567890']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Sistem Informasi Monitoring Bimbingan',
            'status' => 'active',
            'pembimbing1_id' => $dosen->id,
        ]);

        $session = MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => $dosen->id,
            'scheduled_at' => now()->addDays(2),
            'topic' => 'Revisi Bab 1',
            'type' => 'offline',
            'location' => 'Ruang Dosen',
            'status' => 'approved',
            'student_attendance_status' => 'attending',
            'student_confirmed_at' => now(),
        ]);

        $newDate = now()->addDays(4)->format('Y-m-d');
        $newTime = '14:30';

        $response = $this->actingAs($dosen)->put(route('mentoring-sessions.update', $session), [
            'scheduled_date' => $newDate,
            'scheduled_time' => $newTime,
            'topic' => 'Revisi Bab 1 dan Metodologi',
            'type' => 'online',
            'location' => 'https://meet.google.com/abc-defg-hij',
            'notes' => 'Harap siapkan draft instrumen penelitian',
        ]);

        $response->assertRedirect(route('mentoring-sessions.index'));
        $response->assertSessionHas('success');

        $session->refresh();

        $this->assertEquals('Revisi Bab 1 dan Metodologi', $session->topic);
        $this->assertEquals('online', $session->type);
        $this->assertEquals('https://meet.google.com/abc-defg-hij', $session->location);
        $this->assertEquals('Harap siapkan draft instrumen penelitian', $session->notes);
        // Student attendance should be reset to pending because time changed
        $this->assertEquals('pending', $session->student_attendance_status);
        $this->assertNull($session->student_confirmed_at);

        Notification::assertSentTo(
            $student,
            MentoringRescheduledNotification::class,
            function ($notification, $channels) use ($student) {
                $this->assertContains(FonnteChannel::class, $channels);
                $this->assertContains('database', $channels);

                $waMessage = $notification->toFonnte($student);
                $this->assertStringContainsString('RESCHEDULE', $waMessage);
                $this->assertStringContainsString('Dr. Hendra, M.Kom', $waMessage);
                $this->assertStringContainsString('Revisi Bab 1 dan Metodologi', $waMessage);

                return true;
            }
        );
    }

    public function test_reschedule_fails_on_dosen_schedule_conflict()
    {
        $dosen = User::factory()->create(['role' => 'dosen']);
        $student1 = User::factory()->create(['role' => 'mahasiswa']);
        $student2 = User::factory()->create(['role' => 'mahasiswa']);

        $thesis1 = Thesis::create([
            'student_id' => $student1->id,
            'title' => 'Skripsi Satu',
            'status' => 'active',
            'pembimbing1_id' => $dosen->id,
        ]);

        $thesis2 = Thesis::create([
            'student_id' => $student2->id,
            'title' => 'Skripsi Dua',
            'status' => 'active',
            'pembimbing1_id' => $dosen->id,
        ]);

        $conflictTime = now()->addDays(3)->format('Y-m-d 10:00:00');

        // Session 1 is booked at conflictTime
        MentoringSession::create([
            'thesis_id' => $thesis1->id,
            'dosen_id' => $dosen->id,
            'scheduled_at' => $conflictTime,
            'topic' => 'Sesi Mahasiswa 1',
            'type' => 'offline',
            'status' => 'approved',
        ]);

        // Session 2 is initially booked at another time
        $session2 = MentoringSession::create([
            'thesis_id' => $thesis2->id,
            'dosen_id' => $dosen->id,
            'scheduled_at' => now()->addDays(5)->format('Y-m-d 10:00:00'),
            'topic' => 'Sesi Mahasiswa 2',
            'type' => 'offline',
            'status' => 'approved',
        ]);

        // Dosen tries to reschedule Session 2 to the same time as Session 1
        $response = $this->actingAs($dosen)->put(route('mentoring-sessions.update', $session2), [
            'scheduled_at' => $conflictTime,
            'topic' => 'Reschedule Sesi Mahasiswa 2',
            'type' => 'offline',
        ]);

        $response->assertSessionHasErrors('scheduled_at');
    }

    public function test_cannot_reschedule_completed_session()
    {
        $dosen = User::factory()->create(['role' => 'dosen']);
        $student = User::factory()->create(['role' => 'mahasiswa']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Skripsi Selesai',
            'status' => 'active',
            'pembimbing1_id' => $dosen->id,
        ]);

        $completedSession = MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => $dosen->id,
            'scheduled_at' => now()->subDays(2),
            'topic' => 'Sesi Selesai',
            'type' => 'offline',
            'status' => 'completed',
            'feedback' => 'Bagus sekali',
        ]);

        $response = $this->actingAs($dosen)->get(route('mentoring-sessions.edit', $completedSession));
        $response->assertStatus(403);

        $response2 = $this->actingAs($dosen)->put(route('mentoring-sessions.update', $completedSession), [
            'scheduled_at' => now()->addDays(1)->format('Y-m-d H:i'),
            'topic' => 'Coba ubah',
            'type' => 'offline',
        ]);
        $response2->assertStatus(403);
    }

    public function test_dosen_can_reschedule_mass_mentoring_sessions_together()
    {
        Notification::fake();

        $dosen = User::factory()->create(['role' => 'dosen', 'name' => 'Dr. Hendra, M.Kom']);
        $student1 = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa 1']);
        $student2 = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa 2']);
        $student3 = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa 3']);

        $thesis1 = Thesis::create(['student_id' => $student1->id, 'title' => 'Skripsi 1', 'status' => 'active', 'pembimbing1_id' => $dosen->id]);
        $thesis2 = Thesis::create(['student_id' => $student2->id, 'title' => 'Skripsi 2', 'status' => 'active', 'pembimbing1_id' => $dosen->id]);
        $thesis3 = Thesis::create(['student_id' => $student3->id, 'title' => 'Skripsi 3', 'status' => 'active', 'pembimbing1_id' => $dosen->id]);

        $initialTime = now()->addDays(2)->format('Y-m-d 09:00:00');

        $session1 = MentoringSession::create([
            'thesis_id' => $thesis1->id, 'dosen_id' => $dosen->id, 'scheduled_at' => $initialTime,
            'topic' => 'Briefing Metodologi', 'type' => 'offline', 'status' => 'approved', 'student_attendance_status' => 'attending'
        ]);
        $session2 = MentoringSession::create([
            'thesis_id' => $thesis2->id, 'dosen_id' => $dosen->id, 'scheduled_at' => $initialTime,
            'topic' => 'Briefing Metodologi', 'type' => 'offline', 'status' => 'approved', 'student_attendance_status' => 'attending'
        ]);
        $session3 = MentoringSession::create([
            'thesis_id' => $thesis3->id, 'dosen_id' => $dosen->id, 'scheduled_at' => $initialTime,
            'topic' => 'Briefing Metodologi', 'type' => 'offline', 'status' => 'approved', 'student_attendance_status' => 'attending'
        ]);

        $newDate = now()->addDays(5)->format('Y-m-d');
        $newTime = '13:00';
        $newScheduledAt = "{$newDate} {$newTime}:00";

        // Dosen edits session1 and checks apply_to_group = 1
        $response = $this->actingAs($dosen)->put(route('mentoring-sessions.update', $session1), [
            'scheduled_date' => $newDate,
            'scheduled_time' => $newTime,
            'topic' => 'Briefing Metodologi & Instrumen Penelitian',
            'type' => 'online',
            'location' => 'https://meet.google.com/group-meet-xyz',
            'notes' => 'Harap semua hadir tepat waktu',
            'apply_to_group' => 1,
        ]);

        $response->assertRedirect(route('mentoring-sessions.index'));
        $response->assertSessionHas('success');

        // All 3 sessions should be updated
        $session1->refresh();
        $session2->refresh();
        $session3->refresh();

        $this->assertEquals($newScheduledAt, $session1->scheduled_at->format('Y-m-d H:i:s'));
        $this->assertEquals($newScheduledAt, $session2->scheduled_at->format('Y-m-d H:i:s'));
        $this->assertEquals($newScheduledAt, $session3->scheduled_at->format('Y-m-d H:i:s'));

        $this->assertEquals('https://meet.google.com/group-meet-xyz', $session1->location);
        $this->assertEquals('https://meet.google.com/group-meet-xyz', $session2->location);
        $this->assertEquals('https://meet.google.com/group-meet-xyz', $session3->location);

        // Attendance reset to pending for all 3
        $this->assertEquals('pending', $session1->student_attendance_status);
        $this->assertEquals('pending', $session2->student_attendance_status);
        $this->assertEquals('pending', $session3->student_attendance_status);

        // All 3 students notified
        Notification::assertSentTo($student1, MentoringRescheduledNotification::class);
        Notification::assertSentTo($student2, MentoringRescheduledNotification::class);
        Notification::assertSentTo($student3, MentoringRescheduledNotification::class);
    }
}
