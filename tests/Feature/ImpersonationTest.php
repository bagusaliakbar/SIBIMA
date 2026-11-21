<?php

namespace Tests\Feature;

use App\Http\Controllers\ImpersonationController;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ImpersonationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_impersonate_student_and_return_to_admin_with_audit_logs(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Super Admin', 'is_active' => true]);
        $student = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'Budi Mahasiswa',
            'identifier' => 'D1A220001',
            'is_active' => true,
            'last_login_at' => null,
        ]);

        // Start impersonation
        $response = $this->actingAs($admin)->post(route('users.impersonate', $student));

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($student);
        $this->assertEquals($admin->id, session(ImpersonationController::SESSION_IMPERSONATOR_ID));

        // Target user's last_login_at must not be overwritten
        $student->refresh();
        $this->assertNull($student->last_login_at);

        // Persistent Login As banner is visible on dashboard
        $dashboard = $this->get(route('dashboard'));
        $dashboard->assertOk();
        $dashboard->assertSee('Mode Login As Aktif:');
        $dashboard->assertSee('Budi Mahasiswa');
        $dashboard->assertSee('Kembali ke Admin');

        // Target user must NOT be marked online; impersonator stays online
        $this->assertFalse(Cache::has('user-is-online-' . $student->id));
        $this->assertTrue(Cache::has('user-is-online-' . $admin->id));

        // Audit log recorded for Start under Admin
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'activity' => 'Login As (Mulai)',
            'module' => 'Auth',
        ]);

        // Leave impersonation
        $leaveResponse = $this->post(route('impersonate.leave'));
        $leaveResponse->assertRedirect(route('users.index'));
        $leaveResponse->assertSessionHas('success');
        $this->assertAuthenticatedAs($admin);
        $this->assertNull(session(ImpersonationController::SESSION_IMPERSONATOR_ID));

        // Audit log recorded for Leave under Admin
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'activity' => 'Login As (Selesai)',
            'module' => 'Auth',
        ]);
    }

    public function test_kaprodi_can_impersonate_dosen_and_return(): void
    {
        $kaprodi = User::factory()->create(['role' => 'kaprodi', 'name' => 'Dr. Kaprodi', 'is_active' => true]);
        $dosen = User::factory()->create(['role' => 'dosen', 'name' => 'Pak Dosen', 'identifier' => '0412345678', 'is_active' => true]);

        $this->actingAs($kaprodi)
            ->post(route('users.impersonate', $dosen))
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($dosen);
        $this->assertEquals($kaprodi->id, session(ImpersonationController::SESSION_IMPERSONATOR_ID));

        $this->post(route('impersonate.leave'))
            ->assertRedirect(route('users.index'));

        $this->assertAuthenticatedAs($kaprodi);
    }

    public function test_cannot_impersonate_admin_kaprodi_self_or_inactive_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $otherAdmin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $kaprodi = User::factory()->create(['role' => 'kaprodi', 'is_active' => true]);
        $inactiveStudent = User::factory()->create(['role' => 'mahasiswa', 'is_active' => false]);

        // Cannot impersonate another admin
        $this->actingAs($admin)
            ->post(route('users.impersonate', $otherAdmin))
            ->assertStatus(403);
        $this->assertAuthenticatedAs($admin);

        // Cannot impersonate kaprodi
        $this->actingAs($admin)
            ->post(route('users.impersonate', $kaprodi))
            ->assertStatus(403);
        $this->assertAuthenticatedAs($admin);

        // Cannot impersonate self
        $this->actingAs($admin)
            ->post(route('users.impersonate', $admin))
            ->assertSessionHas('error');
        $this->assertAuthenticatedAs($admin);

        // Cannot impersonate inactive user
        $this->actingAs($admin)
            ->post(route('users.impersonate', $inactiveStudent))
            ->assertSessionHas('error');
        $this->assertAuthenticatedAs($admin);
    }

    public function test_dosen_and_mahasiswa_cannot_start_or_forge_leave_impersonation(): void
    {
        $dosen = User::factory()->create(['role' => 'dosen', 'is_active' => true]);
        $student = User::factory()->create(['role' => 'mahasiswa', 'is_active' => true]);

        $this->actingAs($dosen)
            ->post(route('users.impersonate', $student))
            ->assertStatus(403);

        $this->actingAs($student)
            ->post(route('users.impersonate', $dosen))
            ->assertStatus(403);

        // Calling leave without active impersonation session returns 403
        $this->actingAs($student)
            ->post(route('impersonate.leave'))
            ->assertStatus(403);
    }

    public function test_password_change_and_account_deletion_are_blocked_during_impersonation(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $student = User::factory()->create([
            'role' => 'mahasiswa',
            'is_active' => true,
            'password' => Hash::make('OldPassword123!'),
        ]);

        $this->actingAs($admin)
            ->post(route('users.impersonate', $student))
            ->assertRedirect(route('dashboard'));

        // Profile page displays credential protection notice
        $this->get(route('profile.edit'))
            ->assertOk()
            ->assertSee('Proteksi Kredensial Mode Login As');

        // Direct PUT /password is blocked with 403
        $this->put(route('password.update'), [
            'current_password' => 'OldPassword123!',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ])->assertStatus(403);

        // Direct DELETE /profile is blocked with 403
        $this->delete(route('profile.destroy'), [
            'password' => 'OldPassword123!',
        ])->assertStatus(403);

        $this->assertDatabaseHas('users', ['id' => $student->id]);
    }

    public function test_leave_fails_closed_if_impersonator_account_was_deactivated(): void
    {
        $kaprodi = User::factory()->create(['role' => 'kaprodi', 'is_active' => true]);
        $student = User::factory()->create(['role' => 'mahasiswa', 'is_active' => true]);

        $this->actingAs($kaprodi)
            ->post(route('users.impersonate', $student))
            ->assertRedirect(route('dashboard'));

        // Deactivate original kaprodi while impersonating
        $kaprodi->update(['is_active' => false]);

        $this->post(route('impersonate.leave'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_users_index_shows_login_as_only_for_active_dosen_and_mahasiswa(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $activeStudent = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Aktif Mhs', 'is_active' => true]);
        $inactiveStudent = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Pending Mhs', 'is_active' => false]);
        $kaprodi = User::factory()->create(['role' => 'kaprodi', 'name' => 'Akun Kaprodi', 'is_active' => true]);

        $response = $this->actingAs($admin)->get(route('users.index'));
        $response->assertOk();
        $response->assertSee(route('users.impersonate', $activeStudent->id), false);
        $response->assertDontSee('action="' . route('users.impersonate', $inactiveStudent->id) . '"', false);
        $response->assertDontSee('action="' . route('users.impersonate', $kaprodi->id) . '"', false);
    }
}
