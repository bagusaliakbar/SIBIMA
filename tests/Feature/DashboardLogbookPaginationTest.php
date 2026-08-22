<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Thesis;
use App\Models\MentoringSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardLogbookPaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_dashboard_logbook_pagination()
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $dosen = User::factory()->create(['role' => 'dosen']);
        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Skripsi Test Bimbingan',
            'status' => 'active',
            'pembimbing1_id' => $dosen->id,
        ]);

        // Create 6 completed mentoring sessions with notes (logbooks)
        for ($i = 1; $i <= 6; $i++) {
            MentoringSession::create([
                'thesis_id' => $thesis->id,
                'dosen_id' => $dosen->id,
                'topic' => "Topik Logbook ke-" . $i,
                'notes' => "Catatan Logbook ke-" . $i,
                'status' => 'completed',
                'scheduled_at' => now()->subDays($i),
            ]);
        }

        // 1. Visit first page
        $response = $this->actingAs($student)->get(route('dashboard'));
        $response->assertStatus(200);
        $this->assertCount(5, $response->viewData('recentLogbooks'));

        // 2. Visit second page
        $response2 = $this->actingAs($student)->get(route('dashboard') . '?logbook_page=2');
        $response2->assertStatus(200);
        $this->assertCount(1, $response2->viewData('recentLogbooks'));
    }

    public function test_lecturer_dashboard_logbook_pagination()
    {
        $dosen = User::factory()->create(['role' => 'dosen']);
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Skripsi Test Bimbingan',
            'status' => 'active',
            'pembimbing1_id' => $dosen->id,
        ]);

        // Create 6 completed mentoring sessions with notes (logbooks)
        for ($i = 1; $i <= 6; $i++) {
            MentoringSession::create([
                'thesis_id' => $thesis->id,
                'dosen_id' => $dosen->id,
                'topic' => "Topik Logbook ke-" . $i,
                'notes' => "Catatan Logbook ke-" . $i,
                'status' => 'completed',
                'scheduled_at' => now()->subDays($i),
            ]);
        }

        // 1. Visit first page
        $response = $this->actingAs($dosen)->get(route('dashboard'));
        $response->assertStatus(200);
        $this->assertCount(5, $response->viewData('recentLogbooks'));

        // 2. Visit second page
        $response2 = $this->actingAs($dosen)->get(route('dashboard') . '?logbook_page=2');
        $response2->assertStatus(200);
        $this->assertCount(1, $response2->viewData('recentLogbooks'));
    }

    public function test_admin_dashboard_logbook_pagination()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $dosen = User::factory()->create(['role' => 'dosen']);
        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Skripsi Test Bimbingan',
            'status' => 'active',
            'pembimbing1_id' => $dosen->id,
        ]);

        // Create 6 completed mentoring sessions with notes (logbooks)
        for ($i = 1; $i <= 6; $i++) {
            MentoringSession::create([
                'thesis_id' => $thesis->id,
                'dosen_id' => $dosen->id,
                'topic' => "Topik Logbook ke-" . $i,
                'notes' => "Catatan Logbook ke-" . $i,
                'status' => 'completed',
                'scheduled_at' => now()->subDays($i),
            ]);
        }

        // 1. Visit first page
        $response = $this->actingAs($admin)->get(route('dashboard'));
        $response->assertStatus(200);
        $this->assertCount(5, $response->viewData('recentLogbooks'));

        // 2. Visit second page
        $response2 = $this->actingAs($admin)->get(route('dashboard') . '?logbook_page=2');
        $response2->assertStatus(200);
        $this->assertCount(1, $response2->viewData('recentLogbooks'));
    }
}
