<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventBackHistory
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Skip no-cache headers for Livewire internal requests (SPA navigation).
        // wire:navigate uses browser-level prefetch/caching that conflicts with
        // aggressive no-cache directives. Regular browser requests still get
        // the no-cache headers to prevent back-button access after logout.
        if ($request->hasHeader('X-Livewire')) {
            return $response;
        }

        // Set headers using HeaderBag so it works for all Symfony responses,
        // including BinaryFileResponse used by export/download endpoints.
        $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sun, 02 Jan 1990 00:00:00 GMT');

        return $response;
    }
}
