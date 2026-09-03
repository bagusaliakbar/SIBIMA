<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Thesis;
use App\Models\MentoringSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentLogbookEnhancementTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_logbook_loads_with_enhanced_features_and_data()
    {
        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Test']);
        $p1 = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Pembimbing 1']);
        $p2 = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Pembimbing 2']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Rancang Bangun Sistem Informasi Monitoring Skripsi',
            'status' => 'active',
            'pembimbing1_id' => $p1->id,
            'pembimbing2_id' => $p2->id,
            'acc_up_p1' => true,
            'acc_up_p2' => false,
            'acc_sidang_p1' => false,
            'acc_sidang_p2' => false,
        ]);

        // Create 3 sessions with P1
        $s1 = MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => $p1->id,
            'topic' => 'Diskusi Rumusan Masalah',
            'notes' => 'Catatan bab 1',
            'feedback' => 'Perbaiki latar belakang dan batasan masalah',
            'status' => 'completed',
            'scheduled_at' => now()->subDays(10),
            'type' => 'offline',
            'location' => 'Ruang Dosen 201',
        ]);

        $s2 = MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => $p1->id,
            'topic' => 'Review Metodologi Waterfall',
            'notes' => 'Bab 3 sudah siap',
            'feedback' => 'Ganti diagram flowchart dengan activity diagram',
            'status' => 'completed',
            'scheduled_at' => now()->subDays(5),
            'type' => 'online',
            'location' => 'https://meet.google.com/abc-defg-hij',
        ]);

        // Create 1 session with P2
        $s3 = MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => $p2->id,
            'topic' => 'Review Pengujian Blackbox',
            'notes' => 'Bab 4 dan pengujian',
            'feedback' => 'Tambahkan skenario pengujian boundary value analysis',
            'status' => 'completed',
            'scheduled_at' => now()->subDays(2),
            'type' => 'offline',
            'location' => 'Lab Komputer',
        ]);

        // 1. Visit Logbook Index as student
        $response = $this->actingAs($student)->get(route('logbooks.index'));
        $response->assertStatus(200);

        // Assert thesis title & mentor names rendered
        $response->assertSee('Rancang Bangun Sistem Informasi Monitoring Skripsi');
        $response->assertSee('Dosen Pembimbing 1');
        $response->assertSee('Dosen Pembimbing 2');
        $response->assertSee('ACC Seminar');

        // Assert chronological badges & counts
        $response->assertSee('Bimbingan #1');
        $response->assertSee('Bimbingan #2');
        $response->assertSee('Bimbingan #3');
        $response->assertSee('Google Meet');
        $response->assertSee('Ruang Dosen 201');

        // 2. Test Search by lecturer feedback (which was previously not searchable)
        $searchResponse = $this->actingAs($student)->get(route('logbooks.index', ['search' => 'activity diagram']));
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Review Metodologi Waterfall');
        $searchResponse->assertDontSee('Diskusi Rumusan Masalah');
        $searchResponse->assertDontSee('Review Pengujian Blackbox');

        // 3. Test Filter by Pembimbing 1 (P1)
        $filterP1Response = $this->actingAs($student)->get(route('logbooks.index', ['dosen' => 'p1']));
        $filterP1Response->assertStatus(200);
        $filterP1Response->assertSee('Diskusi Rumusan Masalah');
        $filterP1Response->assertSee('Review Metodologi Waterfall');
        $filterP1Response->assertDontSee('Review Pengujian Blackbox');

        // 4. Test Filter by Pembimbing 2 (P2)
        $filterP2Response = $this->actingAs($student)->get(route('logbooks.index', ['dosen' => 'p2']));
        $filterP2Response->assertStatus(200);
        $filterP2Response->assertSee('Review Pengujian Blackbox');
        $filterP2Response->assertDontSee('Diskusi Rumusan Masalah');
        $filterP2Response->assertDontSee('Review Metodologi Waterfall');
    }
}
