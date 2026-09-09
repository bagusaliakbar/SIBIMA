<?php

namespace Tests\Feature;

use App\Models\ThesisRepository;
use App\Models\User;
use App\Services\UnsubRepositorySyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UnsubRepositorySyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_strictly_filters_only_fasilkom_documents(): void
    {
        $service = new UnsubRepositorySyncService();

        $fasilkomDoc1 = [
            'fakultas_nama' => 'Fakultas Ilmu Komputer',
            'prodi_nama' => 'Sistem Informasi',
            'judul' => 'Sistem Informasi Manajemen A',
            'penulis' => 'Ahmad',
        ];

        $fasilkomDoc2 = [
            'fakultas_nama' => 'FASILKOM',
            'prodi_nama' => 'Informatika',
            'judul' => 'Aplikasi Mobile B',
            'penulis' => 'Budi',
        ];

        $hukumDoc = [
            'fakultas_nama' => 'Fakultas Hukum',
            'prodi_nama' => 'Ilmu Hukum',
            'judul' => 'Analisis Yuridis',
            'penulis' => 'Candra',
        ];

        $fiaDoc = [
            'fakultas_nama' => 'Fakultas Ilmu Administrasi',
            'prodi_nama' => 'Administrasi Publik',
            'judul' => 'Kebijakan Publik',
            'penulis' => 'Dewi',
        ];

        $fkipDoc = [
            'fakultas_nama' => 'Fakultas Keguruan dan Ilmu Pendidikan',
            'prodi_nama' => 'Pendidikan Matematika',
            'judul' => 'Model Pembelajaran',
            'penulis' => 'Eko',
        ];

        $this->assertTrue($service->isFasilkomDocument($fasilkomDoc1));
        $this->assertTrue($service->isFasilkomDocument($fasilkomDoc2));
        $this->assertFalse($service->isFasilkomDocument($hukumDoc));
        $this->assertFalse($service->isFasilkomDocument($fiaDoc));
        $this->assertFalse($service->isFasilkomDocument($fkipDoc));

        $filtered = $service->filterFasilkom([$fasilkomDoc1, $fasilkomDoc2, $hukumDoc, $fiaDoc, $fkipDoc]);
        $this->assertCount(2, $filtered);
        $this->assertEquals('Sistem Informasi Manajemen A', $filtered[0]['judul']);
        $this->assertEquals('Aplikasi Mobile B', $filtered[1]['judul']);
    }

    public function test_service_strictly_extracts_bab_1_and_bab_2_files(): void
    {
        $service = new UnsubRepositorySyncService();

        $files = [
            ['file_name' => 'Cover.pdf', 'file_path' => 'cover123'],
            ['file_name' => 'BAB I.pdf', 'file_path' => 'bab1_gdrive_id', 'file_size' => 1024],
            ['file_name' => 'BAB II.pdf', 'file_path' => 'bab2_gdrive_id', 'file_size' => 2048],
            ['file_name' => 'BAB III.pdf', 'file_path' => 'bab3_gdrive_id', 'file_size' => 3072],
            ['file_name' => 'BAB IV.pdf', 'file_path' => 'bab4_gdrive_id', 'file_size' => 4096],
            ['file_name' => 'BAB V.pdf', 'file_path' => 'bab5_gdrive_id', 'file_size' => 5120],
        ];

        $bab1 = $service->extractBab1File($files);
        $this->assertNotNull($bab1);
        $this->assertEquals('BAB I.pdf', $bab1['file_name']);
        $this->assertEquals('bab1_gdrive_id', $bab1['file_path']);

        $bab2 = $service->extractBab2File($files);
        $this->assertNotNull($bab2);
        $this->assertEquals('BAB II.pdf', $bab2['file_name']);
        $this->assertEquals('bab2_gdrive_id', $bab2['file_path']);

        // Test with different naming style e.g. Bab 1, Bab 2, BAB_2
        $filesAlt = [
            ['file_name' => 'Bab 1 Pendahuluan.pdf', 'file_path' => 'alt_bab1'],
            ['file_name' => 'Bab 2 Tinjauan Pustaka.pdf', 'file_path' => 'alt_bab2'],
        ];
        $bab1Alt = $service->extractBab1File($filesAlt);
        $this->assertNotNull($bab1Alt);
        $this->assertEquals('Bab 1 Pendahuluan.pdf', $bab1Alt['file_name']);

        $bab2Alt = $service->extractBab2File($filesAlt);
        $this->assertNotNull($bab2Alt);
        $this->assertEquals('Bab 2 Tinjauan Pustaka.pdf', $bab2Alt['file_name']);
    }

    public function test_service_creates_new_record_or_enriches_existing(): void
    {
        $service = new UnsubRepositorySyncService();

        // 1. Initial creation
        $doc = [
            'fakultas_nama' => 'Fakultas Ilmu Komputer',
            'prodi_nama' => 'Sistem Informasi',
            'judul' => 'SISTEM INFORMASI AKADEMIK BERBASIS CLOUD',
            'penulis' => 'Rian Pratama',
            'npm' => 'D1A180999',
            'tahun' => '2023',
            'abstrak' => 'Teks abstrak lengkap penelitian sistem informasi akademik.',
            'dosen_pembimbing' => '1. Dr. Hendra, M.Kom',
            'dosen_pembimbing_2' => '2. Ir. Maya, M.T',
            'files' => [
                ['file_name' => 'BAB I.pdf', 'file_path' => 'gdrive_bab1_123'],
                ['file_name' => 'BAB II.pdf', 'file_path' => 'gdrive_bab2_123'],
            ]
        ];

        $result = $service->syncDocument($doc, false); // false = store proxy stream URL
        $this->assertEquals('created', $result['status']);
        $this->assertTrue($result['has_bab1']);
        $this->assertTrue($result['has_bab2']);
        $this->assertDatabaseHas('thesis_repositories', [
            'identifier' => 'D1A180999',
            'title' => 'SISTEM INFORMASI AKADEMIK BERBASIS CLOUD',
            'abstract' => 'Teks abstrak lengkap penelitian sistem informasi akademik.',
            'pembimbing1' => 'Dr. Hendra, M.Kom',
            'pembimbing2' => 'Ir. Maya, M.T',
        ]);

        // 2. Existing record from portal (without abstract and file_path)
        $existing = ThesisRepository::create([
            'identifier' => 'D1A180888',
            'name' => 'Siti Nurhaliza',
            'year' => 2022,
            'title' => 'PENGEMBANGAN SISTEM REPOSITORI DIGITAL',
            'abstract' => null,
            'file_path' => null,
            'file_path_bab2' => null,
            'pembimbing1' => null,
            'pembimbing2' => null,
        ]);

        $enrichDoc = [
            'fakultas_nama' => 'Fakultas Ilmu Komputer',
            'prodi_nama' => 'Sistem Informasi',
            'judul' => 'PENGEMBANGAN SISTEM REPOSITORI DIGITAL',
            'penulis' => 'Siti Nurhaliza',
            'npm' => 'D1A180888',
            'tahun' => 2022,
            'abstrak' => 'Abstrak baru yang diperkaya dari repositori universitas.',
            'dosen_pembimbing' => 'Prof. Dr. Ir. Gunawan',
            'dosen_pembimbing_2' => 'Dewi Lestari, M.Kom',
            'files' => [
                ['file_name' => 'BAB I.pdf', 'file_path' => 'enrich_gdrive_bab1'],
                ['file_name' => 'BAB II.pdf', 'file_path' => 'enrich_gdrive_bab2'],
            ]
        ];

        $enrichResult = $service->syncDocument($enrichDoc, false);
        $this->assertEquals('enriched', $enrichResult['status']);
        $this->assertTrue($enrichResult['has_bab1']);
        $this->assertTrue($enrichResult['has_bab2']);

        $existing->refresh();
        $this->assertEquals('Abstrak baru yang diperkaya dari repositori universitas.', $existing->abstract);
        $this->assertStringContainsString('enrich_gdrive_bab1', $existing->file_path);
        $this->assertStringContainsString('enrich_gdrive_bab2', $existing->file_path_bab2);
        $this->assertEquals('Prof. Dr. Ir. Gunawan', $existing->pembimbing1);

        // Verify total count in DB is exactly 2 (no duplicates)
        $this->assertEquals(2, ThesisRepository::count());
    }

    public function test_stream_bab1_handles_remote_and_local_files(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);

        // Remote URL repository record
        $repoRemote = ThesisRepository::create([
            'title' => 'Skripsi Remote',
            'name' => 'Mahasiswa 1',
            'year' => 2023,
            'file_path' => 'https://repository.unsub.ac.id/api/gdrive-proxy/test_remote_id',
        ]);

        $responseRemote = $this->actingAs($student)->get(route('repositories.bab1', $repoRemote));
        $responseRemote->assertRedirect('https://repository.unsub.ac.id/api/gdrive-proxy/test_remote_id');

        // Local file repository record
        Storage::fake('public');
        Storage::disk('public')->put('theses_bab1/test_local_BAB1.pdf', '%PDF-1.4 dummy content');

        $repoLocal = ThesisRepository::create([
            'title' => 'Skripsi Local',
            'name' => 'Mahasiswa 2',
            'year' => 2023,
            'file_path' => 'theses_bab1/test_local_BAB1.pdf',
        ]);

        $responseLocal = $this->actingAs($student)->get(route('repositories.bab1', $repoLocal));
        $responseLocal->assertStatus(200);
        $responseLocal->assertHeader('Content-Type', 'application/pdf');

        // Empty file_path record
        $repoEmpty = ThesisRepository::create([
            'title' => 'Skripsi No File',
            'name' => 'Mahasiswa 3',
            'year' => 2023,
            'file_path' => null,
        ]);

        $responseEmpty = $this->actingAs($student)->get(route('repositories.bab1', $repoEmpty));
        $responseEmpty->assertStatus(404);
    }

    public function test_stream_bab2_handles_remote_and_local_files(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);

        // Remote URL repository record
        $repoRemote = ThesisRepository::create([
            'title' => 'Skripsi Remote Bab 2',
            'name' => 'Mahasiswa Bab 2',
            'year' => 2023,
            'file_path_bab2' => 'https://repository.unsub.ac.id/api/gdrive-proxy/test_remote_id_bab2',
        ]);

        $responseRemote = $this->actingAs($student)->get(route('repositories.bab2', $repoRemote));
        $responseRemote->assertRedirect('https://repository.unsub.ac.id/api/gdrive-proxy/test_remote_id_bab2');

        // Local file repository record
        Storage::fake('public');
        Storage::disk('public')->put('theses_bab2/test_local_BAB2.pdf', '%PDF-1.4 dummy content');

        $repoLocal = ThesisRepository::create([
            'title' => 'Skripsi Local Bab 2',
            'name' => 'Mahasiswa Local Bab 2',
            'year' => 2023,
            'file_path_bab2' => 'theses_bab2/test_local_BAB2.pdf',
        ]);

        $responseLocal = $this->actingAs($student)->get(route('repositories.bab2', $repoLocal));
        $responseLocal->assertStatus(200);
        $responseLocal->assertHeader('Content-Type', 'application/pdf');

        // Empty file_path_bab2 record
        $repoEmpty = ThesisRepository::create([
            'title' => 'Skripsi No File Bab 2',
            'name' => 'Mahasiswa No File',
            'year' => 2023,
            'file_path_bab2' => null,
        ]);

        $responseEmpty = $this->actingAs($student)->get(route('repositories.bab2', $repoEmpty));
        $responseEmpty->assertStatus(404);
    }

    public function test_only_admin_and_kaprodi_can_access_sync_endpoints(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'mahasiswa']);

        // Student forbidden
        $this->actingAs($student)->get(route('repositories.unsub-info'))->assertStatus(403);
        $this->actingAs($student)->post(route('repositories.sync-unsub-chunk'))->assertStatus(403);

        // Admin authorized
        Http::fake([
            UnsubRepositorySyncService::API_URL => Http::response([
                [
                    'fakultas_nama' => 'Fakultas Ilmu Komputer',
                    'prodi_nama' => 'Sistem Informasi',
                    'judul' => 'Skripsi Fake',
                    'penulis' => 'Penulis Fake',
                ],
                [
                    'fakultas_nama' => 'Fakultas Hukum',
                    'prodi_nama' => 'Ilmu Hukum',
                    'judul' => 'Skripsi Hukum Fake',
                    'penulis' => 'Penulis Hukum',
                ]
            ], 200)
        ]);

        $resInfo = $this->actingAs($admin)->get(route('repositories.unsub-info'));
        $resInfo->assertStatus(200);
        $resInfo->assertJson([
            'success' => true,
            'total_all' => 2,
            'total_fasilkom' => 1,
            'ignored_count' => 1
        ]);
    }

    public function test_repository_index_renders_in_app_pdf_reader_modal(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        ThesisRepository::create([
            'title' => 'Sistem Informasi Skripsi PDF Reader',
            'name' => 'Mahasiswa Test',
            'identifier' => 'D1A200001',
            'year' => 2024,
            'file_path' => 'theses_bab1/test_bab1.pdf',
            'file_path_bab2' => 'theses_bab2/test_bab2.pdf',
        ]);

        $response = $this->actingAs($admin)->get(route('repositories.index'));

        $response->assertStatus(200);
        $response->assertSee('pdfReaderModalElement', false);
        $response->assertSee('openPdfReader', false);
        $response->assertSee('switchChapter', false);
        $response->assertSee('drawWatermark', false);
        $response->assertSee('pdf.min.js', false);
        $response->assertSee('BAB 3', false);
        $response->assertSee('BAB 4', false);
        $response->assertSee('BAB 5', false);
        $response->assertSee('BAB 6', false);
    }

    public function test_service_extracts_bab_3_bab_4_bab_5_and_bab_6_files(): void
    {
        $service = new UnsubRepositorySyncService();

        $files = [
            ['file_name' => 'BAB III.pdf', 'file_path' => 'bab3_id'],
            ['file_name' => 'BAB IV.pdf', 'file_path' => 'bab4_id'],
            ['file_name' => 'BAB V.pdf', 'file_path' => 'bab5_id'],
            ['file_name' => 'BAB VI.pdf', 'file_path' => 'bab6_id'],
        ];

        $bab3 = $service->extractBab3File($files);
        $this->assertNotNull($bab3);
        $this->assertEquals('BAB III.pdf', $bab3['file_name']);
        $this->assertEquals('bab3_id', $bab3['file_path']);

        $bab4 = $service->extractBab4File($files);
        $this->assertNotNull($bab4);
        $this->assertEquals('BAB IV.pdf', $bab4['file_name']);
        $this->assertEquals('bab4_id', $bab4['file_path']);

        $bab5 = $service->extractBab5File($files);
        $this->assertNotNull($bab5);
        $this->assertEquals('BAB V.pdf', $bab5['file_name']);
        $this->assertEquals('bab5_id', $bab5['file_path']);

        $bab6 = $service->extractBab6File($files);
        $this->assertNotNull($bab6);
        $this->assertEquals('BAB VI.pdf', $bab6['file_name']);
        $this->assertEquals('bab6_id', $bab6['file_path']);
    }

    public function test_repositories_bab3_bab4_bab5_bab6_streaming_routes(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);

        $repo = ThesisRepository::create([
            'title' => 'Skripsi Bab 3 4 5 6 Test',
            'name' => 'Mahasiswa Test Chapters',
            'identifier' => 'D1A200002',
            'year' => 2024,
            'file_path_bab3' => 'https://repository.unsub.ac.id/api/gdrive-proxy/fake_bab3',
            'file_path_bab4' => 'https://repository.unsub.ac.id/api/gdrive-proxy/fake_bab4',
            'file_path_bab5' => 'https://repository.unsub.ac.id/api/gdrive-proxy/fake_bab5',
            'file_path_bab6' => 'https://repository.unsub.ac.id/api/gdrive-proxy/fake_bab6',
        ]);

        $res3 = $this->actingAs($student)->get(route('repositories.bab3', $repo));
        $res3->assertRedirect('https://repository.unsub.ac.id/api/gdrive-proxy/fake_bab3');

        $res4 = $this->actingAs($student)->get(route('repositories.bab4', $repo));
        $res4->assertRedirect('https://repository.unsub.ac.id/api/gdrive-proxy/fake_bab4');

        $res5 = $this->actingAs($student)->get(route('repositories.bab5', $repo));
        $res5->assertRedirect('https://repository.unsub.ac.id/api/gdrive-proxy/fake_bab5');

        $res6 = $this->actingAs($student)->get(route('repositories.bab6', $repo));
        $res6->assertRedirect('https://repository.unsub.ac.id/api/gdrive-proxy/fake_bab6');
    }
}
