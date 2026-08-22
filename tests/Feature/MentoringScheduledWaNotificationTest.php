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

        $response = $this->actingAs($dosen)->post(route('mentoring-sessions.store'), [
            'thesis_id' => $thesis->id,
            'scheduled_at' => now()->addDays(2)->format('Y-m-d H:i'),
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

                // Verify WhatsApp message content
                $waMessage = $notification->toFonnte($student);
                $this->assertStringContainsString('JADWAL BIMBINGAN SKRIPSI BARU', $waMessage);
                $this->assertStringContainsString('Ahmad Mahasiswa', $waMessage);
                $this->assertStringContainsString('Dr. Budi Santoso, M.Kom', $waMessage);
                $this->assertStringContainsString('Bimbingan Bab 1 dan 2', $waMessage);
                $this->assertStringContainsString('Konfirmasi Kehadiran', $waMessage);

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
            'scheduled_at' => now()->addDays(3)->format('Y-m-d H:i'),
            'topic' => 'Briefing Metodologi Penelitian & Progres Bab 3',
            'type' => 'offline',
            'location' => 'Lab Komputer 1',
        ]);

        $response->assertRedirect();

        Notification::assertSentTo($student1, MentoringScheduledByDosenNotification::class);
        Notification::assertSentTo($student2, MentoringScheduledByDosenNotification::class);
    }
}
