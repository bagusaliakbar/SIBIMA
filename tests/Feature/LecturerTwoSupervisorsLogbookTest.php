<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Thesis;
use App\Models\MentoringSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LecturerTwoSupervisorsLogbookTest extends TestCase
{
    use RefreshDatabase;

    public function test_pembimbing1_and_pembimbing2_can_view_each_others_logbook_sessions()
    {
        $p1 = User::factory()->create(['role' => 'dosen', 'name' => 'Prof. Dosen Pembimbing 1']);
        $p2 = User::factory()->create(['role' => 'dosen', 'name' => 'Dr. Dosen Pembimbing 2']);
        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Budi Mahasiswa', 'identifier' => '2106002']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Sistem Cerdas Rekomendasi Jurnal Ilmiah',
            'status' => 'active',
            'pembimbing1_id' => $p1->id,
            'pembimbing2_id' => $p2->id,
        ]);

        // Sesi dengan Pembimbing 1
        $sessionP1 = MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => $p1->id,
            'topic' => 'Bimbingan Bab 1 dengan P1',
            'notes' => 'Catatan mahasiswa untuk P1',
            'feedback' => 'Arahan revisi latar belakang dari Pembimbing 1',
            'status' => 'completed',
            'is_absent' => false,
            'scheduled_at' => now()->subDays(5),
        ]);

        // Sesi dengan Pembimbing 2
        $sessionP2 = MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => $p2->id,
            'topic' => 'Bimbingan Bab 2 dengan P2',
            'notes' => 'Catatan mahasiswa untuk P2',
            'feedback' => 'Arahan tinjauan pustaka dari Pembimbing 2',
            'status' => 'completed',
            'is_absent' => false,
            'scheduled_at' => now()->subDays(2),
        ]);

        // 1. Pembimbing 1 membuka halaman show logbook
        // Pembimbing 1 HARUS bisa melihat sesi dari Pembimbing 2 dan sesinya sendiri
        $responseP1 = $this->actingAs($p1)->get(route('theses.logbooks', $thesis->id));
        $responseP1->assertStatus(200);
        $responseP1->assertSee('Bimbingan Bab 1 dengan P1');
        $responseP1->assertSee('Bimbingan Bab 2 dengan P2');
        $responseP1->assertSee('Arahan tinjauan pustaka dari Pembimbing 2');
        $responseP1->assertSee('Arahan revisi latar belakang dari Pembimbing 1');

        // 2. Pembimbing 2 membuka halaman show logbook
        // Pembimbing 2 HARUS bisa melihat sesi dari Pembimbing 1 dan sesinya sendiri
        $responseP2 = $this->actingAs($p2)->get(route('theses.logbooks', $thesis->id));
        $responseP2->assertStatus(200);
        $responseP2->assertSee('Bimbingan Bab 1 dengan P1');
        $responseP2->assertSee('Bimbingan Bab 2 dengan P2');
        $responseP2->assertSee('Arahan revisi latar belakang dari Pembimbing 1');
        $responseP2->assertSee('Arahan tinjauan pustaka dari Pembimbing 2');

        // 3. Filter Pembimbing 1 (?dosen=p1)
        $filterP1Response = $this->actingAs($p2)->get(route('theses.logbooks', ['thesis' => $thesis->id, 'dosen' => 'p1']));
        $filterP1Response->assertStatus(200);
        $filterP1Response->assertSee('Bimbingan Bab 1 dengan P1');
        $filterP1Response->assertDontSee('Bimbingan Bab 2 dengan P2');

        // 4. Filter Pembimbing 2 (?dosen=p2)
        $filterP2Response = $this->actingAs($p1)->get(route('theses.logbooks', ['thesis' => $thesis->id, 'dosen' => 'p2']));
        $filterP2Response->assertStatus(200);
        $filterP2Response->assertSee('Bimbingan Bab 2 dengan P2');
        $filterP2Response->assertDontSee('Bimbingan Bab 1 dengan P1');

        // 5. Quick Preview untuk Pembimbing 1: melihat sesi dari kedua pembimbing
        $previewP1 = $this->actingAs($p1)->getJson(route('theses.logbooks.quick-preview', $thesis->id));
        $previewP1->assertStatus(200);
        $previewP1->assertJson([
            'total_completed' => 2,
            'p1_completed' => 1,
            'p2_completed' => 1,
        ]);
        $previewP1Data = $previewP1->json();
        $this->assertCount(2, $previewP1Data['sessions']);

        // 6. Quick Preview untuk Pembimbing 2: melihat sesi dari kedua pembimbing
        $previewP2 = $this->actingAs($p2)->getJson(route('theses.logbooks.quick-preview', $thesis->id));
        $previewP2->assertStatus(200);
        $previewP2->assertJson([
            'total_completed' => 2,
            'p1_completed' => 1,
            'p2_completed' => 1,
        ]);
    }
}
