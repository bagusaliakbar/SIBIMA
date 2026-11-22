<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Default to private reverse-proxy CIDRs + official Cloudflare edge IP ranges (never wildcard '*' by default)
        $defaultTrustedProxies = implode(',', [
            '127.0.0.1',
            '::1',
            '10.0.0.0/8',
            '172.16.0.0/12',
            '192.168.0.0/16',
            // Cloudflare IPv4
            '173.245.48.0/20', '103.21.244.0/22', '103.22.200.0/22', '103.31.4.0/22',
            '141.101.64.0/18', '108.162.192.0/18', '190.93.240.0/20', '188.114.96.0/20',
            '197.234.240.0/22', '198.41.128.0/17', '162.158.0.0/15', '104.16.0.0/13',
            '104.24.0.0/14', '172.64.0.0/13', '131.0.72.0/22',
            // Cloudflare IPv6
            '2400:cb00::/32', '2606:4700::/32', '2803:f800::/32', '2405:b500::/32',
            '2405:8100::/32', '2a06:98c0::/29', '2c0f:f248::/32',
        ]);
        $rawTrustedProxies = trim((string) env('TRUSTED_PROXIES', ''));
        $trustedProxies = $rawTrustedProxies !== '' ? $rawTrustedProxies : $defaultTrustedProxies;
        $middleware->trustProxies(
            at: $trustedProxies === '*'
                ? '*'
                : array_values(array_filter(array_map('trim', explode(',', $trustedProxies))))
        );

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        $middleware->append(\App\Http\Middleware\ContentSecurityPolicy::class);
        $middleware->web(append: [
            \App\Http\Middleware\UpdateUserOnlineStatus::class,
            \App\Http\Middleware\CheckMaintenanceMode::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $handleTokenMismatch = function (Request $request) {
            if ($request->hasSession()) {
                $request->session()->regenerateToken();
            }

            $isAuthenticated = Auth::check();
            $fallbackUrl = $isAuthenticated ? route('dashboard') : route('login');
            $previousUrl = url()->previous();
            $logoutUrl = route('logout');
            $safePreviousUrl = ($previousUrl && $previousUrl !== $logoutUrl)
                ? $previousUrl
                : $fallbackUrl;

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => $isAuthenticated
                        ? 'Token keamanan halaman telah diperbarui. Silakan ulangi aksi Anda.'
                        : 'Sesi halaman telah berakhir karena tidak ada aktivitas. Silakan login kembali.',
                    'expired' => true,
                    'csrf_token' => csrf_token(),
                    'redirect' => $isAuthenticated ? $safePreviousUrl : route('login', ['expired' => 1]),
                ], 419);
            }

            if (!$isAuthenticated) {
                $isGuestForm = $request->is('register', 'forgot-password', 'reset-password*');
                if ($isGuestForm) {
                    $guestTarget = $request->is('register')
                        ? route('register')
                        : ($request->is('forgot-password') ? route('password.request') : $safePreviousUrl);

                    return redirect()->to($guestTarget)
                        ->withInput($request->except(['password', 'password_confirmation', '_token']))
                        ->with('status', 'Token keamanan formulir telah diperbarui. Silakan kirim ulang.');
                }

                $redirect = redirect()->route('login')
                    ->with('status', 'Sesi halaman telah berakhir karena tidak ada aktivitas. Silakan login kembali.');

                if ($request->is('login') && $request->filled('username')) {
                    $redirect->withInput($request->only('username'));
                }

                return $redirect;
            }

            return redirect()->to($safePreviousUrl)
                ->withInput($request->except(['password', 'password_confirmation', 'current_password', '_token']))
                ->with('warning', 'Token keamanan formulir telah diperbarui karena halaman terlalu lama terbuka. Silakan kirim ulang data Anda.');
        };

        $exceptions->render(function (TokenMismatchException $e, Request $request) use ($handleTokenMismatch) {
            return $handleTokenMismatch($request);
        });

        $exceptions->render(function (HttpException $e, Request $request) use ($handleTokenMismatch) {
            if ($e->getStatusCode() === 419) {
                return $handleTokenMismatch($request);
            }

            return null;
        });
    })->create();
