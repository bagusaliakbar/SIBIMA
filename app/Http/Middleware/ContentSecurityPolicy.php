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

        // Only add CSP to responses that are actually returning HTML
        if ($response->headers->get('Content-Type') && str_contains($response->headers->get('Content-Type'), 'text/html')) {
            // Highly permissive CSP for local development to avoid breaking Vite/Metronic
            $csp = [
                "default-src * 'unsafe-inline' 'unsafe-eval' data: blob:",
                "script-src * 'unsafe-inline' 'unsafe-eval' data: blob:",
                "style-src * 'unsafe-inline' data: blob:",
                "img-src * data: blob: https:",
                "font-src * data: https:",
                "connect-src * ws: wss: http://localhost:* http://127.0.0.1:* http://[::1] https:",
                "frame-src *",
                "object-src 'none'",
                "base-uri 'self'",
                "form-action 'self'",
            ];

            $response->headers->set('Content-Security-Policy', implode('; ', $csp));
        }

        return $response;
    }
}
