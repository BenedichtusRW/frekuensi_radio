<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileIsComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // KECUALIKAN SUPER ADMIN: Super Admin bebas masuk tanpa harus lengkapi profil
            if ($user->role === 'super_admin') {
                return $next($request);
            }
            
            // Check if profile is incomplete (Name contains 'Admin' or Photo is null)
            $isGenericName = preg_match('/^Admin\s*\d*$/i', $user->name);
            
            if (($isGenericName || !$user->profile_photo) && !$request->routeIs('profile.complete') && !$request->routeIs('logout')) {
                return redirect()->route('profile.complete')->with('info', 'Mohon lengkapi profil Anda terlebih dahulu.');
            }
        }

        return $next($request);
    }
}
