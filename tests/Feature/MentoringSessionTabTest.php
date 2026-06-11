<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Thesis;
use App\Models\MentoringSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MentoringSessionTabTest extends TestCase
{
    use RefreshDatabase;

    public function test_lecturer_mentoring_sessions_filtering_by_active_and_history_tabs()
    {
        $dosen = User::factory()->create(['role' => 'dosen']);
        $studentActive = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Active Student']);
        $studentGraduated = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Graduated Student']);

        // Active Thesis
        $thesisActive = Thesis::create([
            'student_id' => $studentActive->id,
            'title' => 'Sistem Akademik Aktif',
            'pembimbing1_id' => $dosen->id,
            'status' => 'active',
        ]);

        // Graduated Thesis
        $thesisGraduated = Thesis::create([
            'student_id' => $studentGraduated->id,
            'title' => 'Sistem Akademik Selesai',
            'pembimbing1_id' => $dosen->id,
            'status' => 'completed',
        ]);

        // Create mentoring sessions
        $sessionActive = MentoringSession::create([
            'thesis_id' => $thesisActive->id,
            'dosen_id' => $dosen->id,
            'topic' => 'Topik Bimbingan Aktif',
            'scheduled_at' => now()->addDays(1),
            'type' => 'offline',
            'status' => 'completed',
        ]);

        $sessionHistory = MentoringSession::create([
            'thesis_id' => $thesisGraduated->id,
            'dosen_id' => $dosen->id,
            'topic' => 'Topik Bimbingan Lulus',
            'scheduled_at' => now()->subDays(10),
            'type' => 'offline',
            'status' => 'completed',
        ]);

        // 1. Check Default/Active Tab
        $response = $this->actingAs($dosen)
            ->get(route('mentoring-sessions.index', ['tab' => 'active']));

        $response->assertStatus(200);
        $response->assertSee('Topik Bimbingan Aktif');
        $response->assertDontSee('Topik Bimbingan Lulus');
        $response->assertDontSee('Lulus');

        // 2. Check History Tab
        $response = $this->actingAs($dosen)
            ->get(route('mentoring-sessions.index', ['tab' => 'history']));

        $response->assertStatus(200);
        $response->assertSee('Topik Bimbingan Lulus');
        $response->assertDontSee('Topik Bimbingan Aktif');
        $response->assertSee('Lulus');
    }
}
