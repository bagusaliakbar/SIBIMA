<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Thesis;
use App\Models\ThesisDefenseSchedule;
use App\Models\ThesisDefenseScheduleDetail;
use App\Models\SeminarSchedule;
use App\Models\SeminarScheduleDetail;
use App\Notifications\ScheduleReminderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class ScheduleReminderTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_schedule_reminders_formats_time_correctly()
    {
        Notification::fake();

        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Rika Novitasari']);
        $dosen = User::factory()->create(['role' => 'dosen', 'name' => 'Bagus Ali Akbar']);
        $pembimbing = User::factory()->create(['role' => 'dosen', 'name' => 'Pembimbing Utama']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Sistem Informasi Akademik',
            'pembimbing1_id' => $pembimbing->id,
            'status' => 'active'
        ]);

        $tomorrow = Carbon::tomorrow()->toDateString();

        $schedule = ThesisDefenseSchedule::create([
            'title' => 'Sidang Skripsi Gelombang 1',
            'date' => $tomorrow,
            'location' => 'Ruang 10',
            'chairman_id' => $dosen->id,
            'created_by' => $dosen->id,
        ]);

        $detail = ThesisDefenseScheduleDetail::create([
            'thesis_defense_schedule_id' => $schedule->id,
            'thesis_id' => $thesis->id,
            'start_time' => '09:00:00',
            'end_time' => '10:30:00',
            'examiner1_id' => $dosen->id,
            'order' => 1
        ]);

        $this->artisan('app:send-schedule-reminders')
            ->assertExitCode(0);

        Notification::assertSentTo(
            $dosen,
            ScheduleReminderNotification::class,
            function ($notification) {
                $reflection = new \ReflectionClass($notification);
                $property = $reflection->getProperty('scheduleData');
                $property->setAccessible(true);
                $data = $property->getValue($notification);

                $this->assertEquals('09:00 - 10:30 WIB', $data['time']);
                $this->assertNotEquals('2026- - 2026-', $data['time']);
                $this->assertEquals('Rika Novitasari', $data['student']);
                $this->assertEquals('Ruang 10', $data['location']);

                return true;
            }
        );
    }
}
