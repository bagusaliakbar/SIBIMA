<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = $request->user();
        if ($user) {
            $user->update(['last_login_at' => now()]);
        }

        $request->session()->regenerate();
        $request->session()->put('last_activity_at', now()->timestamp);

        if ($user && filled($user->identifier) && (string) $request->input('password') === (string) $user->identifier) {
            $request->session()->put('using_default_password', true);
            $request->session()->flash(
                'warning',
                'Keamanan Akun: Anda masih menggunakan password standar (NPM/NIDN). Demi keamanan akun Anda, silakan segera ganti password melalui menu Profil.'
            );
        }
        
        ActivityLog::log('Login', 'User berhasil login ke sistem.', 'Auth');

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $isIdleLogout = $request->input('reason') === 'idle';
        $idleTimeoutMinutes = max(1, (int) config('session.idle_timeout', 60));

        if ($userId = Auth::id()) {
            \Illuminate\Support\Facades\Cache::forget('user-is-online-' . $userId);
        }

        ActivityLog::log(
            $isIdleLogout ? 'Auto Logout' : 'Logout',
            $isIdleLogout
                ? "Sesi berakhir otomatis karena tidak ada aktivitas selama {$idleTimeoutMinutes} menit."
                : 'User keluar dari sistem.',
            'Auth'
        );

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($isIdleLogout) {
            return redirect()->route('login')->with(
                'status',
                "Sesi Anda telah berakhir karena tidak ada aktivitas selama {$idleTimeoutMinutes} menit. Silakan login kembali."
            );
        }

        return redirect('/');
    }
}
