<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Thesis;
use App\Models\MentoringSession;
use App\Models\Announcement;
use App\Models\Message;
use App\Models\ActivityLog;
use App\Services\ChatService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ComprehensiveSystemTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Chat Routing, Allowed Users query, and message sending logic.
     */
    public function test_chat_routing_and_permissions()
    {
        // 1. Setup Roles & Relationship
        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Test']);
        $otherStudent = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Other Mahasiswa']);
        $p1 = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Pembimbing 1']);
        $p2 = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Pembimbing 2']);
        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin SIBIMA']);

        // Set up thesis relationship
        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Analisis Performa Sistem',
            'abstract' => 'Penelitian performa...',
            'pembimbing1_id' => $p1->id,
            'pembimbing2_id' => $p2->id,
            'status' => 'approved',
        ]);

        // 2. Test Allowed Users for Student
        $this->actingAs($student);
        $chatService = app(ChatService::class);
        $allowedForStudent = $chatService->getAllowedUsers();

        // Mahasiswa should see Pembimbing 1, Pembimbing 2, and Admin/Kaprodi, but NOT other student
        $allowedIds = $allowedForStudent->pluck('id')->toArray();
        $this->assertContains($p1->id, $allowedIds);
        $this->assertContains($p2->id, $allowedIds);
        $this->assertContains($admin->id, $allowedIds);
        $this->assertNotContains($otherStudent->id, $allowedIds);

        // 3. Test Routing
        $response = $this->get(route('chat.index'));
        $response->assertStatus(200);

        $response = $this->get(route('chat.show', $p1->id));
        $response->assertStatus(200);

        // Send a valid message
        $response = $this->post(route('chat.store', $p1->id), [
            'message' => 'Halo Bapak, mohon bimbingannya.',
        ]);
        $response->assertRedirect(route('chat.show', $p1->id));

        // Check if message is logged in messages table
        $this->assertDatabaseHas('messages', [
            'sender_id' => $student->id,
            'receiver_id' => $p1->id,
            'message' => 'Halo Bapak, mohon bimbingannya.',
        ]);

        // Check if activity is logged
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $student->id,
            'activity' => 'Kirim Pesan',
            'module' => 'Chat',
        ]);
    }

    /**
     * Test validation constraints on the chat message storage endpoint.
     */
    public function test_chat_message_validation()
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $p1 = User::factory()->create(['role' => 'dosen']);

        $this->actingAs($student);

        // Empty message validation
        $response = $this->post(route('chat.store', $p1->id), [
            'message' => '',
        ]);
        $response->assertSessionHasErrors('message');

        // Too long message validation
        $response = $this->post(route('chat.store', $p1->id), [
            'message' => str_repeat('A', 1001),
        ]);
        $response->assertSessionHasErrors('message');
    }

    /**
     * Test checking lecturer conflicts during seminar/defense planning.
     */
    public function test_dosen_availability_conflict_check()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $dosen = User::factory()->create(['role' => 'dosen']);

        $this->actingAs($admin);

        // Valid query check
        $response = $this->postJson(route('check-dosen-availability'), [
            'dosen_ids' => [$dosen->id],
            'date' => '2026-06-20',
            'start_time' => '09:00',
            'end_time' => '10:30',
            'schedule_type' => 'seminar',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['has_conflict', 'conflicts']);
        $this->assertFalse($response->json('has_conflict'));
    }

    /**
     * Test security and authorization constraints for secure private files.
     */
    public function test_secure_private_download_permissions()
    {
        Storage::fake('local');

        $studentA = User::factory()->create(['role' => 'mahasiswa']);
        $studentB = User::factory()->create(['role' => 'mahasiswa']);
        $admin = User::factory()->create(['role' => 'admin']);

        // Create a mock private file
        $path = 'session-documents/mock_session_doc_student_a.pdf';
        Storage::disk('local')->put($path, 'dummy content');

        // Setup Mentoring Session for studentA
        $thesis = Thesis::create([
            'student_id' => $studentA->id,
            'title' => 'Thesis A',
            'abstract' => 'Abstract A',
            'status' => 'approved',
        ]);

        MentoringSession::create([
            'thesis_id' => $thesis->id,
            'dosen_id' => User::factory()->create(['role' => 'dosen'])->id,
            'topic' => 'Bab 1',
            'notes' => 'Catatan',
            'scheduled_at' => now(),
            'type' => 'offline',
            'status' => 'approved',
            'document_path' => $path,
        ]);

        // Student A should be able to download
        $response = $this->actingAs($studentA)->get(route('download.private', ['path' => $path]));
        $response->assertStatus(200);

        // Student B should NOT be able to download (Unauthorized 403)
        $response = $this->actingAs($studentB)->get(route('download.private', ['path' => $path]));
        $response->assertStatus(403);

        // Admin should be able to download
        $response = $this->actingAs($admin)->get(route('download.private', ['path' => $path]));
        $response->assertStatus(200);
    }

    /**
     * Test creating system announcements and toggling their state.
     */
    public function test_announcement_toggle_and_crud()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // Test Store Announcement
        $response = $this->post(route('announcements.store'), [
            'title' => 'Pengumuman Penting Libur Akhir Semester',
            'content' => 'Hari raya libur mulai 25 Desember.',
            'type' => 'important',
            'is_active' => 'on',
        ]);
        $response->assertRedirect();

        $announcement = Announcement::first();
        $this->assertNotNull($announcement);
        $this->assertEquals('Pengumuman Penting Libur Akhir Semester', $announcement->title);
        $this->assertTrue((bool)$announcement->is_active);

        // Test Toggle Status Announcement
        $response = $this->post(route('announcements.toggle', $announcement->id));
        $response->assertRedirect();
        
        $announcement->refresh();
        $this->assertFalse((bool)$announcement->is_active);
    }

    /**
     * Test role-based protection for administrative and settings endpoints.
     */
    public function test_role_based_access_control()
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);

        // Student cannot access announcements settings route
        $response = $this->actingAs($student)->get(route('announcements.index'));
        $response->assertStatus(403);

        // Student cannot access system logs route
        $response = $this->actingAs($student)->get(route('admin.logs'));
        $response->assertStatus(403);

        // Student cannot edit waves settings
        $response = $this->actingAs($student)->get(route('waves.index'));
        $response->assertStatus(403);
    }
}
