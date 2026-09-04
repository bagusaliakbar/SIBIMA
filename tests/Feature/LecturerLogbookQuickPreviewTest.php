<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Thesis;
use App\Models\MentoringSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LecturerLogbookQuickPreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_supervisor_can_access_quick_preview()
    {
        $dosen = User::factory()->create(['role' => 'dosen', 'name' => 'Dr. Pembimbing Utama']);
        $otherDosen = User::factory()->create(['role' => 'dosen', 'name' => 'Dr. Dosen Lain']);
        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Ahmad Bimbingan', 'identifier' => '2106001']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Implementasi Machine Learning untuk Deteksi Polusi',
            'status' => 'active',
            'pembimbing1_id' => $dosen->id,
            'pembimbing2_id' => $otherDosen->id,
        ]);

        for ($i = 1; $i <= 3; $i++) {
            MentoringSession::create([
                'thesis_id' => $thesis->id,
                'dosen_id' => $dosen->id,
                'topic' => "Pembahasan Bab {$i}",
                'notes' => "Mahasiswa memaparkan progres bab {$i}",
                'feedback' => "Perbaiki tinjauan pustaka dan format sitasi bab {$i}",
                'status' => 'completed',
                'is_absent' => false,
                'scheduled_at' => now()->subDays(4 - $i),
            ]);
        }

        // 1. Authorized Dosen Pembimbing
        $response = $this->actingAs($dosen)->getJson(route('theses.logbooks.quick-preview', $thesis->id));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'thesis_id',
            'student_name',
            'student_identifier',
            'thesis_title',
            'total_completed',
            'full_logbook_url',
            'sessions' => [
                '*' => [
                    'id',
                    'session_number',
                    'topic',
                    'scheduled_at',
                    'time_ago',
                    'feedback',
                    'notes',
                    'dosen_name',
                ]
            ]
        ]);

        $response->assertJson([
            'student_name' => 'Ahmad Bimbingan',
            'total_completed' => 3,
            'sessions' => [
                [
                    'session_number' => 3,
                    'topic' => 'Pembahasan Bab 3',
                    'feedback' => 'Perbaiki tinjauan pustaka dan format sitasi bab 3',
                ]
            ]
        ]);

        // 2. Unauthorized Dosen (not a supervisor)
        $unrelatedDosen = User::factory()->create(['role' => 'dosen', 'name' => 'Dr. Asing']);
        $unauthResponse = $this->actingAs($unrelatedDosen)->getJson(route('theses.logbooks.quick-preview', $thesis->id));
        $unauthResponse->assertStatus(403);
    }
}
