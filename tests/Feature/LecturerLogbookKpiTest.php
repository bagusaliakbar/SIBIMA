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

        // Student 1: 0 sessions (stalled)
        $mhs1 = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Nol Sesi']);
        $thesis1 = Thesis::create([
            'student_id' => $mhs1->id,
            'title' => 'Sistem Informasi Akademik A',
            'status' => 'active',
            'pembimbing1_id' => $dosen->id,
            'pembimbing2_id' => $dosen2->id,
            'created_at' => now()->subDays(20),
        ]);

        // Student 2: 4 sessions (ready_up)
        $mhs2 = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Siap UP']);
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

        // Student 3: 8 sessions (ready_sidang)
        $mhs3 = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Siap Sidang']);
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

        // Student 4: 2 sessions but last session was 20 days ago (stalled)
        $mhs4 = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Pasif']);
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

        // 1. Visit index as dosen: check stats
        $response = $this->actingAs($dosen)->get(route('logbooks.index'));
        $response->assertStatus(200);
        $response->assertViewHas('stats', function ($stats) {
            return $stats['total'] === 4
                && $stats['p1'] === 3
                && $stats['p2'] === 1
                && $stats['ready_up'] === 2
                && $stats['ready_sidang'] === 1
                && $stats['stalled'] === 2;
        });

        // 2. Filter: ready_sidang
        $sidangResponse = $this->actingAs($dosen)->get(route('logbooks.index', ['filter' => 'ready_sidang']));
        $sidangResponse->assertStatus(200);
        $sidangResponse->assertSee('Mahasiswa Siap Sidang');
        $sidangResponse->assertDontSee('Mahasiswa Nol Sesi');
        $sidangResponse->assertDontSee('Mahasiswa Pasif');

        // 3. Filter: stalled
        $stalledResponse = $this->actingAs($dosen)->get(route('logbooks.index', ['filter' => 'stalled']));
        $stalledResponse->assertStatus(200);
        $stalledResponse->assertSee('Mahasiswa Nol Sesi');
        $stalledResponse->assertSee('Mahasiswa Pasif');
        $stalledResponse->assertDontSee('Mahasiswa Siap Sidang');

        // 4. Filter: ready_up
        $upResponse = $this->actingAs($dosen)->get(route('logbooks.index', ['filter' => 'ready_up']));
        $upResponse->assertStatus(200);
        $upResponse->assertSee('Mahasiswa Siap UP');
        $upResponse->assertSee('Mahasiswa Siap Sidang');
        $upResponse->assertDontSee('Mahasiswa Nol Sesi');
        $upResponse->assertDontSee('Mahasiswa Pasif');

        // 5. Combined: filter ready_up + search 'Rekomendasi'
        $searchResponse = $this->actingAs($dosen)->get(route('logbooks.index', ['filter' => 'ready_up', 'search' => 'Rekomendasi']));
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Mahasiswa Siap UP');
        $searchResponse->assertDontSee('Mahasiswa Siap Sidang');
    }
}
