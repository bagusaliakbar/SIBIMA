<?php

namespace Tests\Feature;

use App\Models\BugReport;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BugReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_student_can_submit_bug_report_with_screenshot(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $kaprodi = User::factory()->create(['role' => 'kaprodi', 'is_active' => true]);
        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Budi Mahasiswa']);

        $screenshot = UploadedFile::fake()->image('error_screenshot.png');

        $response = $this->actingAs($student)->post(route('bug-reports.store'), [
            'title' => 'Tombol Cetak Kartu Error',
            'category' => 'functionality',
            'severity' => 'high',
            'page_url' => 'http://localhost/theses/kanban',
            'description' => 'Saat klik tombol cetak, halaman blank putih dan muncul pesan error 500.',
            'steps_to_reproduce' => '1. Buka halaman kanban. 2. Klik tombol cetak.',
            'device_info' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) [1920x1080]',
            'attachment' => $screenshot,
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('bug_reports', [
            'user_id' => $student->id,
            'title' => 'Tombol Cetak Kartu Error',
            'category' => 'functionality',
            'severity' => 'high',
            'status' => 'open',
        ]);

        $report = BugReport::where('title', 'Tombol Cetak Kartu Error')->first();
        $this->assertNotNull($report);
        $this->assertStringStartsWith('BUG-', $report->ticket_number);
        $this->assertNotNull($report->attachment_path);
        Storage::disk('public')->assertExists($report->attachment_path);

        // Verify notifications sent to admin & kaprodi
        Notification::assertSentTo(
            [$admin, $kaprodi],
            GeneralNotification::class
        );
    }

    public function test_lecturer_can_submit_bug_report_via_ajax(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $lecturer = User::factory()->create(['role' => 'dosen', 'name' => 'Dr. Bambang']);

        $response = $this->actingAs($lecturer)->postJson(route('bug-reports.store'), [
            'title' => 'Tampilan Rekap Nilai Bergeser',
            'category' => 'ui_ux',
            'severity' => 'medium',
            'page_url' => 'http://localhost/monitoring/defense-scores',
            'description' => 'Tabel bergeser ke kiri di layar resolusi 1366x768.',
            'device_info' => 'Chrome [1366x768]',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('bug_reports', [
            'user_id' => $lecturer->id,
            'title' => 'Tampilan Rekap Nilai Bergeser',
            'category' => 'ui_ux',
            'severity' => 'medium',
        ]);
    }

    public function test_authenticated_user_can_fetch_their_own_reports(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $otherUser = User::factory()->create(['role' => 'mahasiswa']);

        BugReport::create([
            'ticket_number' => 'BUG-20260917-0001',
            'user_id' => $student->id,
            'title' => 'Bug Milik Mahasiswa Ini',
            'category' => 'functionality',
            'severity' => 'low',
            'status' => 'open',
            'description' => 'Deskripsi bug...',
        ]);

        BugReport::create([
            'ticket_number' => 'BUG-20260917-0002',
            'user_id' => $otherUser->id,
            'title' => 'Bug Milik Mahasiswa Lain',
            'category' => 'ui_ux',
            'severity' => 'low',
            'status' => 'open',
            'description' => 'Deskripsi bug...',
        ]);

        $response = $this->actingAs($student)->getJson(route('bug-reports.my-reports'));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'reports')
            ->assertJsonFragment(['title' => 'Bug Milik Mahasiswa Ini'])
            ->assertJsonMissing(['title' => 'Bug Milik Mahasiswa Lain']);
    }

    public function test_admin_and_kaprodi_can_access_management_panel(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $kaprodi = User::factory()->create(['role' => 'kaprodi']);

        $student = User::factory()->create(['role' => 'mahasiswa']);
        BugReport::create([
            'ticket_number' => 'BUG-20260917-0003',
            'user_id' => $student->id,
            'title' => 'Sistem crash saat upload KRS',
            'category' => 'functionality',
            'severity' => 'critical',
            'status' => 'open',
            'description' => 'Deskripsi...',
        ]);

        // Admin can view
        $adminResponse = $this->actingAs($admin)->get(route('admin.bug-reports.index'));
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('Sistem crash saat upload KRS');

        // Kaprodi can view
        $kaprodiResponse = $this->actingAs($kaprodi)->get(route('admin.bug-reports.index'));
        $kaprodiResponse->assertStatus(200);
        $kaprodiResponse->assertSee('Sistem crash saat upload KRS');
    }

    public function test_student_and_lecturer_are_forbidden_from_admin_management_panel(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $lecturer = User::factory()->create(['role' => 'dosen']);

        $this->actingAs($student)->get(route('admin.bug-reports.index'))
            ->assertStatus(403);

        $this->actingAs($lecturer)->get(route('admin.bug-reports.index'))
            ->assertStatus(403);
    }

    public function test_admin_can_update_status_and_add_notes_and_reporter_is_notified(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin Utama']);
        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Budi']);

        $bug = BugReport::create([
            'ticket_number' => 'BUG-20260917-0004',
            'user_id' => $student->id,
            'title' => 'Gagal verifikasi berkas',
            'category' => 'functionality',
            'severity' => 'high',
            'status' => 'open',
            'description' => 'Berkas tidak tervalidasi...',
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.bug-reports.update-status', $bug->id), [
            'status' => 'resolved',
            'admin_notes' => 'Telah diperbaiki pada rilis patch terbaru.',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('bug_reports', [
            'id' => $bug->id,
            'status' => 'resolved',
            'admin_notes' => 'Telah diperbaiki pada rilis patch terbaru.',
            'resolved_by' => $admin->id,
        ]);

        $this->assertNotNull($bug->fresh()->resolved_at);

        // Reporter receives notification
        Notification::assertSentTo(
            $student,
            GeneralNotification::class
        );
    }

    public function test_admin_can_delete_bug_report(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'mahasiswa']);

        $file = UploadedFile::fake()->image('temp.png');
        $path = $file->store('bug_reports', 'public');

        $bug = BugReport::create([
            'ticket_number' => 'BUG-20260917-0005',
            'user_id' => $student->id,
            'title' => 'Spam bug report',
            'category' => 'other',
            'severity' => 'low',
            'status' => 'rejected',
            'description' => 'Testing spam',
            'attachment_path' => $path,
        ]);

        Storage::disk('public')->assertExists($path);

        $response = $this->actingAs($admin)->delete(route('admin.bug-reports.destroy', $bug->id));

        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('bug_reports', ['id' => $bug->id]);
        Storage::disk('public')->assertMissing($path);
    }
}
