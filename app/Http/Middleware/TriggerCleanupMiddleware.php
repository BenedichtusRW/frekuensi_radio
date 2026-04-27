<?php

namespace App\Http\Middleware;

use App\Jobs\CleanupExpiredData;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TriggerCleanupMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Dispatch cleanup job randomly (~5% of requests)
        // This ensures cleanup happens without external scheduler
        if (rand(1, 20) === 1) {
            // Dispatch asynchronously - does not block request
            CleanupExpiredData::dispatch();
        }

        return $next($request);
    }
}
