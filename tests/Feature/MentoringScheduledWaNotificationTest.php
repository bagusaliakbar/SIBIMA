<?php

namespace Tests\Feature;

use App\Channels\FonnteChannel;
use App\Models\MentoringSession;
use App\Models\Thesis;
use App\Models\User;
use App\Notifications\MentoringScheduledByDosenNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MentoringScheduledWaNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_dosen_scheduling_mentoring_sends_wa_and_db_notification_to_student()
    {
        Notification::fake();

        $dosen = User::factory()->create(['role' => 'dosen', 'name' => 'Dr. Budi Santoso, M.Kom']);
        $student = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'Ahmad Mahasiswa',
            'phone_number' => '081234567890',
        ]);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Rancang Bangun Sistem Informasi Bimbingan',
            'status' => 'active',
            'pembimbing1_id' => $dosen->id,
        ]);

        $startDate = now()->addDays(2)->format('Y-m-d');

        $response = $this->actingAs($dosen)->post(route('mentoring-sessions.store'), [
            'thesis_id' => $thesis->id,
            'scheduled_date' => $startDate,
            'scheduled_hour' => '09',
            'scheduled_minute' => '00',
            'scheduled_end_time' => '11:00',
            'topic' => 'Bimbingan Bab 1 dan 2',
            'type' => 'offline',
            'location' => 'Ruang Dosen Lt. 2',
        ]);

        $response->assertRedirect();

        Notification::assertSentTo(
            $student,
            MentoringScheduledByDosenNotification::class,
            function ($notification, $channels) use ($student) {
                // Verify channels
                $this->assertContains(FonnteChannel::class, $channels);
                $this->assertContains('database', $channels);

                // Verify WhatsApp message content includes full time range
                $waMessage = $notification->toFonnte($student);
                $this->assertStringContainsString('JADWAL BIMBINGAN SKRIPSI BARU', $waMessage);
                $this->assertStringContainsString('Ahmad Mahasiswa', $waMessage);
                $this->assertStringContainsString('Dr. Budi Santoso, M.Kom', $waMessage);
                $this->assertStringContainsString('Bimbingan Bab 1 dan 2', $waMessage);
                $this->assertStringContainsString('09:00 - 11:00 WIB', $waMessage);
                $this->assertStringContainsString('Konfirmasi Kehadiran', $waMessage);

                // Verify database notification payload contains topic and notes
                $data = $notification->toArray($student);
                $this->assertEquals('Bimbingan Bab 1 dan 2', $data['topic']);
                $this->assertArrayHasKey('notes', $data);
                $this->assertStringContainsString('Topik: Bimbingan Bab 1 dan 2', $data['message']);

                return true;
            }
        );
    }

    public function test_dosen_scheduling_mass_mentoring_sends_notification_to_all_active_students()
    {
        Notification::fake();

        $dosen = User::factory()->create(['role' => 'dosen', 'name' => 'Dr. Budi Santoso, M.Kom']);

        $student1 = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa 1', 'phone_number' => '081111111111']);
        $student2 = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa 2', 'phone_number' => '082222222222']);

        $thesis1 = Thesis::create([
            'student_id' => $student1->id,
            'title' => 'Skripsi Mahasiswa Satu',
            'status' => 'active',
            'pembimbing1_id' => $dosen->id,
        ]);

        $thesis2 = Thesis::create([
            'student_id' => $student2->id,
            'title' => 'Skripsi Mahasiswa Dua',
            'status' => 'active',
            'pembimbing1_id' => $dosen->id,
        ]);

        $response = $this->actingAs($dosen)->post(route('mentoring-sessions.store'), [
            'thesis_id' => 'all',
            'scheduled_at' => now()->addDays(3)->format('Y-m-d 09:00'),
            'scheduled_end_at' => now()->addDays(3)->format('Y-m-d 11:00'),
            'topic' => 'Briefing Metodologi Penelitian & Progres Bab 3',
            'type' => 'offline',
            'location' => 'Lab Komputer 1',
        ]);

        $response->assertRedirect();

        Notification::assertSentTo($student1, MentoringScheduledByDosenNotification::class);
        Notification::assertSentTo($student2, MentoringScheduledByDosenNotification::class);
    }

    public function test_scheduled_end_time_is_required_and_prevents_overlapping_ranges_while_allowing_back_to_back()
    {
        Notification::fake();

        $dosen = User::factory()->create(['role' => 'dosen']);
        $student1 = User::factory()->create(['role' => 'mahasiswa']);
        $student2 = User::factory()->create(['role' => 'mahasiswa']);

        $thesis1 = Thesis::create(['student_id' => $student1->id, 'title' => 'Skripsi 1', 'status' => 'active', 'pembimbing1_id' => $dosen->id]);
        $thesis2 = Thesis::create(['student_id' => $student2->id, 'title' => 'Skripsi 2', 'status' => 'active', 'pembimbing1_id' => $dosen->id]);

        $date = now()->addDays(4)->format('Y-m-d');

        // 1. Missing end time fails validation
        $this->actingAs($dosen)->post(route('mentoring-sessions.store'), [
            'thesis_id' => $thesis1->id,
            'scheduled_date' => $date,
            'scheduled_hour' => '09',
            'scheduled_minute' => '00',
            'topic' => 'Tanpa Jam Selesai',
            'type' => 'offline',
        ])->assertSessionHasErrors('scheduled_end_at');

        // 2. End time <= start time fails validation
        $this->actingAs($dosen)->post(route('mentoring-sessions.store'), [
            'thesis_id' => $thesis1->id,
            'scheduled_date' => $date,
            'scheduled_hour' => '10',
            'scheduled_minute' => '00',
            'scheduled_end_time' => '09:30',
            'topic' => 'Jam Selesai Lebih Awal',
            'type' => 'offline',
        ])->assertSessionHasErrors('scheduled_end_at');

        // 3. Valid 09:00 - 11:00 succeeds
        $this->actingAs($dosen)->post(route('mentoring-sessions.store'), [
            'thesis_id' => $thesis1->id,
            'scheduled_date' => $date,
            'scheduled_hour' => '09',
            'scheduled_minute' => '00',
            'scheduled_end_time' => '11:00',
            'topic' => 'Sesi Pagi',
            'type' => 'offline',
        ])->assertRedirect();

        // 4. Overlapping 10:00 - 11:30 is blocked
        $this->actingAs($dosen)->post(route('mentoring-sessions.store'), [
            'thesis_id' => $thesis2->id,
            'scheduled_date' => $date,
            'scheduled_hour' => '10',
            'scheduled_minute' => '00',
            'scheduled_end_time' => '11:30',
            'topic' => 'Sesi Bentrok',
            'type' => 'offline',
        ])->assertSessionHasErrors('scheduled_at');

        // 5. Back-to-back 11:00 - 12:00 is allowed
        $this->actingAs($dosen)->post(route('mentoring-sessions.store'), [
            'thesis_id' => $thesis2->id,
            'scheduled_date' => $date,
            'scheduled_hour' => '11',
            'scheduled_minute' => '00',
            'scheduled_end_time' => '12:00',
            'topic' => 'Sesi Berikutnya',
            'type' => 'offline',
        ])->assertRedirect();

        $this->assertDatabaseHas('mentoring_sessions', [
            'thesis_id' => $thesis2->id,
            'scheduled_at' => "{$date} 11:00:00",
            'scheduled_end_at' => "{$date} 12:00:00",
        ]);
    }
}
