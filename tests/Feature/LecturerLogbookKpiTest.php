<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Thesis;
use App\Models\MentoringSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LecturerLogbookKpiTest extends TestCase
{
    use RefreshDatabase;

    public function test_lecturer_logbook_kpi_cards_and_filtering()
    {
        $dosen = User::factory()->create(['role' => 'dosen', 'name' => 'Dr. Dosen Test']);
        $dosen2 = User::factory()->create(['role' => 'dosen', 'name' => 'Dr. Partner Test']);

        // Student 1: 0 sessions (stalled), entry_year 2021
        $mhs1 = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Nol Sesi', 'entry_year' => 2021]);
        $thesis1 = Thesis::create([
            'student_id' => $mhs1->id,
            'title' => 'Sistem Informasi Akademik A',
            'status' => 'active',
            'pembimbing1_id' => $dosen->id,
            'pembimbing2_id' => $dosen2->id,
            'created_at' => now()->subDays(20),
        ]);

        // Student 2: 4 sessions (ready_up), entry_year 2022
        $mhs2 = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Siap UP', 'entry_year' => 2022]);
        $thesis2 = Thesis::create([
            'student_id' => $mhs2->id,
            'title' => 'Sistem Rekomendasi B',
            'status' => 'active',
            'pembimbing1_id' => $dosen->id,
            'pembimbing2_id' => $dosen2->id,
        ]);
        for ($i = 0; $i < 4; $i++) {
            MentoringSession::create([
                'thesis_id' => $thesis2->id,
                'dosen_id' => $dosen->id,
                'topic' => "Bimbingan UP ke-{$i}",
                'status' => 'completed',
                'is_absent' => false,
                'scheduled_at' => now()->subDays(3 - $i),
            ]);
        }

        // Student 3: 8 sessions (ready_sidang), entry_year 2022
        $mhs3 = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Siap Sidang', 'entry_year' => 2022]);
        $thesis3 = Thesis::create([
            'student_id' => $mhs3->id,
            'title' => 'Deep Learning Vision C',
            'status' => 'active',
            'pembimbing1_id' => $dosen2->id,
            'pembimbing2_id' => $dosen->id, // As P2
        ]);
        for ($i = 0; $i < 8; $i++) {
            MentoringSession::create([
                'thesis_id' => $thesis3->id,
                'dosen_id' => $dosen->id,
                'topic' => "Bimbingan Sidang ke-{$i}",
                'status' => 'completed',
                'is_absent' => false,
                'scheduled_at' => now()->subDays(2),
            ]);
        }

        // Student 4: 2 sessions but last session was 20 days ago (stalled, proposal stage), entry_year 2021
        $mhs4 = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Pasif', 'entry_year' => 2021]);
        $thesis4 = Thesis::create([
            'student_id' => $mhs4->id,
            'title' => 'Blockchain IoT D',
            'status' => 'active',
            'pembimbing1_id' => $dosen->id,
            'pembimbing2_id' => $dosen2->id,
        ]);
        MentoringSession::create([
            'thesis_id' => $thesis4->id,
            'dosen_id' => $dosen->id,
            'topic' => 'Bimbingan Awal',
            'status' => 'completed',
            'is_absent' => false,
            'scheduled_at' => now()->subDays(20),
        ]);

        // Student 5: Graduated / Completed student
        $mhs5 = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Sudah Lulus', 'entry_year' => 2020]);
        $thesis5 = Thesis::create([
            'student_id' => $mhs5->id,
            'title' => 'Sistem Lama Sudah Lulus E',
            'status' => 'completed',
            'pembimbing1_id' => $dosen->id,
            'pembimbing2_id' => $dosen2->id,
            'acc_sidang_p1' => true,
            'acc_sidang_p2' => true,
        ]);

        // 1. Visit index as dosen: check stats and ensure graduated student is NOT in active list
        $response = $this->actingAs($dosen)->get(route('logbooks.index'));
        $response->assertStatus(200);
        $response->assertDontSee('Mahasiswa Sudah Lulus'); // Graduated student excluded from active logbooks!
        $response->assertViewHas('stats', function ($stats) {
            return $stats['total'] === 4 // Only active students
                && $stats['p1'] === 3
                && $stats['p2'] === 1
                && $stats['proposal'] === 2 // mhs1 (0) + mhs4 (1)
                && $stats['ready_up'] === 2
                && $stats['ready_sidang'] === 1
                && $stats['stalled'] === 2
                && $stats['graduated_total'] === 1; // 1 graduated student
        });

        // 2. Filter: ready_sidang
        $sidangResponse = $this->actingAs($dosen)->get(route('logbooks.index', ['filter' => 'ready_sidang']));
        $sidangResponse->assertStatus(200);
        $sidangResponse->assertSee('Mahasiswa Siap Sidang');
        $sidangResponse->assertDontSee('Mahasiswa Nol Sesi');
        $sidangResponse->assertDontSee('Mahasiswa Pasif');
        $sidangResponse->assertDontSee('Mahasiswa Sudah Lulus');

        // 3. Filter: stalled
        $stalledResponse = $this->actingAs($dosen)->get(route('logbooks.index', ['filter' => 'stalled']));
        $stalledResponse->assertStatus(200);
        $stalledResponse->assertSee('Mahasiswa Nol Sesi');
        $stalledResponse->assertSee('Mahasiswa Pasif');
        $stalledResponse->assertDontSee('Mahasiswa Siap Sidang');
        $stalledResponse->assertDontSee('Mahasiswa Sudah Lulus');

        // 4. Filter: ready_up
        $upResponse = $this->actingAs($dosen)->get(route('logbooks.index', ['filter' => 'ready_up']));
        $upResponse->assertStatus(200);
        $upResponse->assertSee('Mahasiswa Siap UP');
        $upResponse->assertSee('Mahasiswa Siap Sidang');
        $upResponse->assertDontSee('Mahasiswa Nol Sesi');
        $upResponse->assertDontSee('Mahasiswa Pasif');
        $upResponse->assertDontSee('Mahasiswa Sudah Lulus');

        // 5. Combined: filter ready_up + search 'Rekomendasi'
        $searchResponse = $this->actingAs($dosen)->get(route('logbooks.index', ['filter' => 'ready_up', 'search' => 'Rekomendasi']));
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Mahasiswa Siap UP');
        $searchResponse->assertDontSee('Mahasiswa Siap Sidang');

        // 6. Filter: proposal (< 4 sessions)
        $proposalResponse = $this->actingAs($dosen)->get(route('logbooks.index', ['filter' => 'proposal']));
        $proposalResponse->assertStatus(200);
        $proposalResponse->assertSee('Mahasiswa Nol Sesi');
        $proposalResponse->assertSee('Mahasiswa Pasif');
        $proposalResponse->assertDontSee('Mahasiswa Siap UP');
        $proposalResponse->assertDontSee('Mahasiswa Siap Sidang');

        // 7. Filter: role_filter = p1 (Pembimbing 1)
        $roleP1Response = $this->actingAs($dosen)->get(route('logbooks.index', ['role_filter' => 'p1']));
        $roleP1Response->assertStatus(200);
        $roleP1Response->assertSee('Mahasiswa Siap UP');
        $roleP1Response->assertDontSee('Mahasiswa Siap Sidang'); // Dosen is P2 for mhs3

        // 8. Filter: role_filter = p2 (Pembimbing 2)
        $roleP2Response = $this->actingAs($dosen)->get(route('logbooks.index', ['role_filter' => 'p2']));
        $roleP2Response->assertStatus(200);
        $roleP2Response->assertSee('Mahasiswa Siap Sidang');
        $roleP2Response->assertDontSee('Mahasiswa Siap UP');

        // 9. Filter: entry_year = 2021
        $entryYearResponse = $this->actingAs($dosen)->get(route('logbooks.index', ['entry_year' => 2021]));
        $entryYearResponse->assertStatus(200);
        $entryYearResponse->assertSee('Mahasiswa Nol Sesi');
        $entryYearResponse->assertSee('Mahasiswa Pasif');
        $entryYearResponse->assertDontSee('Mahasiswa Siap UP');
        $entryYearResponse->assertDontSee('Mahasiswa Siap Sidang');

        // 10. View Completed / Graduated Tab: graduated student appears here
        $completedResponse = $this->actingAs($dosen)->get(route('logbooks.index', ['status' => 'completed']));
        $completedResponse->assertStatus(200);
        $completedResponse->assertSee('Mahasiswa Sudah Lulus');
        $completedResponse->assertSee('Lulus');
        $completedResponse->assertDontSee('Mahasiswa Siap Sidang');

        // 11. Check Dual Progress Bar and Quick Filter UI elements
        $activeResponse = $this->actingAs($dosen)->get(route('logbooks.index'));
        $activeResponse->assertSee('Target UP');
        $activeResponse->assertSee('Target Sidang');
        $activeResponse->assertSee('4/4 Sesi');
        $activeResponse->assertSee('8/8 Sesi');
        $activeResponse->assertSee('PROGRES TARGET BIMBINGAN');
        $activeResponse->assertSee('Tab Filter Cepat');
        $activeResponse->assertSee('Filter Peran:');
        $activeResponse->assertSee('Sebagai Pembimbing 1');
        $activeResponse->assertSee('Sebagai Pembimbing 2');
        $activeResponse->assertSee('Kategori Progres:');
        $activeResponse->assertSee('Tahap Proposal (< 4 sesi)');
        $activeResponse->assertSee('Siap UP (≥ 4 sesi)');
        $activeResponse->assertSee('Siap Sidang (≥ 8 sesi)');
        $activeResponse->assertSee('Macet (> 14 hari)');
        $activeResponse->assertSee('Semua Angkatan');
        $activeResponse->assertSee('Angkatan 2021');
        $activeResponse->assertSee('Angkatan 2022');
    }
}
