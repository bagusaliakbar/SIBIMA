<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SessionSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_idle_timeout_config_is_60_minutes_with_2_minute_warning(): void
    {
        $this->assertSame(60, (int) config('session.idle_timeout'));
        $this->assertSame(2, (int) config('session.idle_warning'));
    }

    public function test_authenticated_user_is_auto_logged_out_after_60_minutes_idle(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $expiredTimestamp = now()->subMinutes(61)->timestamp;

        $response = $this->actingAs($user)
            ->withSession(['last_activity_at' => $expiredTimestamp])
            ->get(route('dashboard'));

        $this->assertGuest();
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'activity' => 'Auto Logout',
            'module' => 'Auth',
        ]);
    }

    public function test_ajax_request_returns_401_json_when_session_idle_expired(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $expiredTimestamp = now()->subMinutes(65)->timestamp;

        $response = $this->actingAs($user)
            ->withSession(['last_activity_at' => $expiredTimestamp])
            ->getJson(route('notifications.index'));

        $this->assertGuest();
        $response->assertStatus(401);
        $response->assertJson([
            'expired' => true,
        ]);
        $response->assertJsonStructure(['message', 'expired', 'redirect']);
    }

    public function test_passive_notification_polling_does_not_extend_idle_activity_or_online_status(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $originalTimestamp = now()->subMinutes(30)->timestamp;
        Cache::forget('user-is-online-' . $user->id);

        $response = $this->actingAs($user)
            ->withSession(['last_activity_at' => $originalTimestamp])
            ->withHeaders([
                'X-Requested-With' => 'XMLHttpRequest',
                'X-SIBIMA-Passive' => '1',
                'Accept' => 'application/json',
            ])
            ->get(route('notifications.index'));

        $response->assertStatus(200);
        $this->assertSame($originalTimestamp, session('last_activity_at'));
        $this->assertFalse(Cache::has('user-is-online-' . $user->id));
    }

    public function test_session_keep_alive_refreshes_last_activity_and_returns_csrf_token(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $oldTimestamp = now()->subMinutes(55)->timestamp;

        $response = $this->actingAs($user)
            ->withSession(['last_activity_at' => $oldTimestamp])
            ->postJson(route('session.keep-alive'));

        $response->assertStatus(200);
        $response->assertJson([
            'ok' => true,
            'idle_timeout' => 60,
        ]);
        $response->assertJsonStructure(['ok', 'csrf_token', 'idle_timeout', 'server_time']);
        $this->assertGreaterThan($oldTimestamp, session('last_activity_at'));
    }

    public function test_idle_logout_redirects_to_login_with_status_message(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $response = $this->actingAs($user)->post(route('logout'), [
            'reason' => 'idle',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'activity' => 'Auto Logout',
            'module' => 'Auth',
        ]);
    }

    private function enableRealCsrfVerification(): void
    {
        $this->app->bind(ValidateCsrfToken::class, function ($app) {
            return new class($app, $app['encrypter']) extends ValidateCsrfToken
            {
                protected function runningUnitTests()
                {
                    return false;
                }
            };
        });
    }

    public function test_guest_token_mismatch_on_login_redirects_gracefully_without_flashing_password(): void
    {
        $this->enableRealCsrfVerification();

        $response = $this->from(route('login'))->post(route('login'), [
            '_token' => 'stale-expired-token',
            'username' => '20210001',
            'password' => 'secret-password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');
        $response->assertSessionHasInput('username', '20210001');
        $this->assertFalse(session()->hasOldInput('password'));
    }

    public function test_authenticated_token_mismatch_redirects_back_with_warning_and_preserved_input(): void
    {
        $this->enableRealCsrfVerification();
        $user = User::factory()->create(['is_active' => true]);

        $response = $this->actingAs($user)
            ->from(route('profile.edit'))
            ->post(route('logout'), [
                '_token' => 'stale-expired-token',
                'custom_note' => 'keep-this-input',
                'password' => 'must-not-flash',
            ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('warning');
        $response->assertSessionHasInput('custom_note', 'keep-this-input');
        $this->assertFalse(session()->hasOldInput('password'));
    }

    public function test_ajax_token_mismatch_returns_419_json_with_fresh_csrf_token(): void
    {
        $this->enableRealCsrfVerification();

        $response = $this->postJson(route('login'), [
            '_token' => 'stale-expired-token',
            'username' => '20210001',
            'password' => 'password',
        ]);

        $response->assertStatus(419);
        $response->assertJson([
            'expired' => true,
        ]);
        $response->assertJsonStructure(['message', 'expired', 'csrf_token', 'redirect']);
    }

    public function test_guest_csrf_refresh_endpoint_and_expired_login_banner_work(): void
    {
        $csrfResponse = $this->getJson(route('csrf.refresh'));
        $csrfResponse->assertStatus(200);
        $csrfResponse->assertJsonStructure(['csrf_token']);

        $loginResponse = $this->get(route('login', ['expired' => 1]));
        $loginResponse->assertStatus(200);
        $loginResponse->assertSee('60 menit');
    }
}
