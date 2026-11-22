<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckMaintenanceMode
{
    public const SESSION_BYPASS_KEY = 'maintenance_bypass_hash';

    /**
     * Enforce user-facing maintenance lock mode for targeted roles (mahasiswa / dosen)
     * and optionally for unauthenticated guests (with secret bypass URL for Admin & Kaprodi).
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->routeIs(
            'logout',
            'impersonate.leave',
            'system.clear-cache',
            'maintenance.bypass',
            'csrf.refresh',
            'signature.verify',
            'document.verify',
            'skl.verify',
            'sk-pembimbing.verify'
        )) {
            return $next($request);
        }

        $config = Setting::getMaintenanceConfig();

        // Guest (pre-login) lock enforcement when enabled
        if (!Auth::check()) {
            if (!Setting::isGuestMaintenanceLocked($config)) {
                return $next($request);
            }

            if ($this->hasValidBypassSession($request, $config) && (
                $request->is('login')
                || $request->routeIs(
                    'login',
                    'password.request',
                    'password.email',
                    'password.reset',
                    'password.store'
                )
            )) {
                return $next($request);
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => $config['message'] ?: 'SIBIMA sedang dalam pemeliharaan sistem.',
                    'maintenance' => true,
                    'estimate' => $config['estimate'] ?: null,
                    'ends_at' => Setting::resolveMaintenanceEndsAt($config),
                ], 503);
            }

            return response()->view('errors.maintenance-user', [
                'config' => $config,
                'user' => null,
            ], 503);
        }

        // Authenticated users: keep FAQ accessible unless locked? Keep existing FAQ exemption for logged-in users.
        if ($request->routeIs('faqs.index')) {
            return $next($request);
        }

        $user = Auth::user();

        if (
            $user
            && ($config['mode'] ?? 'banner') === 'lock'
            && Setting::isMaintenanceActiveForRole($user->role, $config)
        ) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => $config['message'] ?: 'SIBIMA sedang dalam pemeliharaan sistem.',
                    'maintenance' => true,
                    'estimate' => $config['estimate'] ?: null,
                    'ends_at' => Setting::resolveMaintenanceEndsAt($config),
                ], 503);
            }

            return response()->view('errors.maintenance-user', [
                'config' => $config,
                'user' => $user,
            ], 503);
        }

        return $next($request);
    }

    /**
     * Check whether the current session holds a valid hash matching the active bypass secret.
     */
    public static function hasValidBypassSession(Request $request, ?array $config = null): bool
    {
        $cfg = $config ?? Setting::getMaintenanceConfig();
        $storedSecret = trim((string) ($cfg['bypass_secret'] ?? ''));
        $sessionHash = (string) $request->session()->get(self::SESSION_BYPASS_KEY, '');

        if ($storedSecret === '' || $sessionHash === '') {
            return false;
        }

        return hash_equals(hash('sha256', $storedSecret), $sessionHash);
    }
}
