<?php

namespace Tests\Feature;

use App\Models\MentoringSession;
use App\Models\Thesis;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MentoringScheduleGuideTest extends TestCase
{
    use RefreshDatabase;

    public function test_lecturer_can_see_panduan_dosen_button_and_guide_modal_on_mentoring_schedule_page(): void
    {
        $dosen = User::factory()->create(['role' => 'dosen', 'name' => 'Bagus Ali Akbar']);

        $response = $this->actingAs($dosen)
            ->get(route('mentoring-sessions.index'));

        $response->assertStatus(200);
        // Verify guide button exists
        $response->assertSee('Panduan Dosen');
        $response->assertSee('openGuideModal');
        
        // Verify modal content exists
        $response->assertSee('Panduan Jadwal Bimbingan');
        $response->assertSee('Alur Kerja Bimbingan');
        $response->assertSee('Pilihan 3 Tampilan');
        $response->assertSee('Fitur Canggih');
        $response->assertSee('Tanya & Jawab', false);
    }
}
