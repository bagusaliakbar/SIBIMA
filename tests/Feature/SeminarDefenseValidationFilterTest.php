<?php

namespace Tests\Feature;

use App\Models\SeminarApplication;
use App\Models\ThesisDefenseApplication;
use App\Models\Thesis;
use App\Models\User;
use App\Models\Wave;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeminarDefenseValidationFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_filter_seminar_applications_by_status_and_search()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $p1 = User::factory()->create(['role' => 'dosen']);
        $p2 = User::factory()->create(['role' => 'dosen']);

        $wave = Wave::create([
            'name' => 'Gelombang 1 Test',
            'start_date' => now()->subDays(5),
            'end_date' => now()->addDays(5),
            'is_active' => true,
        ]);

        $student1 = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Budi Santoso', 'identifier' => 'D1A220001']);
        $thesis1 = Thesis::create([
            'student_id' => $student1->id,
            'title' => 'Sistem Informasi Akademik Terpadu',
            'abstract' => 'Abstrak skripsi Budi',
            'status' => 'active',
            'pembimbing1_id' => $p1->id,
            'pembimbing2_id' => $p2->id,
        ]);
        $app1 = SeminarApplication::create([
            'thesis_id' => $thesis1->id,
            'wave_id' => $wave->id,
            'status' => 'pending',
            'file_acc_pembimbing' => 'seminars/acc1.pdf',
            'file_pembayaran' => 'seminars/pay1.pdf',
            'file_kartu_bimbingan' => 'seminars/card1.pdf',
            'file_skripsi' => 'seminars/draft1.pdf',
            'file_formulir' => 'seminars/form1.pdf',
        ]);

        $student2 = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Siti Rahma', 'identifier' => 'D1A220002']);
        $thesis2 = Thesis::create([
            'student_id' => $student2->id,
            'title' => 'Penerapan Machine Learning Pada Citra Medis',
            'abstract' => 'Abstrak skripsi Siti',
            'status' => 'active',
            'pembimbing1_id' => $p1->id,
            'pembimbing2_id' => $p2->id,
        ]);
        $app2 = SeminarApplication::create([
            'thesis_id' => $thesis2->id,
            'wave_id' => $wave->id,
            'status' => 'approved',
            'file_acc_pembimbing' => 'seminars/acc2.pdf',
            'file_pembayaran' => 'seminars/pay2.pdf',
            'file_kartu_bimbingan' => 'seminars/card2.pdf',
            'file_skripsi' => 'seminars/draft2.pdf',
            'file_formulir' => 'seminars/form2.pdf',
        ]);

        // 1. Visit index: should see both and stats
        $response = $this->actingAs($admin)->get(route('seminar-applications.index', ['wave_id' => $wave->id]));
        $response->assertStatus(200);
        $response->assertSee('Budi Santoso');
        $response->assertSee('Siti Rahma');
        $response->assertSee('Total Pendaftar');
        $response->assertSee('Menunggu Validasi');
        $response->assertSee('Disetujui / Siap Jadwal');

        // 2. Filter by status: pending
        $responsePending = $this->actingAs($admin)->get(route('seminar-applications.index', [
            'wave_id' => $wave->id,
            'status' => 'pending'
        ]));
        $responsePending->assertStatus(200);
        $responsePending->assertSee('Budi Santoso');
        $responsePending->assertDontSee('Siti Rahma');

        // 3. Filter by search: "Machine Learning"
        $responseSearch = $this->actingAs($admin)->get(route('seminar-applications.index', [
            'wave_id' => $wave->id,
            'search' => 'Machine Learning'
        ]));
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee('Siti Rahma');
        $responseSearch->assertDontSee('Budi Santoso');
    }

    public function test_admin_can_filter_thesis_defense_applications_by_status_and_search()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $p1 = User::factory()->create(['role' => 'dosen']);
        $p2 = User::factory()->create(['role' => 'dosen']);

        $wave = Wave::create([
            'name' => 'Gelombang 1 Sidang',
            'start_date' => now()->subDays(5),
            'end_date' => now()->addDays(5),
            'is_active' => true,
        ]);

        $student1 = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Ahmad Fauzi', 'identifier' => 'D1A220003']);
        $thesis1 = Thesis::create([
            'student_id' => $student1->id,
            'title' => 'Rancang Bangun IoT Smart Campus',
            'abstract' => 'Abstrak sidang Ahmad',
            'status' => 'active',
            'pembimbing1_id' => $p1->id,
            'pembimbing2_id' => $p2->id,
        ]);
        $defense1 = ThesisDefenseApplication::create([
            'thesis_id' => $thesis1->id,
            'wave_id' => $wave->id,
            'status' => 'pending',
            'file_formulir' => 'defenses/form1.pdf',
            'file_transkrip' => 'defenses/trans1.pdf',
            'file_acc_pembimbing' => 'defenses/acc1.pdf',
            'file_logbook' => 'defenses/log1.pdf',
            'file_pembayaran' => 'defenses/pay1.pdf',
            'file_skripsi' => 'defenses/skripsi1.pdf',
            'file_ktm' => 'defenses/ktm1.pdf',
            'file_pkkmb_univ' => 'defenses/pkkmb1.pdf',
            'file_pkkmb_fak' => 'defenses/pkkmb2.pdf',
            'file_makrab' => 'defenses/makrab1.pdf',
            'file_cisco' => 'defenses/cisco1.pdf',
            'file_workshop' => 'defenses/ws1.pdf',
            'file_organisasi' => 'defenses/org1.pdf',
            'file_toefl' => 'defenses/toefl1.pdf',
            'file_kewirausahaan' => 'defenses/kwu1.pdf',
            'file_tahsin' => 'defenses/tahsin1.pdf',
            'file_komputer' => 'defenses/comp1.pdf',
            'file_perpus_pinjam' => 'defenses/lib1.pdf',
            'file_perpus_sumbang' => 'defenses/lib2.pdf',
            'file_ijazah' => 'defenses/ijazah1.pdf',
        ]);

        $student2 = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Dewi Lestari', 'identifier' => 'D1A220004']);
        $thesis2 = Thesis::create([
            'student_id' => $student2->id,
            'title' => 'Analisis Algoritma Kriptografi Modern',
            'abstract' => 'Abstrak sidang Dewi',
            'status' => 'active',
            'pembimbing1_id' => $p1->id,
            'pembimbing2_id' => $p2->id,
        ]);
        $defense2 = ThesisDefenseApplication::create([
            'thesis_id' => $thesis2->id,
            'wave_id' => $wave->id,
            'status' => 'rejected',
            'admin_feedback' => 'Scan sertifikat TOEFL kurang jelas',
            'file_formulir' => 'defenses/form2.pdf',
            'file_transkrip' => 'defenses/trans2.pdf',
            'file_acc_pembimbing' => 'defenses/acc2.pdf',
            'file_logbook' => 'defenses/log2.pdf',
            'file_pembayaran' => 'defenses/pay2.pdf',
            'file_skripsi' => 'defenses/skripsi2.pdf',
            'file_ktm' => 'defenses/ktm2.pdf',
            'file_pkkmb_univ' => 'defenses/pkkmb1.pdf',
            'file_pkkmb_fak' => 'defenses/pkkmb2.pdf',
            'file_makrab' => 'defenses/makrab1.pdf',
            'file_cisco' => 'defenses/cisco1.pdf',
            'file_workshop' => 'defenses/ws1.pdf',
            'file_organisasi' => 'defenses/org1.pdf',
            'file_toefl' => 'defenses/toefl1.pdf',
            'file_kewirausahaan' => 'defenses/kwu1.pdf',
            'file_tahsin' => 'defenses/tahsin1.pdf',
            'file_komputer' => 'defenses/comp1.pdf',
            'file_perpus_pinjam' => 'defenses/lib1.pdf',
            'file_perpus_sumbang' => 'defenses/lib2.pdf',
            'file_ijazah' => 'defenses/ijazah1.pdf',
        ]);

        // 1. Visit index: should see both and stats
        $response = $this->actingAs($admin)->get(route('thesis-defense-applications.index', ['wave_id' => $wave->id]));
        $response->assertStatus(200);
        $response->assertSee('Ahmad Fauzi');
        $response->assertSee('Dewi Lestari');
        $response->assertSee('Perlu verifikasi 20 berkas');

        // 2. Filter by status: rejected
        $responseRej = $this->actingAs($admin)->get(route('thesis-defense-applications.index', [
            'wave_id' => $wave->id,
            'status' => 'rejected'
        ]));
        $responseRej->assertStatus(200);
        $responseRej->assertSee('Dewi Lestari');
        $responseRej->assertSee('Scan sertifikat TOEFL kurang jelas');
        $responseRej->assertDontSee('Ahmad Fauzi');

        // 3. Search by NPM
        $responseSearch = $this->actingAs($admin)->get(route('thesis-defense-applications.index', [
            'wave_id' => $wave->id,
            'search' => 'D1A220003'
        ]));
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee('Ahmad Fauzi');
        $responseSearch->assertDontSee('Dewi Lestari');
    }
}
