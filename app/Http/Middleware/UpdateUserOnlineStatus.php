<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateUserOnlineStatus
{
    /**
     * Handle an incoming request: enforce active account check, server-side
     * idle session timeout, and update user online status in Cache.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user && !$user->is_active) {
                User::markOffline((int) $user->id);
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'message' => 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator.',
                        'redirect' => route('login'),
                    ], 401);
                }

                return redirect()->route('login')->withErrors([
                    'username' => 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator.',
                ]);
            }

            // Passive background polling must NOT extend idle session or online presence
            $isPassiveRequest = $request->header('X-SIBIMA-Passive') === '1'
                || $request->routeIs('dashboard.online-users')
                || ($request->routeIs('notifications.index') && ($request->expectsJson() || $request->ajax()));

            $idleTimeoutMinutes = max(1, (int) config('session.idle_timeout', 60));
            $idleTimeoutSeconds = $idleTimeoutMinutes * 60;
            $now = now()->timestamp;
            $lastActivity = $request->session()->get('last_activity_at');

            if ($lastActivity !== null && ($now - (int) $lastActivity) >= $idleTimeoutSeconds) {
                try {
                    ActivityLog::log(
                        'Auto Logout',
                        "Sesi berakhir otomatis karena tidak ada aktivitas selama {$idleTimeoutMinutes} menit.",
                        'Auth'
                    );
                } catch (\Throwable $e) {
                    // Never block auto-logout if activity logging fails
                }

                User::markOffline((int) $user->id);
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $expiredMessage = "Sesi Anda telah berakhir karena tidak ada aktivitas selama {$idleTimeoutMinutes} menit. Silakan login kembali.";

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'message' => $expiredMessage,
                        'expired' => true,
                        'redirect' => route('login', ['expired' => 1]),
                    ], 401);
                }

                return redirect()->route('login')->with('status', $expiredMessage);
            }

            if ($lastActivity === null || !$isPassiveRequest) {
                $request->session()->put('last_activity_at', $now);
            }

            if (!$isPassiveRequest) {
                $impersonatorId = $request->session()->get(\App\Http\Controllers\ImpersonationController::SESSION_IMPERSONATOR_ID);
                if ($impersonatorId) {
                    User::markOnline((int) $impersonatorId, 180);
                } elseif (Auth::id()) {
                    User::markOnline((int) Auth::id(), 180);
                }
            }
        }

        return $next($request);
    }
}
