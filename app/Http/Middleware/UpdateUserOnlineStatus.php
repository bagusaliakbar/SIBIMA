<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UpdateUserOnlineStatus
{
    /**
     * Handle an incoming request and update user online status in Cache.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            Cache::put('user-is-online-' . Auth::id(), true, now()->addMinutes(3));
        }

        return $next($request);
    }
}
