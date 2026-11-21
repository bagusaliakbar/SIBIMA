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

    public function test_student_can_confirm_attendance_via_get_with_status_query()
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
            'topic' => 'Pembahasan Bab 1-3',
            'scheduled_at' => now()->addDays(2),
            'type' => 'offline',
            'status' => 'approved',
            'student_attendance_status' => 'pending',
        ]);

        $response = $this->actingAs($student)
            ->get(route('mentoring-sessions.confirm-attendance', [$session->id, 'status' => 'attending']));

        $response->assertRedirect(route('mentoring.student_index', ['highlight' => $session->id]));
        $response->assertSessionHas('success');
        $session->refresh();

        $this->assertEquals('attending', $session->student_attendance_status);
        $this->assertNotNull($session->student_confirmed_at);
    }

    public function test_get_confirm_attendance_without_status_redirects_gracefully_without_405()
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
            'topic' => 'Pembahasan Bab 1-3',
            'scheduled_at' => now()->addDays(2),
            'type' => 'offline',
            'status' => 'approved',
            'student_attendance_status' => 'pending',
        ]);

        // Access via plain GET (like browser refresh, back button, or direct URL)
        $response = $this->actingAs($student)
            ->get(route('mentoring-sessions.confirm-attendance', $session->id));

        $response->assertRedirect(route('mentoring.student_index', ['highlight' => $session->id]));
        $response->assertSessionHas('info');
    }

    public function test_student_can_confirm_attendance_with_preferred_arrival_time_within_range()
    {
        Notification::fake();

        $dosen = User::factory()->create(['role' => 'dosen', 'name' => 'Dr. Dosen']);
        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Hadir']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Skripsi Test Jam Kedatangan',
            'pembimbing1_id' => $dosen->id,
            'status' => 'active',
        ]);

        $date = now()->addDays(2)->format('Y-m-d');
        $session = MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => $dosen->id,
            'topic' => 'Bimbingan Rentang Waktu',
            'scheduled_at' => "{$date} 09:00:00",
            'scheduled_end_at' => "{$date} 11:00:00",
            'type' => 'offline',
            'status' => 'approved',
            'student_attendance_status' => 'pending',
        ]);

        // Out-of-range arrival time must be rejected
        $this->actingAs($student)
            ->post(route('mentoring-sessions.confirm-attendance', $session->id), [
                'status' => 'attending',
                'arrival_time' => '11:30',
            ])
            ->assertSessionHasErrors('arrival_time');

        // Valid arrival time within 09:00 - 11:00 succeeds
        $this->actingAs($student)
            ->post(route('mentoring-sessions.confirm-attendance', $session->id), [
                'status' => 'attending',
                'arrival_time' => '09:30',
            ])
            ->assertSessionHas('success');

        $session->refresh();
        $this->assertEquals('attending', $session->student_attendance_status);
        $this->assertEquals('09:30', $session->student_arrival_time);
        $this->assertEquals('09:30 WIB', $session->formatted_arrival_time);

        // Live attendance JSON endpoint returns arrival time & range
        $liveRes = $this->actingAs($dosen)->getJson(route('mentoring-sessions.live-attendance'));
        $liveRes->assertOk()
            ->assertJsonPath('sessions.0.arrival_time', '09:30')
            ->assertJsonPath('sessions.0.arrival_time_formatted', '09:30 WIB')
            ->assertJsonPath('sessions.0.scheduled_time_formatted', '09:00 - 11:00 WIB');

        // Rescheduling the session resets arrival_time back to null
        $newDate = now()->addDays(4)->format('Y-m-d');
        $this->actingAs($dosen)->put(route('mentoring-sessions.update', $session), [
            'scheduled_date' => $newDate,
            'scheduled_hour' => '13',
            'scheduled_minute' => '00',
            'scheduled_end_time' => '15:00',
            'topic' => 'Bimbingan Rentang Waktu',
            'type' => 'offline',
        ])->assertRedirect();

        $session->refresh();
        $this->assertEquals('pending', $session->student_attendance_status);
        $this->assertNull($session->student_arrival_time);
    }

    public function test_switching_attendance_to_permission_clears_arrival_time()
    {
        Notification::fake();

        $dosen = User::factory()->create(['role' => 'dosen']);
        $student = User::factory()->create(['role' => 'mahasiswa']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Skripsi Test',
            'pembimbing1_id' => $dosen->id,
            'status' => 'active',
        ]);

        $date = now()->addDays(2)->format('Y-m-d');
        $session = MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => $dosen->id,
            'topic' => 'Bimbingan',
            'scheduled_at' => "{$date} 09:00:00",
            'scheduled_end_at' => "{$date} 11:00:00",
            'type' => 'offline',
            'status' => 'approved',
            'student_attendance_status' => 'attending',
            'student_arrival_time' => '10:00',
        ]);

        $this->actingAs($student)
            ->post(route('mentoring-sessions.confirm-attendance', $session->id), [
                'status' => 'permission',
                'reason' => 'Ada kuliah pengganti mendadak.',
            ])
            ->assertSessionHas('success');

        $session->refresh();
        $this->assertEquals('permission', $session->student_attendance_status);
        $this->assertNull($session->student_arrival_time);
    }
}