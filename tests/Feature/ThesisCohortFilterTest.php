<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Thesis;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThesisCohortFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_dosen_can_filter_theses_by_new_and_old_cohort()
    {
        $dosen = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Pembimbing']);
        
        $newStudent = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'Mahasiswa Baru 2023',
            'identifier' => 'MHS2023',
            'entry_year' => 2023,
        ]);

        $oldStudent = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'Mahasiswa Senior 2019',
            'identifier' => 'MHS2019',
            'entry_year' => 2019,
        ]);

        $thesisNew = Thesis::create([
            'student_id' => $newStudent->id,
            'title' => 'Skripsi Mahasiswa Baru 2023',
            'pembimbing1_id' => $dosen->id,
            'status' => 'active',
        ]);

        $thesisOld = Thesis::create([
            'student_id' => $oldStudent->id,
            'title' => 'Skripsi Mahasiswa Senior 2019',
            'pembimbing1_id' => $dosen->id,
            'status' => 'active',
        ]);

        // 1. Dosen views All (Default)
        $responseAll = $this->actingAs($dosen)
            ->get(route('theses.index', ['status' => 'active', 'cohort_filter' => 'all']));
        $responseAll->assertStatus(200);
        $this->assertCount(2, $responseAll->viewData('theses'));

        // 2. Dosen filters New Cohort
        $responseNew = $this->actingAs($dosen)
            ->get(route('theses.index', ['status' => 'active', 'cohort_filter' => 'new']));
        $responseNew->assertStatus(200);
        $thesesNewData = $responseNew->viewData('theses');
        $this->assertCount(1, $thesesNewData);
        $this->assertEquals('Mahasiswa Baru 2023', $thesesNewData->first()->student->name);

        // 3. Dosen filters Old Cohort
        $responseOld = $this->actingAs($dosen)
            ->get(route('theses.index', ['status' => 'active', 'cohort_filter' => 'old']));
        $responseOld->assertStatus(200);
        $thesesOldData = $responseOld->viewData('theses');
        $this->assertCount(1, $thesesOldData);
        $this->assertEquals('Mahasiswa Senior 2019', $thesesOldData->first()->student->name);
    }

    public function test_admin_can_filter_by_specific_entry_year()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $dosen = User::factory()->create(['role' => 'dosen']);

        $student2022 = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'Budi Angkatan 2022',
            'entry_year' => 2022,
        ]);

        $student2019 = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'Siti Angkatan 2019',
            'entry_year' => 2019,
        ]);

        Thesis::create([
            'student_id' => $student2022->id,
            'title' => 'Skripsi Angkatan 2022',
            'pembimbing1_id' => $dosen->id,
            'status' => 'active',
        ]);

        Thesis::create([
            'student_id' => $student2019->id,
            'title' => 'Skripsi Angkatan 2019',
            'pembimbing1_id' => $dosen->id,
            'status' => 'active',
        ]);

        // Filter specific year 2022
        $response = $this->actingAs($admin)
            ->get(route('theses.index', ['status' => 'all', 'entry_year' => 2022]));
        
        $response->assertStatus(200);
        $thesesData = $response->viewData('theses');
        $this->assertCount(1, $thesesData);
        $this->assertEquals('Budi Angkatan 2022', $thesesData->first()->student->name);
    }

    public function test_export_pdf_works_with_cohort_filter()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $dosen = User::factory()->create(['role' => 'dosen']);

        $student2022 = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'Budi Angkatan 2022',
            'entry_year' => 2022,
        ]);

        Thesis::create([
            'student_id' => $student2022->id,
            'title' => 'Skripsi Angkatan 2022',
            'pembimbing1_id' => $dosen->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('theses.export-pdf', ['status' => 'all', 'cohort_filter' => 'new']));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }

    public function test_export_excel_works_with_cohort_filter()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $dosen = User::factory()->create(['role' => 'dosen']);

        $student2022 = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'Budi Angkatan 2022',
            'entry_year' => 2022,
        ]);

        Thesis::create([
            'student_id' => $student2022->id,
            'title' => 'Skripsi Angkatan 2022',
            'pembimbing1_id' => $dosen->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('theses.export-excel', ['status' => 'all', 'cohort_filter' => 'new']));

        $response->assertStatus(200);
        $this->assertStringContainsString('spreadsheetml', $response->headers->get('content-type'));
    }
}
