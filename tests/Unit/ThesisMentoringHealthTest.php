<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Thesis;
use App\Models\User;
use App\Models\MentoringSession;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ThesisMentoringHealthTest extends TestCase
{
    use RefreshDatabase;

    public function test_days_since_last_mentoring_is_positive_when_session_is_in_the_past()
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $dosen = User::factory()->create(['role' => 'dosen']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'pembimbing1_id' => $dosen->id,
            'title' => 'Sistem Informasi Skripsi Test',
            'status' => 'active',
        ]);

        // Create completed session 35 days ago
        MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => $dosen->id,
            'topic' => 'Bimbingan Bab 1',
            'status' => 'completed',
            'is_absent' => false,
            'scheduled_at' => now()->subDays(35),
        ]);

        $thesis->refresh();

        // Must be positive 35, not -35!
        $this->assertEquals(35, $thesis->days_since_last_mentoring);
        $this->assertEquals('critical', $thesis->mentoring_health_status);

        $badge = $thesis->mentoring_health_badge;
        $this->assertEquals('critical', $badge['status']);
        $this->assertEquals('rose', $badge['color']);
        $this->assertEquals('Macet (35h)', $badge['label']);
    }

    public function test_mentoring_health_warning_status_for_eight_to_fourteen_days()
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $dosen = User::factory()->create(['role' => 'dosen']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'pembimbing1_id' => $dosen->id,
            'title' => 'Sistem Informasi Skripsi Warning Test',
            'status' => 'active',
        ]);

        // Create completed session 10 days ago
        MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => $dosen->id,
            'topic' => 'Bimbingan Bab 2',
            'status' => 'completed',
            'is_absent' => false,
            'scheduled_at' => now()->subDays(10),
        ]);

        $thesis->refresh();

        $this->assertEquals(10, $thesis->days_since_last_mentoring);
        $this->assertEquals('warning', $thesis->mentoring_health_status);

        $badge = $thesis->mentoring_health_badge;
        $this->assertEquals('warning', $badge['status']);
        $this->assertEquals('amber', $badge['color']);
        $this->assertEquals('Pasif (10h)', $badge['label']);
    }

    public function test_mentoring_health_active_status_within_seven_days()
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $dosen = User::factory()->create(['role' => 'dosen']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'pembimbing1_id' => $dosen->id,
            'title' => 'Sistem Informasi Skripsi Active Test',
            'status' => 'active',
        ]);

        // Create completed session 3 days ago
        MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => $dosen->id,
            'topic' => 'Bimbingan Bab 3',
            'status' => 'completed',
            'is_absent' => false,
            'scheduled_at' => now()->subDays(3),
        ]);

        $thesis->refresh();

        $this->assertEquals(3, $thesis->days_since_last_mentoring);
        $this->assertEquals('active', $thesis->mentoring_health_status);

        $badge = $thesis->mentoring_health_badge;
        $this->assertEquals('active', $badge['status']);
        $this->assertEquals('emerald', $badge['color']);
        $this->assertEquals('Lancar (3h)', $badge['label']);
    }
}
