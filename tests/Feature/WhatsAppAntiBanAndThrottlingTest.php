<?php

namespace Tests\Feature;

use App\Channels\FonnteChannel;
use App\Models\MentoringSession;
use App\Models\Setting;
use App\Models\Thesis;
use App\Models\User;
use App\Notifications\MentoringStatusUpdatedNotification;
use App\Services\MentoringService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class WhatsAppAntiBanAndThrottlingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::setWhatsAppEnabled(true);
        Setting::resetWhatsAppCircuit();
        Cache::forget('wa_next_dispatch_timestamp');
    }

    public function test_circuit_breaker_can_be_tripped_and_reset()
    {
        $this->assertFalse(Setting::isWhatsAppCircuitTripped());

        // Trip the circuit breaker
        Cache::put('wa_circuit_breaker_tripped', true, now()->addMinutes(30));
        Cache::put('wa_circuit_breaker_reason', 'Gateway error: device disconnected', now()->addMinutes(30));

        $this->assertTrue(Setting::isWhatsAppCircuitTripped());
        $this->assertEquals('Gateway error: device disconnected', Setting::getWhatsAppCircuitReason());

        // Reset it
        Setting::resetWhatsAppCircuit();

        $this->assertFalse(Setting::isWhatsAppCircuitTripped());
        $this->assertNull(Setting::getWhatsAppCircuitReason());
    }

    public function test_fonnte_channel_skips_sending_when_circuit_breaker_is_active()
    {
        Http::fake();

        // Trip circuit breaker
        Cache::put('wa_circuit_breaker_tripped', true, now()->addMinutes(30));
        Cache::put('wa_circuit_breaker_reason', 'Device banned');

        $user = User::factory()->create([
            'phone_number' => '081234567890',
        ]);

        $dosen = User::factory()->create(['role' => 'dosen']);
        $thesis = Thesis::create([
            'student_id' => $user->id,
            'pembimbing1_id' => $dosen->id,
            'title' => 'Sistem Informasi Kampus',
            'status' => 'active',
        ]);
        $session = MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => $dosen->id,
            'topic' => 'Bab 1 Evaluasi',
            'scheduled_at' => Carbon::now()->addDay(),
            'status' => 'approved',
        ]);

        $notification = new MentoringStatusUpdatedNotification($session, 'completed', 'Catatan revisi', true);
        $channel = new FonnteChannel();
        $channel->send($user, $notification);

        // No HTTP requests should have been sent to Fonnte
        Http::assertNothingSent();
    }

    public function test_past_session_defaults_to_silent_whatsapp_notification_to_prevent_mass_bans()
    {
        Notification::fake();

        $student = User::factory()->create(['role' => 'mahasiswa', 'phone_number' => '081234567890']);
        $dosen = User::factory()->create(['role' => 'dosen']);
        $thesis = Thesis::create([
            'student_id' => $student->id,
            'pembimbing1_id' => $dosen->id,
            'title' => 'Sistem Informasi Kampus',
            'status' => 'active',
        ]);

        // Session that was scheduled 3 days ago (historical backlog)
        $pastSession = MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => $dosen->id,
            'scheduled_at' => Carbon::now()->subDays(3),
            'topic' => 'Bab 1 Pendahuluan',
            'status' => 'approved',
        ]);

        $service = app(MentoringService::class);
        $service->updateStatus($pastSession, [
            'status' => 'completed',
            'feedback' => 'Sudah oke.',
        ]);

        // Notification should have been sent with sendWhatsApp = false
        Notification::assertSentTo($student, MentoringStatusUpdatedNotification::class, function ($notif) {
            return $notif->sendWhatsApp === false;
        });
    }

    public function test_current_or_future_session_defaults_to_active_whatsapp_notification()
    {
        Notification::fake();

        $student = User::factory()->create(['role' => 'mahasiswa', 'phone_number' => '081234567890']);
        $dosen = User::factory()->create(['role' => 'dosen']);
        $thesis = Thesis::create([
            'student_id' => $student->id,
            'pembimbing1_id' => $dosen->id,
            'title' => 'Sistem Informasi Kampus',
            'status' => 'active',
        ]);

        // Session scheduled today or in future
        $futureSession = MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => $dosen->id,
            'scheduled_at' => Carbon::now()->addHours(2),
            'topic' => 'Bab 2 Tinjauan Pustaka',
            'status' => 'pending',
        ]);

        $service = app(MentoringService::class);
        $service->updateStatus($futureSession, [
            'status' => 'approved',
        ]);

        // Notification should have sendWhatsApp = true
        Notification::assertSentTo($student, MentoringStatusUpdatedNotification::class, function ($notif) {
            return $notif->sendWhatsApp === true;
        });
    }

    public function test_bulk_update_status_defaults_to_silent_whatsapp()
    {
        Notification::fake();

        $student1 = User::factory()->create(['role' => 'mahasiswa']);
        $student2 = User::factory()->create(['role' => 'mahasiswa']);
        $dosen = User::factory()->create(['role' => 'dosen']);

        $thesis1 = Thesis::create([
            'student_id' => $student1->id,
            'pembimbing1_id' => $dosen->id,
            'title' => 'Judul Skripsi 1',
            'status' => 'active',
        ]);
        $thesis2 = Thesis::create([
            'student_id' => $student2->id,
            'pembimbing1_id' => $dosen->id,
            'title' => 'Judul Skripsi 2',
            'status' => 'active',
        ]);

        $session1 = MentoringSession::create([
            'thesis_id' => $thesis1->id,
            'dosen_id' => $dosen->id,
            'scheduled_at' => Carbon::now()->addDay(),
            'topic' => 'Topik 1',
            'status' => 'approved',
        ]);
        $session2 = MentoringSession::create([
            'thesis_id' => $thesis2->id,
            'dosen_id' => $dosen->id,
            'scheduled_at' => Carbon::now()->addDay(),
            'topic' => 'Topik 2',
            'status' => 'approved',
        ]);

        $service = app(MentoringService::class);
        $service->bulkUpdateStatus(collect([$session1, $session2]), [
            'status' => 'completed',
            'feedback' => 'Catatan massal.',
        ]);

        // For bulk updates without explicit notify_student_wa = true, sendWhatsApp should be false
        Notification::assertSentTo($student1, MentoringStatusUpdatedNotification::class, function ($notif) {
            return $notif->sendWhatsApp === false;
        });
        Notification::assertSentTo($student2, MentoringStatusUpdatedNotification::class, function ($notif) {
            return $notif->sendWhatsApp === false;
        });
    }

    public function test_admin_can_reset_circuit_via_controller_route()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Trip circuit breaker
        Cache::put('wa_circuit_breaker_tripped', true, now()->addMinutes(30));
        $this->assertTrue(Setting::isWhatsAppCircuitTripped());

        $response = $this->actingAs($admin)->post(route('wa-templates.reset-circuit'));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertFalse(Setting::isWhatsAppCircuitTripped());
    }
}
