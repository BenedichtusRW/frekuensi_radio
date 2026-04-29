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
            
            if (($isGenericName || !$user->profile_photo) && !$request->routeIs('profile.store') && !$request->routeIs('logout')) {
                // Blokir segala bentuk submit data atau AJAX (kecuali logout & simpan profil)
                if ($request->ajax() || in_array($request->method(), ['POST', 'PUT', 'DELETE', 'PATCH'])) {
                    if ($request->wantsJson() || $request->ajax()) {
                        return response()->json(['message' => 'Silakan lengkapi profil Anda terlebih dahulu.'], 403);
                    }
                    return redirect()->back()->with('error', 'Lengkapi profil Anda terlebih dahulu.');
                }
                
                // Untuk request GET biasa, biarkan lolos agar halaman utama bisa dirender
                // (nanti akan di-blur oleh UI di app.blade.php)
            }
        }

        return $next($request);
    }
}
