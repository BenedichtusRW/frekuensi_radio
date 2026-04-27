<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin(): View | RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->intended(route('dashboard'));
        }

        // Server-side 2FA detection — no AJAX needed
        $admin = \App\Models\User::first();
        $has2fa = $admin && $admin->two_factor_enabled && !empty($admin->google2fa_secret);
        $adminEmail = $admin ? $admin->email : '';

        return view('auth.login', [
            'has2fa' => $has2fa,
            'adminEmail' => $adminEmail,
        ]);
    }

    /**
     * Handle authentication attempt.
     */
    public function authenticate(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = $request->input('email');
        $user = \App\Models\User::where('email', $email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Akun tidak ditemukan.'])->onlyInput('email');
        }

        // SECURITY CHECK: Pastikan akun aktif
        if (!$user->is_active) {
            $this->logSuspiciousActivity($request, 'blocked_login', 'Percobaan login ke akun nonaktif: ' . $email);
            return back()->withErrors(['email' => 'Akun Anda telah dinonaktifkan. Silakan hubungi Super Admin.']);
        }

        $throttleKey = Str::lower($email) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $this->logSuspiciousActivity($request, 'brute_force_detected', 'Terdeteksi percobaan brute force pada email: ' . $email);
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors(['email' => 'Terlalu banyak percobaan. Coba lagi dalam ' . $seconds . ' detik.']);
        }

        // Logic 2FA Aktif (Hanya Kode, Tanpa Password sesuai permintaan)
        if ($user->two_factor_enabled && !empty($user->google2fa_secret)) {
            $request->validate(['two_factor_code' => 'required|digits:6']);
            
            $google2fa = new \PragmaRX\Google2FA\Google2FA();
            $valid = $google2fa->verifyKey($user->google2fa_secret, $request->two_factor_code);

            if ($valid) {
                Auth::login($user, $request->boolean('remember'));
                RateLimiter::clear($throttleKey);
                $request->session()->regenerate();
                return redirect()->intended(route('dashboard'));
            }

            RateLimiter::hit($throttleKey, 60);
            $this->logSuspiciousActivity($request, 'failed_2fa', 'Kode 2FA salah untuk email: ' . $email);
            return back()->withErrors(['two_factor_code' => 'Kode autentikasi salah.'])->onlyInput('email');
        }

        // Logic 2FA Mati (Email + Password Biasa)
        $request->validate(['password' => 'required']);
        if (Auth::attempt(['email' => $email, 'password' => $request->password], $request->boolean('remember'))) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        RateLimiter::hit($throttleKey, 60);
        $this->logSuspiciousActivity($request, 'failed_login', 'Gagal login (password salah) untuk email: ' . $email);
        return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
    }

    private function logSuspiciousActivity(Request $request, string $action, string $description): void
    {
        ActivityLog::create([
            'user_id' => null, // Suspicious usually means not logged in
            'action' => $action,
            'description' => $description,
            'ip_address' => $request->ip(),
            'browser' => $this->getBrowser($request->header('User-Agent')),
            'platform' => $this->getPlatform($request->header('User-Agent')),
        ]);
    }

    private function getBrowser($userAgent): string
    {
        if (strpos($userAgent, 'MSIE') !== false) return 'Internet Explorer';
        if (strpos($userAgent, 'Firefox') !== false) return 'Firefox';
        if (strpos($userAgent, 'Chrome') !== false) return 'Chrome';
        if (strpos($userAgent, 'Safari') !== false) return 'Safari';
        if (strpos($userAgent, 'Opera') !== false) return 'Opera';
        return 'Unknown';
    }

    private function getPlatform($userAgent): string
    {
        if (strpos($userAgent, 'Windows') !== false) return 'Windows';
        if (strpos($userAgent, 'Linux') !== false) return 'Linux';
        if (strpos($userAgent, 'Macintosh') !== false) return 'Mac';
        if (strpos($userAgent, 'iPhone') !== false) return 'iOS';
        if (strpos($userAgent, 'Android') !== false) return 'Android';
        return 'Unknown';
    }

    /**
     * Show the password reset form.
     */
    public function showResetForm(string $token): View
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Handle password reset attempt.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        // Pencegahan menggunakan password yang sama dengan yang lama
        $user = \App\Models\User::where('email', $request->email)->first();
        if ($user && Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Masukkan kata sandi yang lain! Anda memasukkan kata sandi lama Anda.']);
        }

        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Kata sandi Anda telah berhasil diperbarui!')
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
