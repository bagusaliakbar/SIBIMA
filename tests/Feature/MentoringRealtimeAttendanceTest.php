<?php

namespace Tests\Feature;

use App\Models\MentoringSession;
use App\Models\Thesis;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MentoringRealtimeAttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_dosen_can_fetch_live_attendance_data(): void
    {
        $dosen = User::factory()->create(['role' => 'dosen']);
        $student1 = User::factory()->create(['role' => 'mahasiswa', 'identifier' => 'MHS001', 'name' => 'Budi Santoso', 'phone_number' => '081234567890']);
        $student2 = User::factory()->create(['role' => 'mahasiswa', 'identifier' => 'MHS002', 'name' => 'Siti Aminah', 'phone_number' => '081234567891']);
        $student3 = User::factory()->create(['role' => 'mahasiswa', 'identifier' => 'MHS003', 'name' => 'Andi Wijaya', 'phone_number' => '081234567892']);

        $thesis1 = Thesis::create(['title' => 'Judul 1', 'student_id' => $student1->id, 'pembimbing1_id' => $dosen->id, 'status' => 'active']);
        $thesis2 = Thesis::create(['title' => 'Judul 2', 'student_id' => $student2->id, 'pembimbing1_id' => $dosen->id, 'status' => 'active']);
        $thesis3 = Thesis::create(['title' => 'Judul 3', 'student_id' => $student3->id, 'pembimbing1_id' => $dosen->id, 'status' => 'active']);

        // Session 1: Attending
        MentoringSession::create([
            'thesis_id' => $thesis1->id,
            'dosen_id' => $dosen->id,
            'topic' => 'Topik 1',
            'type' => 'offline',
            'scheduled_at' => now()->addDay(),
            'status' => 'approved',
            'student_attendance_status' => 'attending',
            'student_confirmed_at' => now(),
        ]);

        // Session 2: Permission with reason
        MentoringSession::create([
            'thesis_id' => $thesis2->id,
            'dosen_id' => $dosen->id,
            'topic' => 'Topik 2',
            'type' => 'online',
            'scheduled_at' => now()->addDay(),
            'status' => 'approved',
            'student_attendance_status' => 'permission',
            'student_attendance_reason' => 'Sakit demam',
            'student_confirmed_at' => now(),
        ]);

        // Session 3: Pending
        MentoringSession::create([
            'thesis_id' => $thesis3->id,
            'dosen_id' => $dosen->id,
            'topic' => 'Topik 3',
            'type' => 'offline',
            'scheduled_at' => now()->addDay(),
            'status' => 'approved',
            'student_attendance_status' => 'pending',
        ]);

        $response = $this->actingAs($dosen)->getJson(route('mentoring-sessions.live-attendance'));

        $response->assertOk();
        $response->assertJsonStructure([
            'summary' => [
                'total',
                'attending',
                'permission',
                'pending',
                'last_updated',
            ],
            'sessions' => [
                '*' => [
                    'id',
                    'student_name',
                    'student_identifier',
                    'topic',
                    'type',
                    'location',
                    'scheduled_at',
                    'attendance_status',
                    'attendance_reason',
                ]
            ]
        ]);

        $response->assertJson([
            'summary' => [
                'total' => 3,
                'attending' => 1,
                'permission' => 1,
                'pending' => 1,
            ]
        ]);
    }

    public function test_admin_can_filter_live_attendance_by_dosen(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $dosen1 = User::factory()->create(['role' => 'dosen']);
        $dosen2 = User::factory()->create(['role' => 'dosen']);

        $student1 = User::factory()->create(['role' => 'mahasiswa']);
        $student2 = User::factory()->create(['role' => 'mahasiswa']);

        $thesis1 = Thesis::create(['title' => 'Judul 1', 'student_id' => $student1->id, 'pembimbing1_id' => $dosen1->id, 'status' => 'active']);
        $thesis2 = Thesis::create(['title' => 'Judul 2', 'student_id' => $student2->id, 'pembimbing1_id' => $dosen2->id, 'status' => 'active']);

        MentoringSession::create([
            'thesis_id' => $thesis1->id,
            'dosen_id' => $dosen1->id,
            'topic' => 'Topik 1',
            'type' => 'offline',
            'scheduled_at' => now()->addDay(),
            'status' => 'approved',
            'student_attendance_status' => 'attending',
        ]);

        MentoringSession::create([
            'thesis_id' => $thesis2->id,
            'dosen_id' => $dosen2->id,
            'topic' => 'Topik 2',
            'type' => 'offline',
            'scheduled_at' => now()->addDay(),
            'status' => 'approved',
            'student_attendance_status' => 'permission',
            'student_attendance_reason' => 'Izin keperluan keluarga',
        ]);

        // When filtered by dosen1
        $response = $this->actingAs($admin)->getJson(route('mentoring-sessions.live-attendance', ['dosen_id' => $dosen1->id]));

        $response->assertOk();
        $response->assertJson([
            'summary' => [
                'total' => 1,
                'attending' => 1,
                'permission' => 0,
                'pending' => 0,
            ]
        ]);
    }

    public function test_student_cannot_access_live_attendance_endpoint(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $response = $this->actingAs($student)->getJson(route('mentoring-sessions.live-attendance'));
        $response->assertForbidden();
    }
}
