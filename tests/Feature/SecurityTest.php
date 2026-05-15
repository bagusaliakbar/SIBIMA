<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_mahasiswa_cannot_access_admin_monitoring()
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);

        $this->actingAs($mahasiswa)
            ->get(route('monitoring.index'))
            ->assertStatus(403);
    }

    public function test_mahasiswa_cannot_access_dosen_tasks()
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);

        $this->actingAs($mahasiswa)
            ->get(route('seminar-examiner.index'))
            ->assertStatus(403);
    }

    public function test_dosen_cannot_access_admin_monitoring()
    {
        $dosen = User::factory()->create(['role' => 'dosen']);

        $this->actingAs($dosen)
            ->get(route('monitoring.index'))
            ->assertStatus(403);
    }

    public function test_admin_can_access_monitoring()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('monitoring.index'))
            ->assertStatus(200);
    }

    public function test_unauthenticated_user_is_redirected_to_login()
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }
}
