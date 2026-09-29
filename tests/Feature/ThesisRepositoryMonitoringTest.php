<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Thesis;
use App\Models\JournalBookmark;
use App\Models\JournalBookmarkFolder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThesisRepositoryMonitoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_mahasiswa_and_dosen_cannot_access_monitoring_page()
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
        $dosen = User::factory()->create(['role' => 'dosen']);

        $responseMhs = $this->actingAs($mahasiswa)->get(route('repositories.monitoring'));
        $responseMhs->assertStatus(403);

        $responseDosen = $this->actingAs($dosen)->get(route('repositories.monitoring'));
        $responseDosen->assertStatus(403);
    }

    public function test_admin_and_kaprodi_can_access_monitoring_page()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $kaprodi = User::factory()->create(['role' => 'kaprodi']);

        // Create student with bookmarks
        $student = User::factory()->create([
            'name' => 'Budi Santoso',
            'role' => 'mahasiswa',
            'identifier' => 'D1A200001',
        ]);

        $folder = JournalBookmarkFolder::create([
            'user_id' => $student->id,
            'name' => 'Bab 2 Landasan Teori',
            'color' => '#10b981',
        ]);

        JournalBookmark::create([
            'user_id' => $student->id,
            'folder_id' => $folder->id,
            'journal_identifier' => 'jurnal-123',
            'title' => 'Penerapan Metode SAW Pada Sistem Pendukung Keputusan',
            'authors' => ['Bagus Ali', 'Akbar'],
            'authors_string' => 'Bagus Ali, Akbar',
            'year' => 2024,
            'venue' => 'Jurnal FASILKOM UNSUB',
            'source' => 'fasilkom',
            'notes' => 'Sangat relevan untuk kajian metode SAW pada bab 2',
        ]);

        $responseAdmin = $this->actingAs($admin)->get(route('repositories.monitoring'));
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertSee('Monitoring Koleksi Pustaka Civitas');
        $responseAdmin->assertSee('Budi Santoso');
        $responseAdmin->assertSee('D1A200001');

        $responseKaprodi = $this->actingAs($kaprodi)->get(route('repositories.monitoring'));
        $responseKaprodi->assertStatus(200);
        $responseKaprodi->assertSee('Budi Santoso');
    }

    public function test_admin_can_fetch_user_collection_json()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create([
            'name' => 'Siti Rahma',
            'role' => 'mahasiswa',
            'identifier' => 'D1A200002',
        ]);

        $folder = JournalBookmarkFolder::create([
            'user_id' => $student->id,
            'name' => 'Bab 1 Pendahuluan',
            'color' => '#f59e0b',
        ]);

        JournalBookmark::create([
            'user_id' => $student->id,
            'folder_id' => $folder->id,
            'journal_identifier' => 'jurnal-456',
            'title' => 'Analisis Tren AI Dalam Dunia Akademik',
            'authors' => ['John Doe'],
            'authors_string' => 'John Doe',
            'year' => 2025,
            'venue' => 'IEEE Transactions',
            'source' => 'openalex',
            'notes' => 'Dasar latar belakang rumusan masalah',
        ]);

        $response = $this->actingAs($admin)->get(route('repositories.monitoring.user-collection', $student));
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'user' => [
                'name' => 'Siti Rahma',
                'identifier' => 'D1A200002',
                'role' => 'mahasiswa',
            ],
            'total_bookmarks' => 1,
        ]);
        $response->assertJsonFragment([
            'title' => 'Analisis Tren AI Dalam Dunia Akademik',
            'source' => 'openalex',
        ]);
    }
}
