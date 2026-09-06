<?php

namespace Tests\Feature;

use App\Models\MentoringSession;
use App\Models\SeminarApplication;
use App\Models\Thesis;
use App\Models\ThesisDefenseApplication;
use App\Models\User;
use App\Models\Wave;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsChartTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('analytics.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_mahasiswa_and_dosen_are_forbidden(): void
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
        $dosen = User::factory()->create(['role' => 'dosen']);

        $this->actingAs($mahasiswa)->get(route('analytics.index'))->assertStatus(403);
        $this->actingAs($mahasiswa)->get(route('analytics.export-excel'))->assertStatus(403);
        $this->actingAs($mahasiswa)->get(route('analytics.export-pdf'))->assertStatus(403);

        $this->actingAs($dosen)->get(route('analytics.index'))->assertStatus(403);
        $this->actingAs($dosen)->get(route('analytics.export-excel'))->assertStatus(403);
        $this->actingAs($dosen)->get(route('analytics.export-pdf'))->assertStatus(403);
    }

    public function test_admin_and_kaprodi_can_access_analytics_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $kaprodi = User::factory()->create(['role' => 'kaprodi']);

        $p1 = User::factory()->create(['role' => 'dosen', 'name' => 'Dr. Pembimbing Satu']);
        $p2 = User::factory()->create(['role' => 'dosen', 'name' => 'Ir. Pembimbing Dua']);
        $student = User::factory()->create(['role' => 'mahasiswa', 'entry_year' => 2022]);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'pembimbing1_id' => $p1->id,
            'pembimbing2_id' => $p2->id,
            'title' => 'Sistem Analitik Data Skripsi Menggunakan Pembelajaran Mesin',
            'status' => 'active',
            'topic' => 'Kecerdasan Buatan (AI)',
        ]);

        MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => $p1->id,
            'topic' => 'Pembahasan Bab 1 Pendahuluan',
            'scheduled_at' => now()->subDays(2),
            'status' => 'completed',
            'is_absent' => false,
            'notes' => 'Bimbingan Bab 1 disetujui.',
        ]);

        // Admin checks
        $responseAdmin = $this->actingAs($admin)->get(route('analytics.index'));
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertSee('Grafik Analitik');
        $responseAdmin->assertSee('Statistik Analitik');
        $responseAdmin->assertSee('Export Excel');
        $responseAdmin->assertSee('Export PDF');
        $responseAdmin->assertSee('Unduh Semua PNG');
        $responseAdmin->assertSee('Mahasiswa Seminar per Pembimbing 1 (P1)');
        $responseAdmin->assertSee('Mahasiswa Seminar per Pembimbing 2 (P2)');
        $responseAdmin->assertSee('Status Seminar per Angkatan Mahasiswa');
        $responseAdmin->assertSee('Mahasiswa Belum Lulus per Pembimbing');

        // Kaprodi checks
        $responseKaprodi = $this->actingAs($kaprodi)->get(route('analytics.index'));
        $responseKaprodi->assertStatus(200);
        $responseKaprodi->assertSee('Grafik Analitik');
        $responseKaprodi->assertSee('Statistik Analitik');
    }

    public function test_analytics_filter_parameters_work(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $dosen = User::factory()->create(['role' => 'dosen']);
        $wave = Wave::create([
            'name' => 'Gelombang 1 2026',
            'is_active' => true,
            'start_date' => now()->subMonth(),
            'end_date' => now()->addMonth(),
        ]);

        $response = $this->actingAs($admin)->get(route('analytics.index', [
            'wave_id' => $wave->id,
            'entry_year' => 2022,
            'dosen_id' => $dosen->id,
            'status' => 'active',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Filter Aktif');
    }

    public function test_analytics_excel_export(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $dosen = User::factory()->create(['role' => 'dosen']);
        $student = User::factory()->create(['role' => 'mahasiswa', 'entry_year' => 2021]);

        Thesis::create([
            'student_id' => $student->id,
            'pembimbing1_id' => $dosen->id,
            'pembimbing2_id' => $dosen->id,
            'title' => 'Rancang Bangun Sistem Informasi Pengolahan Data',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get(route('analytics.export-excel'));
        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-disposition'), '.xlsx'));
    }

    public function test_analytics_pdf_export(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $dosen = User::factory()->create(['role' => 'dosen']);
        $student = User::factory()->create(['role' => 'mahasiswa', 'entry_year' => 2021]);

        Thesis::create([
            'student_id' => $student->id,
            'pembimbing1_id' => $dosen->id,
            'pembimbing2_id' => $dosen->id,
            'title' => 'Implementasi Keamanan Jaringan Komputer',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($admin)->get(route('analytics.export-pdf'));
        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-type'), 'application/pdf'));
    }

    public function test_analytics_cohort_range_filter_works(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $dosen = User::factory()->create(['role' => 'dosen']);

        $mhs2020 = User::factory()->create(['role' => 'mahasiswa', 'entry_year' => 2020]);
        $mhs2021 = User::factory()->create(['role' => 'mahasiswa', 'entry_year' => 2021]);
        $mhs2024 = User::factory()->create(['role' => 'mahasiswa', 'entry_year' => 2024]);

        Thesis::create([
            'student_id' => $mhs2020->id,
            'pembimbing1_id' => $dosen->id,
            'pembimbing2_id' => $dosen->id,
            'title' => 'Skripsi Angkatan 2020',
            'status' => 'active',
        ]);

        Thesis::create([
            'student_id' => $mhs2021->id,
            'pembimbing1_id' => $dosen->id,
            'pembimbing2_id' => $dosen->id,
            'title' => 'Skripsi Angkatan 2021',
            'status' => 'active',
        ]);

        Thesis::create([
            'student_id' => $mhs2024->id,
            'pembimbing1_id' => $dosen->id,
            'pembimbing2_id' => $dosen->id,
            'title' => 'Skripsi Angkatan 2024',
            'status' => 'active',
        ]);

        // Filter range: 2020 - 2022
        $response = $this->actingAs($admin)->get(route('analytics.index', [
            'entry_year_from' => 2020,
            'entry_year_to' => 2022,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Angkatan 2020 - 2022');
        $this->assertEquals(2, $response->viewData('kpi')['totalStudents']);

        // Test reverse order auto-swap: 2022 to 2020
        $responseSwap = $this->actingAs($admin)->get(route('analytics.index', [
            'entry_year_from' => 2022,
            'entry_year_to' => 2020,
        ]));
        $responseSwap->assertStatus(200);
        $this->assertEquals(2, $responseSwap->viewData('kpi')['totalStudents']);

        // Test PDF export with cohort range
        $responsePdf = $this->actingAs($admin)->get(route('analytics.export-pdf', [
            'entry_year_from' => 2020,
            'entry_year_to' => 2022,
        ]));
        $responsePdf->assertStatus(200);
    }
}
