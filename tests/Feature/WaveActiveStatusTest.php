<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Wave;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WaveActiveStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_archived_wave_is_not_returned_by_get_current_active_or_shown_on_dashboards(): void
    {
        $archivedWave = Wave::create([
            'name' => 'Gelombang 2 Semester Ganjil 2026/2027',
            'start_date' => now()->subDays(25)->toDateString(),
            'end_date' => now()->subDays(10)->toDateString(),
            'is_active' => false,
        ]);

        $this->assertNull(Wave::getCurrentActive());

        $dosen = User::factory()->create(['role' => 'dosen', 'is_active' => true]);
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $this->actingAs($dosen)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Gelombang Aktif')
            ->assertDontSee($archivedWave->name);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Gelombang Ujian Sedang Berjalan');
    }

    public function test_active_wave_with_past_end_date_shows_pendaftaran_berakhir_instead_of_hari_terakhir(): void
    {
        $activeExpiredWave = Wave::create([
            'name' => 'Gelombang Aktif Lewat Tanggal',
            'start_date' => now()->subDays(15)->toDateString(),
            'end_date' => now()->subDays(2)->toDateString(),
            'is_active' => true,
        ]);

        $this->assertSame($activeExpiredWave->id, Wave::getCurrentActive()?->id);

        $dosen = User::factory()->create(['role' => 'dosen', 'is_active' => true]);

        $this->actingAs($dosen)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Gelombang Aktif Lewat Tanggal')
            ->assertSee('Pendaftaran Berakhir')
            ->assertDontSee('Hari Terakhir!')
            ->assertDontSee('( - )');
    }
}
