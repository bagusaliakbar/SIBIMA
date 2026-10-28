<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicy
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Standard defensive HTTP security headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(self), microphone=(), geolocation=(self)');

        // Only add CSP to responses that are actually returning HTML
        if ($response->headers->get('Content-Type') && str_contains($response->headers->get('Content-Type'), 'text/html')) {
            $isLocal = app()->environment('local', 'testing');
            $localDevOrigins = $isLocal ? ' http://localhost:* http://127.0.0.1:* http://[::1]:* ws://localhost:* ws://127.0.0.1:*' : '';

            $csp = [
                "default-src 'self' https: data: blob:{$localDevOrigins}",
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://challenges.cloudflare.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://cdn.tailwindcss.com data: blob:{$localDevOrigins}",
                "style-src 'self' 'unsafe-inline' https://fonts.bunny.net https://fonts.googleapis.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net data: blob:{$localDevOrigins}",
                "img-src 'self' data: blob: https:{$localDevOrigins}",
                "font-src 'self' data: https://fonts.bunny.net https://fonts.gstatic.com https://cdnjs.cloudflare.com{$localDevOrigins}",
                "connect-src 'self' https: wss: ws:{$localDevOrigins}",
                "frame-src 'self' https://challenges.cloudflare.com https://drive.google.com https://docs.google.com blob: data:",
                "frame-ancestors 'self'",
                "object-src 'none'",
                "base-uri 'self'",
                "form-action 'self'",
            ];

            $response->headers->set('Content-Security-Policy', implode('; ', $csp));
        }

        return $response;
    }
}
