<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintenanceModeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_enable_banner_maintenance_for_students_only(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $student = User::factory()->create(['role' => 'mahasiswa', 'is_active' => true]);
        $dosen = User::factory()->create(['role' => 'dosen', 'is_active' => true]);

        $this->actingAs($admin)
            ->put(route('admin.maintenance.update'), [
                'enabled' => '1',
                'mode' => 'banner',
                'target' => 'mahasiswa',
                'title' => 'Pemeliharaan Terjadwal Malam Ini',
                'message' => 'Sistem akan dilakukan update server pukul 22.00 WIB.',
                'estimate' => '23:00 WIB',
                'ends_at' => now()->addHours(2)->format('Y-m-d\TH:i'),
            ])
            ->assertRedirect(route('admin.maintenance.index'))
            ->assertSessionHas('success');

        $this->assertNotEmpty(Setting::getMaintenanceConfig()['ends_at']);

        // Student sees banner and can still access dashboard (200)
        $this->actingAs($student)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Pemeliharaan Terjadwal Malam Ini')
            ->assertSee('Estimasi: 23:00 WIB');

        // Dosen is NOT targeted, so banner is not shown
        $this->actingAs($dosen)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Pemeliharaan Terjadwal Malam Ini');

        // Admin sees compact active maintenance reminder
        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Mode Pemeliharaan Aktif');
    }

    public function test_lock_mode_blocks_targeted_users_with_503_while_admin_and_kaprodi_keep_full_access(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $kaprodi = User::factory()->create(['role' => 'kaprodi', 'is_active' => true]);
        $student = User::factory()->create(['role' => 'mahasiswa', 'is_active' => true]);
        $dosen = User::factory()->create(['role' => 'dosen', 'is_active' => true]);

        Setting::saveMaintenanceConfig([
            'enabled' => true,
            'mode' => 'lock',
            'target' => 'both',
            'title' => 'SIBIMA Sedang Diperbarui',
            'message' => 'Mohon tunggu hingga pemeliharaan selesai.',
            'estimate' => '30 Menit',
            'ends_at' => now()->addHour()->toIso8601String(),
        ]);

        // Student gets 503 maintenance screen with countdown
        $this->actingAs($student)
            ->get(route('dashboard'))
            ->assertStatus(503)
            ->assertSee('SIBIMA Sedang Diperbarui')
            ->assertSee('Estimasi selesai: 30 Menit')
            ->assertSee('Hitung Mundur Estimasi Selesai');

        // AJAX request gets 503 JSON including ends_at
        $this->actingAs($dosen)
            ->getJson(route('dashboard'))
            ->assertStatus(503)
            ->assertJson(['maintenance' => true])
            ->assertJsonStructure(['message', 'maintenance', 'estimate', 'ends_at']);

        // Student can still log out
        $this->actingAs($student)
            ->post(route('logout'))
            ->assertRedirect('/');

        // Admin and Kaprodi are NEVER locked out
        $this->actingAs($admin)
            ->get(route('admin.maintenance.index'))
            ->assertOk();

        $this->actingAs($kaprodi)
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_students_and_dosen_cannot_access_maintenance_settings_page(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa', 'is_active' => true]);
        $dosen = User::factory()->create(['role' => 'dosen', 'is_active' => true]);

        $this->actingAs($student)->get(route('admin.maintenance.index'))->assertStatus(403);
        $this->actingAs($dosen)->get(route('admin.maintenance.index'))->assertStatus(403);
    }

    public function test_estimate_clock_time_automatically_activates_countdown_when_ends_at_is_empty(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa', 'is_active' => true]);

        Setting::saveMaintenanceConfig([
            'enabled' => true,
            'mode' => 'lock',
            'target' => 'both',
            'title' => 'Pemeliharaan Sistem SIBIMA',
            'message' => 'Upgrade server.',
            'estimate' => 'Sampai 12.00',
            'ends_at' => '',
        ]);

        $resolved = Setting::resolveMaintenanceEndsAt();
        $this->assertNotNull($resolved);
        $this->assertStringContainsString('T12:00:00', $resolved);

        $this->actingAs($student)
            ->get(route('dashboard'))
            ->assertStatus(503)
            ->assertSee('Hitung Mundur Estimasi Selesai')
            ->assertSee('Estimasi selesai: Sampai 12.00');
    }
}
