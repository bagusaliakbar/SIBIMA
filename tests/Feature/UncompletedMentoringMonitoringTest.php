<?php

namespace Tests\Feature;

use App\Models\MentoringSession;
use App\Models\Thesis;
use App\Models\User;
use App\Services\WaBroadcastService;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class UncompletedMentoringMonitoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_mahasiswa_and_dosen_cannot_access_uncompleted_mentoring(): void
    {
        $mhs = User::factory()->create(['role' => 'mahasiswa']);
        $dosen = User::factory()->create(['role' => 'dosen']);

        $this->actingAs($mhs)->get(route('monitoring.uncompleted-mentoring'))->assertStatus(403);
        $this->actingAs($dosen)->get(route('monitoring.uncompleted-mentoring'))->assertStatus(403);

        $this->actingAs($mhs)->get(route('monitoring.uncompleted-mentoring.export-excel'))->assertStatus(403);
        $this->actingAs($dosen)->get(route('monitoring.uncompleted-mentoring.export-excel'))->assertStatus(403);
    }

    public function test_admin_and_kaprodi_can_access_and_view_uncompleted_sessions(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $kaprodi = User::factory()->create(['role' => 'kaprodi']);

        $dosen = User::factory()->create(['role' => 'dosen', 'name' => 'Dr. Budi Santoso, M.Kom']);
        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Ahmad Fulan', 'identifier' => 'D1A210055']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'pembimbing1_id' => $dosen->id,
            'title' => 'Sistem Pemantauan Cerdas Berbasis IoT',
            'status' => 'active',
        ]);

        // Sesi yang sudah lewat waktu tapi belum diselesaikan (overdue)
        MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => $dosen->id,
            'scheduled_at' => Carbon::now()->subDays(3),
            'topic' => 'Pembahasan Bab 3 Metodologi Penelitian',
            'status' => 'approved',
        ]);

        // Sesi mendatang
        MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => $dosen->id,
            'scheduled_at' => Carbon::now()->addDays(2),
            'topic' => 'Diskusi Bab 4 Hasil Pengujian',
            'status' => 'approved',
        ]);

        // Admin view per dosen
        $resAdmin = $this->actingAs($admin)->get(route('monitoring.uncompleted-mentoring', ['scope' => 'all', 'view' => 'dosen']));
        $resAdmin->assertStatus(200);
        $resAdmin->assertSee('Dr. Budi Santoso, M.Kom');
        $resAdmin->assertSee('Pembahasan Bab 3 Metodologi Penelitian');
        $resAdmin->assertSee('1 Sesi Terlewat');
        $resAdmin->assertDontSee('Diskusi Bab 4 Hasil Pengujian');

        // Kaprodi view flat session
        $resKaprodi = $this->actingAs($kaprodi)->get(route('monitoring.uncompleted-mentoring', ['scope' => 'all', 'view' => 'session']));
        $resKaprodi->assertStatus(200);
        $resKaprodi->assertSee('Pembahasan Bab 3 Metodologi Penelitian');
        $resKaprodi->assertDontSee('Diskusi Bab 4 Hasil Pengujian');
    }

    public function test_send_uncompleted_mentoring_reminder_to_lecturer(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $dosen = User::factory()->create([
            'role' => 'dosen',
            'name' => 'Prof. Haryono',
            'phone_number' => '081234567899',
        ]);
        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Siti Aminah']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'pembimbing1_id' => $dosen->id,
            'title' => 'Analisis Sentimen Twitter',
            'status' => 'active',
        ]);

        MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => $dosen->id,
            'scheduled_at' => Carbon::now()->subDays(2),
            'topic' => 'Review Naskah Bab 2',
            'status' => 'approved',
        ]);

        $mockWa = Mockery::mock(WhatsAppService::class);
        $mockWa->shouldReceive('sendMessage')
            ->once()
            ->with('081234567899', Mockery::type('string'))
            ->andReturn(true);

        $this->app->instance(WhatsAppService::class, $mockWa);

        $response = $this->actingAs($admin)->post(route('monitoring.uncompleted-mentoring.remind'), [
            'dosen_id' => $dosen->id,
        ]);

        $response->assertSessionHas('success');
    }

    public function test_wa_broadcast_target_audience_for_dosen_belum_selesai(): void
    {
        $dosen = User::factory()->create([
            'role' => 'dosen',
            'name' => 'Ir. Hendra',
            'phone_number' => '08987654321',
        ]);
        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Rian Hidayat']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'pembimbing1_id' => $dosen->id,
            'title' => 'Sistem Rekomendasi Tempat Wisata',
            'status' => 'active',
        ]);

        MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => $dosen->id,
            'scheduled_at' => Carbon::now()->subDay(),
            'topic' => 'Revisi Bab 1 Latar Belakang',
            'status' => 'approved',
        ]);

        /** @var WaBroadcastService $broadcastService */
        $broadcastService = app(WaBroadcastService::class);
        $recipients = $broadcastService->getTargetRecipients('dosen_belum_selesai_bimbingan');

        $this->assertNotEmpty($recipients);
        $target = $recipients->firstWhere('user_id', $dosen->id);
        $this->assertNotNull($target);
        $this->assertEquals('Ir. Hendra', $target['name']);
        $this->assertStringContainsString('Revisi Bab 1 Latar Belakang', $target['context']['daftar_mahasiswa']);
    }

    public function test_export_uncompleted_mentoring_excel(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('monitoring.uncompleted-mentoring.export-excel'));
        $response->assertStatus(200);
        $this->assertStringContainsString('spreadsheetml', $response->headers->get('content-type'));
    }
}
