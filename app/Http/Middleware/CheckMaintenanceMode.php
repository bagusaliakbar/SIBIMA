<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckMaintenanceMode
{
    /**
     * Enforce user-facing maintenance lock mode for targeted roles (mahasiswa / dosen).
     * Admin & Kaprodi, auth routes, impersonation leave, and system cache routes are always exempt.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return $next($request);
        }

        if ($request->routeIs('logout', 'impersonate.leave', 'system.clear-cache', 'faqs.index')) {
            return $next($request);
        }

        $user = Auth::user();
        $config = Setting::getMaintenanceConfig();

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
                    'ends_at' => $config['ends_at'] ?: null,
                ], 503);
            }

            return response()->view('errors.maintenance-user', [
                'config' => $config,
                'user' => $user,
            ], 503);
        }

        return $next($request);
    }
}
