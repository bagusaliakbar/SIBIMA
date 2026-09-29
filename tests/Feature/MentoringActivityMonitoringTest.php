<?php

namespace Tests\Feature;

use App\Models\MentoringSession;
use App\Models\Thesis;
use App\Models\User;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class MentoringActivityMonitoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_mahasiswa_and_dosen_cannot_access_activity_monitoring(): void
    {
        $mhs = User::factory()->create(['role' => 'mahasiswa']);
        $dosen = User::factory()->create(['role' => 'dosen']);

        $this->actingAs($mhs)->get(route('monitoring.activity'))->assertStatus(403);
        $this->actingAs($dosen)->get(route('monitoring.activity'))->assertStatus(403);
    }

    public function test_admin_and_kaprodi_can_access_activity_monitoring(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $kaprodi = User::factory()->create(['role' => 'kaprodi']);

        $dosen1 = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Aktif Sekali']);
        $dosen2 = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Pasif']);

        $mhsRajin = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Budi Terajin', 'identifier' => 'D1A210001']);
        $mhsPasif = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Iwan Pasif', 'identifier' => 'D1A210002']);

        $thesisRajin = Thesis::create([
            'student_id' => $mhsRajin->id,
            'pembimbing1_id' => $dosen1->id,
            'pembimbing2_id' => $dosen2->id,
            'title' => 'Implementasi Machine Learning',
            'status' => 'approved',
        ]);

        $thesisPasif = Thesis::create([
            'student_id' => $mhsPasif->id,
            'pembimbing1_id' => $dosen1->id,
            'pembimbing2_id' => $dosen2->id,
            'title' => 'Sistem Rekomendasi Buku',
            'status' => 'approved',
        ]);

        // Sesi selesai bulan ini untuk mhsRajin & dosen1
        MentoringSession::create([
            'thesis_id' => $thesisRajin->id,
            'dosen_id' => $dosen1->id,
            'scheduled_at' => Carbon::now()->subDays(2),
            'status' => 'completed',
            'is_absent' => false,
            'topic' => 'Bab 1 dan 2',
        ]);

        MentoringSession::create([
            'thesis_id' => $thesisRajin->id,
            'dosen_id' => $dosen1->id,
            'scheduled_at' => Carbon::now()->subDays(5),
            'status' => 'completed',
            'is_absent' => false,
            'topic' => 'Bab 3 Metodologi',
        ]);

        // Response for Admin
        $responseAdmin = $this->actingAs($admin)->get(route('monitoring.activity'));
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertSee('Radar Keaktifan');
        $responseAdmin->assertSee('Budi Terajin');
        $responseAdmin->assertSee('Dosen Aktif Sekali');
        $responseAdmin->assertSee('2 Sesi');

        // Response for Kaprodi
        $responseKaprodi = $this->actingAs($kaprodi)->get(route('monitoring.activity'));
        $responseKaprodi->assertStatus(200);
        $responseKaprodi->assertSee('Budi Terajin');
    }

    public function test_send_activity_reminder_via_whatsapp(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'Agus Santoso',
            'phone_number' => '081234567890',
        ]);

        $mockWa = Mockery::mock(WhatsAppService::class);
        $mockWa->shouldReceive('sendMessage')
            ->once()
            ->with('081234567890', Mockery::type('string'))
            ->andReturn(true);

        $this->app->instance(WhatsAppService::class, $mockWa);

        $response = $this->actingAs($admin)->post(route('monitoring.activity.remind'), [
            'type' => 'student',
            'user_id' => $student->id,
            'days_inactive' => 30,
        ]);

        $response->assertSessionHas('success');
    }

    public function test_export_excel_and_pdf(): void
    {
        $kaprodi = User::factory()->create(['role' => 'kaprodi']);

        $responseExcel = $this->actingAs($kaprodi)->get(route('monitoring.activity.export-excel'));
        $responseExcel->assertStatus(200);
        $this->assertStringContainsString('spreadsheetml', $responseExcel->headers->get('content-type'));

        $responsePdf = $this->actingAs($kaprodi)->get(route('monitoring.activity.export-pdf'));
        $responsePdf->assertStatus(200);
        $this->assertEquals('application/pdf', $responsePdf->headers->get('content-type'));
    }
}
