<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserLoginActivityMonitoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_mahasiswa_and_dosen_cannot_access_login_activity(): void
    {
        $mhs = User::factory()->create(['role' => 'mahasiswa']);
        $dosen = User::factory()->create(['role' => 'dosen']);

        $this->actingAs($mhs)->get(route('admin.logs.login-activity'))->assertStatus(403);
        $this->actingAs($dosen)->get(route('admin.logs.login-activity'))->assertStatus(403);

        $this->actingAs($mhs)->get(route('admin.logs.login-activity.export'))->assertStatus(403);
        $this->actingAs($dosen)->get(route('admin.logs.login-activity.export'))->assertStatus(403);

        $this->actingAs($mhs)->get(route('admin.logs.login-activity.user-history', $mhs))->assertStatus(403);
        $this->actingAs($dosen)->get(route('admin.logs.login-activity.user-history', $dosen))->assertStatus(403);
    }

    public function test_admin_and_kaprodi_can_access_and_see_login_leaderboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin SIBIMA']);
        $kaprodi = User::factory()->create(['role' => 'kaprodi', 'name' => 'Kaprodi SIBIMA']);

        $userRajin = User::factory()->create([
            'role' => 'mahasiswa', 
            'name' => 'Budi Teraktif', 
            'identifier' => 'D1A210001',
            'last_login_at' => Carbon::now()
        ]);
        $userSedang = User::factory()->create([
            'role' => 'dosen', 
            'name' => 'Dosen Aktif', 
            'identifier' => '0401018801',
            'last_login_at' => Carbon::now()->subHours(2)
        ]);

        // Create 3 login logs for userRajin
        for ($i = 0; $i < 3; $i++) {
            ActivityLog::create([
                'user_id' => $userRajin->id,
                'activity' => 'Login',
                'description' => 'User berhasil login ke sistem.',
                'module' => 'Auth',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0',
                'created_at' => Carbon::now()->subMinutes($i * 10),
            ]);
        }

        // Create 1 login log for userSedang
        ActivityLog::create([
            'user_id' => $userSedang->id,
            'activity' => 'Login',
            'description' => 'User berhasil login ke sistem.',
            'module' => 'Auth',
            'ip_address' => '192.168.1.10',
            'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) Safari/604.1',
            'created_at' => Carbon::now()->subHours(2),
        ]);

        // Admin access
        $responseAdmin = $this->actingAs($admin)->get(route('admin.logs.login-activity', ['period' => 'this_month']));
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertSee('Budi Teraktif');
        $responseAdmin->assertSee('Dosen Aktif');
        $responseAdmin->assertSee('Peringkat Paling Aktif');

        // Kaprodi access
        $responseKaprodi = $this->actingAs($kaprodi)->get(route('admin.logs.login-activity'));
        $responseKaprodi->assertStatus(200);
        $responseKaprodi->assertSee('Budi Teraktif');
    }

    public function test_inactive_users_tab_displays_users_never_logged_in_or_inactive(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $userNever = User::factory()->create([
            'role' => 'mahasiswa', 
            'name' => 'Siswa Belum Login', 
            'identifier' => 'D1A210099',
            'last_login_at' => null
        ]);

        $userLama = User::factory()->create([
            'role' => 'dosen', 
            'name' => 'Dosen Sudah Lama Tidak Login', 
            'identifier' => '0401019901',
            'last_login_at' => Carbon::now()->subDays(45)
        ]);

        $response = $this->actingAs($admin)->get(route('admin.logs.login-activity', ['tab' => 'inactive']));
        $response->assertStatus(200);
        $response->assertSee('Siswa Belum Login');
        $response->assertSee('Belum Pernah Login');
        $response->assertSee('Dosen Sudah Lama Tidak Login');
        $response->assertSee('Pasif');
    }

    public function test_user_session_history_json_endpoint(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create([
            'role' => 'mahasiswa', 
            'name' => 'Ahmad Pelajar',
            'last_login_at' => Carbon::now()
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'activity' => 'Login',
            'description' => 'User berhasil login ke sistem.',
            'module' => 'Auth',
            'ip_address' => '10.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0',
            'created_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($admin)->getJson(route('admin.logs.login-activity.user-history', $user));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'user' => [
                'id',
                'name',
                'email',
                'identifier',
                'role',
                'avatar_url',
                'is_online',
                'last_login_at',
                'total_logins',
                'month_logins',
                'week_logins',
            ],
            'sessions' => [
                '*' => [
                    'id',
                    'created_at_formatted',
                    'time_ago',
                    'ip_address',
                    'device',
                    'raw_user_agent',
                ]
            ]
        ]);

        $response->assertJsonFragment([
            'name' => 'Ahmad Pelajar',
            'ip_address' => '10.0.0.1',
        ]);
    }

    public function test_export_login_activity_excel(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.logs.login-activity.export'));
        $response->assertStatus(200);
        $this->assertTrue(
            str_contains($response->headers->get('content-disposition'), '.xlsx')
        );
    }
}
