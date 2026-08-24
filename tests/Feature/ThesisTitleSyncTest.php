<?php

namespace Tests\Feature;

use App\Models\Thesis;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThesisTitleSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_updating_thesis_title_synchronizes_title_and_final_title()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $dosen1 = User::factory()->create(['role' => 'dosen']);
        $dosen2 = User::factory()->create(['role' => 'dosen']);

        $thesis = Thesis::create([
            'student_id' => $student->id,
            'title' => 'Judul Awal Skripsi Mahasiswa',
            'abstract' => 'Deskripsi abstrak awal.',
            'status' => 'pending',
            'pembimbing1_id' => $dosen1->id,
            'pembimbing2_id' => $dosen2->id,
        ]);

        $this->assertSame('Judul Awal Skripsi Mahasiswa', $thesis->title);
        $this->assertNull($thesis->final_title);

        $newTitle = 'SISTEM INFORMASI REKAM MEDIS BERBASIS WEB (STUDI KASUS: UPTD PUSKESMAS WANAYASA)';

        $response = $this->actingAs($admin)->put(route('theses.update', $thesis), [
            'final_title' => $newTitle,
            'pembimbing1_id' => $dosen1->id,
            'pembimbing2_id' => $dosen2->id,
        ]);

        $response->assertRedirect();
        $thesis->refresh();

        $this->assertSame($newTitle, $thesis->final_title);
        $this->assertSame($newTitle, $thesis->title);
        $this->assertSame($newTitle, $thesis->display_title);

        // Student Dashboard view check
        $dashboardResponse = $this->actingAs($student)->get(route('dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee($newTitle);
    }
}
