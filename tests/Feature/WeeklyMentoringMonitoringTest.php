<?php

namespace Tests\Feature;

use App\Models\MentoringSession;
use App\Models\Thesis;
use App\Models\User;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeeklyMentoringMonitoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthorized_users_cannot_access_weekly_monitoring()
    {
        // 1. Guest redirected to login
        $response = $this->get(route('monitoring.weekly'));
        $response->assertRedirect(route('login'));

        // 2. Mahasiswa gets 403
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $this->actingAs($student)->get(route('monitoring.weekly'))->assertStatus(403);

        // 3. Dosen gets 403
        $dosen = User::factory()->create(['role' => 'dosen']);
        $this->actingAs($dosen)->get(route('monitoring.weekly'))->assertStatus(403);
    }

    public function test_kaprodi_and_admin_can_access_weekly_monitoring()
    {
        $kaprodi = User::factory()->create(['role' => 'kaprodi']);
        $admin = User::factory()->create(['role' => 'admin']);

        $responseKaprodi = $this->actingAs($kaprodi)->get(route('monitoring.weekly'));
        $responseKaprodi->assertStatus(200);
        $responseKaprodi->assertViewIs('monitoring.weekly');
        $responseKaprodi->assertSee('Monitoring Bimbingan Mingguan');

        $responseAdmin = $this->actingAs($admin)->get(route('monitoring.weekly'));
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertViewIs('monitoring.weekly');
    }

    public function test_weekly_mentoring_compliance_status_and_kpi_stats()
    {
        // Fix test week to a known Monday - Sunday
        $monday = Carbon::parse('2026-09-07 09:00:00'); // Monday
        $wednesday = Carbon::parse('2026-09-09 10:00:00'); // Wednesday
        $friday = Carbon::parse('2026-09-11 14:00:00'); // Friday
        $lastWeek = Carbon::parse('2026-09-01 10:00:00'); // Previous week

        $admin = User::factory()->create(['role' => 'admin']);
        $p1 = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Pembimbing 1']);
        $p2 = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Pembimbing 2']);

        // Student A: Compliant (Mentored with both P1 and P2)
        $studentA = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Lengkap', 'identifier' => 'MHS001']);
        $thesisA = Thesis::create([
            'student_id' => $studentA->id,
            'title' => 'Skripsi Mahasiswa A',
            'pembimbing1_id' => $p1->id,
            'pembimbing2_id' => $p2->id,
            'status' => 'active',
        ]);
        MentoringSession::create([
            'thesis_id' => $thesisA->id,
            'dosen_id' => $p1->id,
            'topic' => 'Bimbingan Bab 1 ke P1',
            'scheduled_at' => $monday,
            'status' => 'completed',
            'is_absent' => false,
        ]);
        MentoringSession::create([
            'thesis_id' => $thesisA->id,
            'dosen_id' => $p2->id,
            'topic' => 'Bimbingan Bab 1 ke P2',
            'scheduled_at' => $wednesday,
            'status' => 'completed',
            'is_absent' => false,
        ]);

        // Student B: Partial (Only mentored with P1 this week)
        $studentB = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Parsial', 'identifier' => 'MHS002']);
        $thesisB = Thesis::create([
            'student_id' => $studentB->id,
            'title' => 'Skripsi Mahasiswa B',
            'pembimbing1_id' => $p1->id,
            'pembimbing2_id' => $p2->id,
            'status' => 'active',
        ]);
        MentoringSession::create([
            'thesis_id' => $thesisB->id,
            'dosen_id' => $p1->id,
            'topic' => 'Bimbingan Bab 2 ke P1',
            'scheduled_at' => $friday,
            'status' => 'completed',
            'is_absent' => false,
        ]);

        // Student C: Inactive (0 sessions this week)
        $studentC = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Pasif', 'identifier' => 'MHS003']);
        $thesisC = Thesis::create([
            'student_id' => $studentC->id,
            'title' => 'Skripsi Mahasiswa C',
            'pembimbing1_id' => $p1->id,
            'pembimbing2_id' => $p2->id,
            'status' => 'active',
        ]);

        // Student D: Mentored in previous week, but 0 sessions this week
        $studentD = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Minggu Lalu', 'identifier' => 'MHS004']);
        $thesisD = Thesis::create([
            'student_id' => $studentD->id,
            'title' => 'Skripsi Mahasiswa D',
            'pembimbing1_id' => $p1->id,
            'pembimbing2_id' => $p2->id,
            'status' => 'active',
        ]);
        MentoringSession::create([
            'thesis_id' => $thesisD->id,
            'dosen_id' => $p1->id,
            'topic' => 'Sesi Minggu Lalu',
            'scheduled_at' => $lastWeek,
            'status' => 'completed',
            'is_absent' => false,
        ]);

        // Access weekly monitoring for 2026-09-07 week
        $response = $this->actingAs($admin)->get(route('monitoring.weekly', ['date' => '2026-09-08']));
        $response->assertStatus(200);

        $stats = $response->viewData('stats');
        $this->assertEquals(4, $stats['total']);
        $this->assertEquals(1, $stats['compliant']);
        $this->assertEquals(1, $stats['partial']);
        $this->assertEquals(2, $stats['inactive']);
        $this->assertEquals(25, $stats['compliant_percent']); // 1/4 = 25%

        $theses = $response->viewData('theses');
        $this->assertCount(4, $theses);

        // Verify Student A data
        $foundA = collect($theses->items())->firstWhere('id', $thesisA->id);
        $this->assertEquals('compliant', $foundA->weekly_compliance_status);
        $this->assertEquals(1, $foundA->weekly_p1_count);
        $this->assertEquals(1, $foundA->weekly_p2_count);

        // Verify Student B data
        $foundB = collect($theses->items())->firstWhere('id', $thesisB->id);
        $this->assertEquals('partial', $foundB->weekly_compliance_status);
        $this->assertEquals(1, $foundB->weekly_p1_count);
        $this->assertEquals(0, $foundB->weekly_p2_count);

        // Verify Student C data
        $foundC = collect($theses->items())->firstWhere('id', $thesisC->id);
        $this->assertEquals('inactive', $foundC->weekly_compliance_status);
        $this->assertEquals(0, $foundC->weekly_p1_count);
        $this->assertEquals(0, $foundC->weekly_p2_count);
    }

    public function test_filter_by_compliance_status()
    {
        $date = '2026-09-08';
        $admin = User::factory()->create(['role' => 'admin']);
        $p1 = User::factory()->create(['role' => 'dosen']);

        $studentA = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Si Rajin']);
        $thesisA = Thesis::create([
            'student_id' => $studentA->id,
            'title' => 'Skripsi A',
            'pembimbing1_id' => $p1->id,
            'status' => 'active',
        ]);
        MentoringSession::create([
            'thesis_id' => $thesisA->id,
            'dosen_id' => $p1->id,
            'topic' => 'Sesi 1',
            'scheduled_at' => Carbon::parse('2026-09-08 10:00:00'),
            'status' => 'completed',
            'is_absent' => false,
        ]);

        $studentB = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Si Malas']);
        Thesis::create([
            'student_id' => $studentB->id,
            'title' => 'Skripsi B',
            'pembimbing1_id' => $p1->id,
            'status' => 'active',
        ]);

        // Filter: Compliant
        $responseCompliant = $this->actingAs($admin)->get(route('monitoring.weekly', [
            'date' => $date,
            'compliance_status' => 'compliant',
        ]));
        $responseCompliant->assertStatus(200);
        $thesesCompliant = $responseCompliant->viewData('theses');
        $this->assertCount(1, $thesesCompliant);
        $this->assertEquals($studentA->id, $thesesCompliant->first()->student_id);

        // Filter: Inactive
        $responseInactive = $this->actingAs($admin)->get(route('monitoring.weekly', [
            'date' => $date,
            'compliance_status' => 'inactive',
        ]));
        $responseInactive->assertStatus(200);
        $thesesInactive = $responseInactive->viewData('theses');
        $this->assertCount(1, $thesesInactive);
        $this->assertEquals($studentB->id, $thesesInactive->first()->student_id);
    }

    public function test_export_weekly_excel()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Budi Santoso', 'identifier' => '12345678']);
        $p1 = User::factory()->create(['role' => 'dosen', 'name' => 'Dr. Pembimbing']);

        Thesis::create([
            'student_id' => $student->id,
            'title' => 'Implementasi Algoritma XYZ',
            'pembimbing1_id' => $p1->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get(route('monitoring.weekly.export-excel', [
            'date' => '2026-09-08',
        ]));

        $response->assertStatus(200);
        $this->assertTrue(
            str_contains($response->headers->get('content-disposition'), 'Monitoring_Bimbingan_Mingguan_')
        );
    }

    public function test_send_weekly_reminder_whatsapp()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create([
            'role' => 'mahasiswa', 
            'name' => 'Budi Santoso', 
            'phone_number' => '081234567890'
        ]);
        $p1 = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Pembimbing 1']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Skripsi Uji Coba Reminder',
            'pembimbing1_id' => $p1->id,
            'status' => 'active',
        ]);

        // Mock WhatsAppService
        $mockWa = $this->mock(WhatsAppService::class);
        $mockWa->shouldReceive('sendMessage')
            ->once()
            ->withArgs(function ($phone, $message) {
                return $phone === '081234567890' && str_contains($message, 'Budi Santoso');
            })
            ->andReturn(true);

        $response = $this->actingAs($admin)->post(route('monitoring.weekly.remind', $thesis->id), [
            'start_date' => '2026-09-07',
            'end_date' => '2026-09-13',
            'message' => 'Halo Sdr/i *Budi Santoso*, Anda belum bimbingan minggu ini.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }
}
