<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // 1. Force HTTPS redirect (production only)
        if (app()->environment('production') && !$request->secure()) {
            return redirect()->secure($request->getRequestUri(), 301);
        }

        // 2. Prevent Clickjacking (X-Frame-Options)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // 3. Prevent MIME Type Sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // 4. XSS Protection (for older browsers)
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // 5. Referrer Policy (privacy & security)
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // 6. Strict Transport Security (HSTS) - force HTTPS for future requests
        if (app()->environment('production')) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        // 7. Content Security Policy (CSP) - mitigate XSS attacks
        $csp = "default-src 'self'; "
            . "script-src 'self' 'unsafe-inline' 'unsafe-eval' cdn.jsdelivr.net cdn.tailwindcss.com; "
            . "style-src 'self' 'unsafe-inline' fonts.googleapis.com cdn.jsdelivr.net cdn.tailwindcss.com; "
            . "font-src 'self' fonts.gstatic.com; "
            . "img-src 'self' data: https:; "
            . "connect-src 'self' https:; "
            . "frame-ancestors 'self'; "
            . "base-uri 'self'; "
            . "form-action 'self'";

        $response->headers->set('Content-Security-Policy', $csp);

        // 8. Prevent Information Disclosure
        $response->headers->remove('Server');
        $response->headers->remove('X-Powered-By');

        return $response;
    }
}
