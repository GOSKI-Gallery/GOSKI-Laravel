<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=()');

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        $viteUrl = config('app.vite_dev_server_url');

        $csp = "default-src 'self'; "
            ."script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net {$viteUrl}; "
            ."style-src 'self' 'unsafe-inline' https://fonts.googleapis.com {$viteUrl}; "
            ."img-src 'self' data: https: {$viteUrl}; "
            ."font-src 'self' https://fonts.gstatic.com; "
            ."connect-src 'self' {$viteUrl} ws://localhost:5173; "
            ."frame-ancestors 'none';";

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
