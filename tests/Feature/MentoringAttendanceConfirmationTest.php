<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Thesis;
use App\Models\MentoringSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use App\Notifications\GeneralNotification;
use Tests\TestCase;

class MentoringAttendanceConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_confirm_attendance_as_attending()
    {
        Notification::fake();

        $dosen = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Pembimbing']);
        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Test']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Sistem Informasi Skripsi',
            'pembimbing1_id' => $dosen->id,
            'status' => 'active',
        ]);

        $session = MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => $dosen->id,
            'topic' => 'Pembahasan Bab 1-3',
            'scheduled_at' => now()->addDays(2),
            'type' => 'offline',
            'status' => 'approved',
            'student_attendance_status' => 'pending',
        ]);

        $response = $this->actingAs($student)
            ->post(route('mentoring-sessions.confirm-attendance', $session->id), [
                'status' => 'attending',
            ]);

        $response->assertSessionHas('success');
        $session->refresh();

        $this->assertEquals('attending', $session->student_attendance_status);
        $this->assertNull($session->student_attendance_reason);
        $this->assertNotNull($session->student_confirmed_at);
        $this->assertTrue($session->isStudentAttending());

        Notification::assertSentTo($dosen, GeneralNotification::class);
    }

    public function test_student_can_confirm_attendance_as_permission_with_reason()
    {
        Notification::fake();

        $dosen = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Pembimbing']);
        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Test']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Sistem Informasi Skripsi',
            'pembimbing1_id' => $dosen->id,
            'status' => 'active',
        ]);

        $session = MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => $dosen->id,
            'topic' => 'Pembahasan Bab 4',
            'scheduled_at' => now()->addDays(2),
            'type' => 'offline',
            'status' => 'approved',
            'student_attendance_status' => 'pending',
        ]);

        $response = $this->actingAs($student)
            ->post(route('mentoring-sessions.confirm-attendance', $session->id), [
                'status' => 'permission',
                'reason' => 'Mohon maaf Pak, saya sedang sakit flu dan demam tinggi.',
            ]);

        $response->assertSessionHas('success');
        $session->refresh();

        $this->assertEquals('permission', $session->student_attendance_status);
        $this->assertEquals('Mohon maaf Pak, saya sedang sakit flu dan demam tinggi.', $session->student_attendance_reason);
        $this->assertNotNull($session->student_confirmed_at);
        $this->assertTrue($session->isStudentPermission());

        Notification::assertSentTo($dosen, GeneralNotification::class);
    }

    public function test_permission_requires_reason()
    {
        $dosen = User::factory()->create(['role' => 'dosen']);
        $student = User::factory()->create(['role' => 'mahasiswa']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Sistem Informasi Skripsi',
            'pembimbing1_id' => $dosen->id,
            'status' => 'active',
        ]);

        $session = MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => $dosen->id,
            'topic' => 'Pembahasan Bab 4',
            'scheduled_at' => now()->addDays(2),
            'type' => 'offline',
            'status' => 'approved',
            'student_attendance_status' => 'pending',
        ]);

        $response = $this->actingAs($student)
            ->post(route('mentoring-sessions.confirm-attendance', $session->id), [
                'status' => 'permission',
                'reason' => '',
            ]);

        $response->assertSessionHasErrors('reason');
    }

    public function test_other_student_cannot_confirm_attendance_for_different_thesis()
    {
        $dosen = User::factory()->create(['role' => 'dosen']);
        $student1 = User::factory()->create(['role' => 'mahasiswa']);
        $student2 = User::factory()->create(['role' => 'mahasiswa']);

        $thesis = Thesis::create([
            'student_id' => $student1->id,
            'title' => 'Sistem Informasi Skripsi',
            'pembimbing1_id' => $dosen->id,
            'status' => 'active',
        ]);

        $session = MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => $dosen->id,
            'topic' => 'Pembahasan Bab 4',
            'scheduled_at' => now()->addDays(2),
            'type' => 'offline',
            'status' => 'approved',
            'student_attendance_status' => 'pending',
        ]);

        $response = $this->actingAs($student2)
            ->post(route('mentoring-sessions.confirm-attendance', $session->id), [
                'status' => 'attending',
            ]);

        $response->assertStatus(403);
    }
}