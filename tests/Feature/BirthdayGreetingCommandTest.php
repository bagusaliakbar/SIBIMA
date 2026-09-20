<?php

namespace Tests\Feature;

use App\Models\Thesis;
use App\Models\User;
use App\Notifications\BirthdayNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class BirthdayGreetingCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_birthday_command_sends_notification_to_thesis_students_and_lecturers()
    {
        Notification::fake();

        $today = Carbon::today();

        // 1. Eligible Student (Active Thesis, Birthday today)
        $student = User::factory()->create([
            'role' => 'mahasiswa',
            'birth_date' => $today->copy()->subYears(22)->format('Y-m-d'),
            'phone_number' => '081234567890',
            'last_birthday_wished_year' => null,
        ]);
        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Implementasi Machine Learning pada IoT',
            'status' => 'draft',
        ]);

        // 2. Eligible Lecturer (Active, Birthday today)
        $lecturer = User::factory()->create([
            'role' => 'dosen',
            'is_active' => true,
            'birth_date' => $today->copy()->subYears(40)->format('Y-m-d'),
            'phone_number' => '081987654321',
            'last_birthday_wished_year' => null,
        ]);

        // 3. Ineligible Student (Birthday tomorrow)
        $otherStudent = User::factory()->create([
            'role' => 'mahasiswa',
            'birth_date' => $today->copy()->addDay()->subYears(21)->format('Y-m-d'),
            'phone_number' => '081111222333',
        ]);
        Thesis::create([
            'student_id' => $otherStudent->id,
            'title' => 'Sistem Informasi Lain',
            'status' => 'draft',
        ]);

        // 4. Ineligible Student (Thesis already completed)
        $graduatedStudent = User::factory()->create([
            'role' => 'mahasiswa',
            'birth_date' => $today->copy()->subYears(23)->format('Y-m-d'),
            'phone_number' => '082222333444',
        ]);
        Thesis::create([
            'student_id' => $graduatedStudent->id,
            'title' => 'Skripsi Selesai',
            'status' => 'completed',
        ]);

        $this->artisan('app:send-birthday-greetings')
            ->expectsOutputToContain('Ditemukan 1 Mahasiswa Skripsi dan 1 Dosen')
            ->assertSuccessful();

        // Assert notification was dispatched to eligible celebrants
        Notification::assertSentTo($student, BirthdayNotification::class);
        Notification::assertSentTo($lecturer, BirthdayNotification::class);

        // Assert not sent to ineligible users
        Notification::assertNotSentTo($otherStudent, BirthdayNotification::class);
        Notification::assertNotSentTo($graduatedStudent, BirthdayNotification::class);

        // Assert last_birthday_wished_year updated
        $this->assertEquals($today->year, $student->fresh()->last_birthday_wished_year);
        $this->assertEquals($today->year, $lecturer->fresh()->last_birthday_wished_year);
    }

    public function test_birthday_command_skips_if_already_wished_this_year()
    {
        Notification::fake();

        $today = Carbon::today();

        $student = User::factory()->create([
            'role' => 'mahasiswa',
            'birth_date' => $today->copy()->subYears(22)->format('Y-m-d'),
            'phone_number' => '081234567890',
            'last_birthday_wished_year' => $today->year, // Already wished!
        ]);
        Thesis::create([
            'student_id' => $student->id,
            'title' => 'Judul Skripsi',
            'status' => 'draft',
        ]);

        $this->artisan('app:send-birthday-greetings')
            ->expectsOutputToContain('Tidak ada civitas akademika yang berulang tahun hari ini atau ucapan sudah terkirim.')
            ->assertSuccessful();

        Notification::assertNothingSent();
    }

    public function test_birthday_command_dry_run_does_not_send_or_update()
    {
        Notification::fake();

        $today = Carbon::today();

        $lecturer = User::factory()->create([
            'role' => 'dosen',
            'is_active' => true,
            'birth_date' => $today->copy()->subYears(35)->format('Y-m-d'),
            'phone_number' => '081987654321',
            'last_birthday_wished_year' => null,
        ]);

        $this->artisan('app:send-birthday-greetings --dry-run')
            ->expectsOutputToContain('Mode SIMULASI (--dry-run) aktif')
            ->expectsOutputToContain('[SIMULASI] Dosen: ' . $lecturer->name)
            ->assertSuccessful();

        Notification::assertNothingSent();
        $this->assertNull($lecturer->fresh()->last_birthday_wished_year);
    }
}
