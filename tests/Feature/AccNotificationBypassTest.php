<?php

namespace Tests\Feature;

use App\Channels\FonnteChannel;
use App\Models\Thesis;
use App\Models\User;
use App\Notifications\AccNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AccNotificationBypassTest extends TestCase
{
    use RefreshDatabase;

    public function test_kaprodi_bypass_acc_all_uses_kaprodi_label_instead_of_personal_name(): void
    {
        Notification::fake();

        $kaprodi = User::factory()->create(['role' => 'kaprodi', 'name' => 'Bagus Ali Akbar, S.Si., M.Kom']);
        $p1 = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen P1']);
        $p2 = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen P2']);
        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Ani Mahasiswa']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'pembimbing1_id' => $p1->id,
            'pembimbing2_id' => $p2->id,
            'title' => 'Sistem Akademik Kampus',
            'status' => 'active',
        ]);

        $response = $this->actingAs($kaprodi)->post(route('theses.toggle-acc', [$thesis->id, 'up']), [
            'slot' => 'all',
        ]);

        $response->assertRedirect();

        Notification::assertSentTo(
            $student,
            AccNotification::class,
            function (AccNotification $notification, array $channels) use ($student) {
                $this->assertContains(FonnteChannel::class, $channels);
                $this->assertContains('database', $channels);

                $waMessage = $notification->toFonnte($student);
                $this->assertStringContainsString('Kaprodi (Bypass ACC)', $waMessage);
                $this->assertStringNotContainsString('Bagus Ali Akbar', $waMessage);

                $arrayData = $notification->toArray($student);
                $this->assertStringContainsString('Kaprodi (Bypass ACC)', $arrayData['message']);
                $this->assertStringNotContainsString('Bagus Ali Akbar', $arrayData['message']);

                return true;
            }
        );
    }

    public function test_kaprodi_single_slot_via_monitoring_uses_kaprodi_label_even_when_also_advisor(): void
    {
        Notification::fake();

        $kaprodi = User::factory()->create(['role' => 'kaprodi', 'name' => 'Bagus Ali Akbar, S.Si., M.Kom']);
        $p2 = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen P2']);
        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Budi Mahasiswa']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'pembimbing1_id' => $kaprodi->id,
            'pembimbing2_id' => $p2->id,
            'title' => 'Implementasi AI Pendidikan',
            'status' => 'active',
        ]);

        $response = $this->actingAs($kaprodi)->post(route('theses.toggle-acc', [$thesis->id, 'sidang']), [
            'slot' => 'p1',
            'via_kaprodi' => '1',
        ]);

        $response->assertRedirect();

        Notification::assertSentTo(
            $student,
            AccNotification::class,
            function (AccNotification $notification) use ($student) {
                $waMessage = $notification->toFonnte($student);
                $this->assertStringContainsString('Kaprodi (Bypass ACC)', $waMessage);
                $this->assertStringNotContainsString('Bagus Ali Akbar', $waMessage);

                return true;
            }
        );
    }

    public function test_regular_advisor_acc_uses_lecturer_name(): void
    {
        Notification::fake();

        $p1 = User::factory()->create(['role' => 'dosen', 'name' => 'Ir. Syarif Hidayat, M.T']);
        $p2 = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen P2']);
        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Citra Mahasiswa']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'pembimbing1_id' => $p1->id,
            'pembimbing2_id' => $p2->id,
            'title' => 'Aplikasi Mobile Kampus',
            'status' => 'active',
        ]);

        $response = $this->actingAs($p1)->post(route('theses.toggle-acc', [$thesis->id, 'up']));

        $response->assertRedirect();

        Notification::assertSentTo(
            $student,
            AccNotification::class,
            function (AccNotification $notification) use ($student) {
                $waMessage = $notification->toFonnte($student);
                $this->assertStringContainsString('Ir. Syarif Hidayat, M.T', $waMessage);
                $this->assertStringNotContainsString('Bypass ACC', $waMessage);

                return true;
            }
        );
    }
}
