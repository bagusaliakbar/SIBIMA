<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ThesisRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RepositoryExportPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_and_kaprodi_can_see_export_buttons_and_download_catalog(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $kaprodi = User::factory()->create(['role' => 'kaprodi']);

        ThesisRepository::create([
            'title' => 'Sistem Informasi Manajemen Skripsi Berbasis Web',
            'name' => 'Budi Santoso',
            'identifier' => 'D1A200001',
            'year' => 2024,
            'pembimbing1' => 'Dr. Hendra',
            'pembimbing2' => 'Ir. Maya',
            'abstract' => 'Abstrak skripsi sistem informasi.',
        ]);

        // Admin checks
        $response = $this->actingAs($admin)->get(route('repositories.index'));
        $response->assertStatus(200);
        $response->assertSee('Excel');
        $response->assertSee('PDF');

        $this->actingAs($admin)->get(route('repositories.export-excel'))->assertStatus(200);
        $this->actingAs($admin)->get(route('repositories.export-pdf'))->assertStatus(200);

        // Kaprodi checks
        $responseKaprodi = $this->actingAs($kaprodi)->get(route('repositories.index'));
        $responseKaprodi->assertStatus(200);
        $responseKaprodi->assertSee('Excel');
        $responseKaprodi->assertSee('PDF');

        $this->actingAs($kaprodi)->get(route('repositories.export-excel'))->assertStatus(200);
        $this->actingAs($kaprodi)->get(route('repositories.export-pdf'))->assertStatus(200);
    }

    public function test_dosen_and_student_cannot_see_export_buttons_and_are_forbidden(): void
    {
        $dosen = User::factory()->create(['role' => 'dosen']);
        $student = User::factory()->create(['role' => 'mahasiswa']);

        ThesisRepository::create([
            'title' => 'Sistem Informasi Manajemen Skripsi Berbasis Web',
            'name' => 'Budi Santoso',
            'identifier' => 'D1A200001',
            'year' => 2024,
            'pembimbing1' => 'Dr. Hendra',
            'pembimbing2' => 'Ir. Maya',
            'abstract' => 'Abstrak skripsi sistem informasi.',
        ]);

        // Dosen checks
        $responseDosen = $this->actingAs($dosen)->get(route('repositories.index'));
        $responseDosen->assertStatus(200);
        $responseDosen->assertDontSee('title="Ekspor Katalog ke Excel Sesuai Filter"', false);
        $responseDosen->assertDontSee('title="Ekspor Katalog ke PDF Sesuai Filter"', false);

        $this->actingAs($dosen)->get(route('repositories.export-excel'))->assertStatus(403);
        $this->actingAs($dosen)->get(route('repositories.export-pdf'))->assertStatus(403);

        // Student checks
        $responseStudent = $this->actingAs($student)->get(route('repositories.index'));
        $responseStudent->assertStatus(200);
        $responseStudent->assertDontSee('title="Ekspor Katalog ke Excel Sesuai Filter"', false);
        $responseStudent->assertDontSee('title="Ekspor Katalog ke PDF Sesuai Filter"', false);

        $this->actingAs($student)->get(route('repositories.export-excel'))->assertStatus(403);
        $this->actingAs($student)->get(route('repositories.export-pdf'))->assertStatus(403);
    }
}
