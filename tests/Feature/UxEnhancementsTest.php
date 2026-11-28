<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\MentoringSession;
use App\Models\Thesis;
use App\Models\User;
use App\Notifications\MentoringScheduledByDosenNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UxEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_quick_search_enforces_strict_role_scoping(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $dosenA = User::factory()->create(['role' => 'dosen', 'is_active' => true, 'name' => 'Dr. Alpha']);
        $dosenB = User::factory()->create(['role' => 'dosen', 'is_active' => true, 'name' => 'Dr. Beta']);
        $studentA = User::factory()->create(['role' => 'mahasiswa', 'is_active' => true, 'name' => 'Raka Pratama', 'identifier' => 'D1A220001']);
        $studentB = User::factory()->create(['role' => 'mahasiswa', 'is_active' => true, 'name' => 'Raka Rahasia', 'identifier' => 'D1A220002']);

        Thesis::create([
            'student_id' => $studentA->id,
            'pembimbing1_id' => $dosenA->id,
            'title' => 'Sistem Deteksi Raka Alpha',
            'status' => 'active',
        ]);

        Thesis::create([
            'student_id' => $studentB->id,
            'pembimbing1_id' => $dosenB->id,
            'title' => 'Sistem Deteksi Raka Beta',
            'status' => 'active',
        ]);

        Faq::create([
            'category' => 'Bimbingan',
            'question' => 'Bagaimana cara mengajukan jadwal Raka?',
            'answer' => 'Buka menu Jadwal Bimbingan.',
            'is_active' => true,
            'order' => 1,
        ]);

        // Guest cannot access quick search
        $this->get(route('quick-search', ['q' => 'Raka']))->assertRedirect(route('login'));

        // Admin sees both theses, users, and FAQ
        $adminRes = $this->actingAs($admin)
            ->getJson(route('quick-search', ['q' => 'Raka']))
            ->assertOk();
        $adminTitles = collect($adminRes->json('results'))->pluck('title')->implode(' | ');
        $this->assertStringContainsString('Raka Pratama', $adminTitles);
        $this->assertStringContainsString('Raka Rahasia', $adminTitles);

        // Dosen A strictly sees only their own advised student (Raka Pratama), NOT Raka Rahasia
        $dosenRes = $this->actingAs($dosenA)
            ->getJson(route('quick-search', ['q' => 'Raka']))
            ->assertOk();
        $dosenTitles = collect($dosenRes->json('results'))->pluck('title')->implode(' | ');
        $this->assertStringContainsString('Raka Pratama', $dosenTitles);
        $this->assertStringNotContainsString('Raka Rahasia', $dosenTitles);

        // Mahasiswa sees FAQ only, NOT other students' theses or user directory
        $studentRes = $this->actingAs($studentA)
            ->getJson(route('quick-search', ['q' => 'Raka']))
            ->assertOk();
        $studentCategories = collect($studentRes->json('results'))->pluck('category')->unique()->all();
        $this->assertContains('Pusat Bantuan (FAQ)', $studentCategories);
        $this->assertNotContains('Pengguna', $studentCategories);
        $this->assertNotContains('Skripsi & Mahasiswa', $studentCategories);
    }

    public function test_student_notifications_json_includes_30_min_arrival_slots_and_synced_status(): void
    {
        $dosen = User::factory()->create(['role' => 'dosen', 'is_active' => true, 'name' => 'Dr. Pembimbing']);
        $student = User::factory()->create(['role' => 'mahasiswa', 'is_active' => true]);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'pembimbing1_id' => $dosen->id,
            'title' => 'Pengujian Notifikasi Jam Kedatangan',
            'status' => 'active',
        ]);

        $session = MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => $dosen->id,
            'topic' => 'Review Bab 3',
            'scheduled_at' => now()->addDay()->setTime(9, 0, 0),
            'scheduled_end_at' => now()->addDay()->setTime(11, 0, 0),
            'status' => 'approved',
            'student_attendance_status' => 'pending',
        ]);

        $student->notify(new MentoringScheduledByDosenNotification($session));

        $res = $this->actingAs($student)
            ->getJson(route('notifications.index'))
            ->assertOk();

        $first = $res->json('notifications.0');
        $this->assertNotNull($first['session_meta'] ?? null);
        $this->assertTrue($first['session_meta']['has_time_range']);
        $this->assertSame('09:00', $first['session_meta']['start_time']);
        $this->assertSame('11:00', $first['session_meta']['end_time']);
        $this->assertSame(['09:00', '09:30', '10:00', '10:30', '11:00'], $first['session_meta']['slots']);
        $this->assertSame('pending', $first['session_meta']['attendance_status']);

        // Confirm attendance with chosen arrival_time via AJAX (same endpoint used by notification dropdown)
        $this->actingAs($student)
            ->postJson(route('mentoring-sessions.confirm-attendance', $session->id), [
                'status' => 'attending',
                'arrival_time' => '10:00',
            ])
            ->assertOk()
            ->assertJson(['success' => true, 'status' => 'attending']);

        // Subsequent notification fetch reflects confirmed status & arrival time
        $after = $this->actingAs($student)
            ->getJson(route('notifications.index'))
            ->assertOk();

        $this->assertSame('attending', $after->json('notifications.0.session_meta.attendance_status'));
        $this->assertSame('10:00', $after->json('notifications.0.session_meta.arrival_time'));
    }

    public function test_command_palette_renders_on_authenticated_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Buka Pencarian Cepat (Ctrl+K)')
            ->assertSee('sibima-open-search')
            ->assertSee('Pencarian Cepat SIBIMA')
            ->assertDontSee('Cari cepat...');
    }
}
