<?php

namespace Tests\Feature;

use App\Models\Thesis;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccReadinessFilterFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_monitoring_acc_status_filters_and_kpi_metrics()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $p1 = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Satu']);
        $p2 = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Dua']);

        $mhs1 = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mhs Siap Seminar']);
        $mhs2 = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mhs Siap Sidang']);
        $mhs3 = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mhs Parsial Satu']);
        $mhs4 = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mhs Belum Siap']);

        // 1. Siap Seminar UP (acc_up_p1 = true, acc_up_p2 = true)
        Thesis::create([
            'student_id' => $mhs1->id,
            'title' => 'Sistem Rekomendasi 1',
            'status' => 'active',
            'pembimbing1_id' => $p1->id,
            'pembimbing2_id' => $p2->id,
            'acc_up_p1' => true,
            'acc_up_p2' => true,
        ]);

        // 2. Siap Sidang Akhir (acc_sidang_p1 = true, acc_sidang_p2 = true)
        Thesis::create([
            'student_id' => $mhs2->id,
            'title' => 'Sistem Rekomendasi 2',
            'status' => 'active',
            'pembimbing1_id' => $p1->id,
            'pembimbing2_id' => $p2->id,
            'acc_up_p1' => true,
            'acc_up_p2' => true,
            'acc_sidang_p1' => true,
            'acc_sidang_p2' => true,
        ]);

        // 3. Parsial (P1 sudah, P2 belum)
        Thesis::create([
            'student_id' => $mhs3->id,
            'title' => 'Sistem Rekomendasi 3',
            'status' => 'active',
            'pembimbing1_id' => $p1->id,
            'pembimbing2_id' => $p2->id,
            'acc_up_p1' => true,
            'acc_up_p2' => false,
        ]);

        // 4. Belum ACC sama sekali
        Thesis::create([
            'student_id' => $mhs4->id,
            'title' => 'Sistem Rekomendasi 4',
            'status' => 'active',
            'pembimbing1_id' => $p1->id,
            'pembimbing2_id' => $p2->id,
            'acc_up_p1' => false,
            'acc_up_p2' => false,
            'acc_sidang_p1' => false,
            'acc_sidang_p2' => false,
        ]);

        // Filter default (all): all 4 students visible
        $resAll = $this->actingAs($admin)->get(route('monitoring.index'));
        $resAll->assertStatus(200);
        $resAll->assertSee('Mhs Siap Seminar');
        $resAll->assertSee('Mhs Siap Sidang');
        $resAll->assertSee('Mhs Parsial Satu');
        $resAll->assertSee('Mhs Belum Siap');
        $resAll->assertSee('Siap Seminar UP');
        $resAll->assertSee('Siap Sidang Akhir');

        // Filter: ready_defense (Only Mhs Siap Sidang)
        $resDefense = $this->actingAs($admin)->get(route('monitoring.index', ['acc_status' => 'ready_defense']));
        $resDefense->assertStatus(200);
        $resDefense->assertSee('Mhs Siap Sidang');
        $resDefense->assertDontSee('Mhs Parsial Satu');
        $resDefense->assertDontSee('Mhs Belum Siap');

        // Filter: ready_seminar (Mhs Siap Seminar & Mhs Siap Sidang both have full UP ACC)
        $resSeminar = $this->actingAs($admin)->get(route('monitoring.index', ['acc_status' => 'ready_seminar']));
        $resSeminar->assertStatus(200);
        $resSeminar->assertSee('Mhs Siap Seminar');
        $resSeminar->assertSee('Mhs Siap Sidang');
        $resSeminar->assertDontSee('Mhs Parsial Satu');
        $resSeminar->assertDontSee('Mhs Belum Siap');

        // Filter: partial_acc (Only Mhs Parsial Satu)
        $resPartial = $this->actingAs($admin)->get(route('monitoring.index', ['acc_status' => 'partial_acc']));
        $resPartial->assertStatus(200);
        $resPartial->assertSee('Mhs Parsial Satu');
        $resPartial->assertDontSee('Mhs Siap Sidang');
        $resPartial->assertDontSee('Mhs Belum Siap');

        // Filter: in_mentoring (Only Mhs Belum Siap)
        $resMentoring = $this->actingAs($admin)->get(route('monitoring.index', ['acc_status' => 'in_mentoring']));
        $resMentoring->assertStatus(200);
        $resMentoring->assertSee('Mhs Belum Siap');
        $resMentoring->assertDontSee('Mhs Siap Seminar');
        $resMentoring->assertDontSee('Mhs Siap Sidang');
    }

    public function test_dosen_can_filter_theses_by_acc_status()
    {
        $dosen = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Pembimbing']);
        $partner = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Rekan']);

        $mhsA = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mhs Menunggu ACC Saya']);
        $mhsB = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mhs Tunggu Rekan']);
        $mhsC = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mhs Keduanya ACC']);

        // A. Belum di-ACC oleh Dosen (sebagai P1)
        Thesis::create([
            'student_id' => $mhsA->id,
            'title' => 'Judul A',
            'status' => 'active',
            'pembimbing1_id' => $dosen->id,
            'pembimbing2_id' => $partner->id,
            'acc_up_p1' => false,
            'acc_up_p2' => false,
        ]);

        // B. Sudah di-ACC oleh Dosen (P1), tapi Partner (P2) belum
        Thesis::create([
            'student_id' => $mhsB->id,
            'title' => 'Judul B',
            'status' => 'active',
            'pembimbing1_id' => $dosen->id,
            'pembimbing2_id' => $partner->id,
            'acc_up_p1' => true,
            'acc_up_p2' => false,
        ]);

        // C. Keduanya sudah ACC UP
        Thesis::create([
            'student_id' => $mhsC->id,
            'title' => 'Judul C',
            'status' => 'active',
            'pembimbing1_id' => $dosen->id,
            'pembimbing2_id' => $partner->id,
            'acc_up_p1' => true,
            'acc_up_p2' => true,
        ]);

        // Dosen filter: my_acc_pending
        $resPending = $this->actingAs($dosen)->get(route('theses.index', ['acc_status' => 'my_acc_pending']));
        $resPending->assertStatus(200);
        $resPending->assertSee('Mhs Menunggu ACC Saya');
        $resPending->assertDontSee('Mhs Keduanya ACC');

        // Dosen filter: waiting_partner
        $resPartner = $this->actingAs($dosen)->get(route('theses.index', ['acc_status' => 'waiting_partner']));
        $resPartner->assertStatus(200);
        $resPartner->assertSee('Mhs Tunggu Rekan');
        $resPartner->assertDontSee('Mhs Menunggu ACC Saya');
        $resPartner->assertDontSee('Mhs Keduanya ACC');

        // Dosen filter: ready_seminar
        $resReady = $this->actingAs($dosen)->get(route('theses.index', ['acc_status' => 'ready_seminar']));
        $resReady->assertStatus(200);
        $resReady->assertSee('Mhs Keduanya ACC');
        $resReady->assertDontSee('Mhs Menunggu ACC Saya');
        $resReady->assertDontSee('Mhs Tunggu Rekan');
    }

    public function test_export_respects_acc_status()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $p1 = User::factory()->create(['role' => 'dosen']);
        $mhs = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mhs Sidang Export']);

        Thesis::create([
            'student_id' => $mhs->id,
            'title' => 'Skripsi Export Testing',
            'status' => 'active',
            'pembimbing1_id' => $p1->id,
            'acc_sidang_p1' => true,
            'acc_sidang_p2' => true,
        ]);

        $res = $this->actingAs($admin)->get(route('monitoring.export', [
            'type' => 'mahasiswa',
            'format' => 'excel',
            'acc_status' => 'ready_defense',
        ]));

        $res->assertStatus(200);
        $this->assertStringContainsString('Laporan_Mahasiswa', $res->headers->get('content-disposition'));
    }

    public function test_monitoring_export_filters_and_mapping()
    {
        $dosenA = User::factory()->create(['role' => 'dosen', 'name' => 'Dr. Alpha']);
        $dosenB = User::factory()->create(['role' => 'dosen', 'name' => 'Dr. Beta']);

        $mhs2021 = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Budi Santoso', 'identifier' => '2110001', 'entry_year' => 2021]);
        $mhs2022 = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Siti Nurhaliza', 'identifier' => '2210002', 'entry_year' => 2022]);

        $thesis1 = Thesis::create([
            'student_id' => $mhs2021->id,
            'title' => 'Implementasi Machine Learning pada IoT',
            'status' => 'active',
            'pembimbing1_id' => $dosenA->id,
            'pembimbing2_id' => $dosenB->id,
            'acc_up_p1' => true,
            'acc_up_p2' => true,
            'acc_sidang_p1' => false,
            'acc_sidang_p2' => false,
        ]);

        $thesis2 = Thesis::create([
            'student_id' => $mhs2022->id,
            'title' => 'Rancang Bangun Sistem Informasi Berbasis Flutter',
            'status' => 'active',
            'pembimbing1_id' => $dosenB->id,
            'pembimbing2_id' => null,
            'acc_up_p1' => true,
            'acc_up_p2' => true,
            'acc_sidang_p1' => true,
            'acc_sidang_p2' => true,
        ]);

        // 1. Filter by pembimbingId (dosenA) -> should return only thesis1
        $exportPembimbingA = new \App\Exports\MonitoringExport('akademik', null, null, 'all', $dosenA->id);
        $colA = $exportPembimbingA->collection();
        $this->assertCount(1, $colA);
        $this->assertEquals($thesis1->id, $colA->first()->id);

        // 2. Filter by entryYear (2022) -> should return only thesis2
        $exportYear2022 = new \App\Exports\MonitoringExport('akademik', null, null, 'all', null, 2022);
        $colYear = $exportYear2022->collection();
        $this->assertCount(1, $colYear);
        $this->assertEquals($thesis2->id, $colYear->first()->id);

        // 3. Filter by search ('Machine Learning') -> should return thesis1
        $exportSearch = new \App\Exports\MonitoringExport('akademik', null, null, 'all', null, null, 'Machine Learning');
        $colSearch = $exportSearch->collection();
        $this->assertCount(1, $colSearch);
        $this->assertEquals($thesis1->id, $colSearch->first()->id);

        // 4. Filter by accStatus ('ready_defense') -> should return thesis2
        $exportAccDefense = new \App\Exports\MonitoringExport('akademik', null, null, 'ready_defense');
        $colAcc = $exportAccDefense->collection();
        $this->assertCount(1, $colAcc);
        $this->assertEquals($thesis2->id, $colAcc->first()->id);

        // 5. Test Heading and Map row
        $headings = $exportAccDefense->headings();
        $this->assertContains('STATUS ACC SEMINAR (UP)', $headings);
        $this->assertContains('STATUS ACC SIDANG', $headings);
        $this->assertCount(13, $headings);

        $mapped = $exportAccDefense->map($thesis2);
        $this->assertCount(13, $mapped);
        $this->assertEquals('2210002', $mapped[1]);
        $this->assertEquals('Siti Nurhaliza', $mapped[2]);
        $this->assertEquals(2022, $mapped[3]);
        $this->assertEquals('Siap Sidang (ACC P1 & P2)', $mapped[11]);
    }
}
