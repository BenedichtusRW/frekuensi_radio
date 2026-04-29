<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;

use App\Traits\LogsActivity;

class AuthController extends Controller
{
    use LogsActivity;
    /**
     * Show the login form.
     */
    public function showLogin(Request $request): View | RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->intended(route('dashboard'));
        }

        // Cek apakah ada SEMBARANG user yang punya 2FA aktif di database
        $any2fa = \App\Models\User::where('two_factor_enabled', true)
            ->whereNotNull('google2fa_secret')
            ->where('is_active', true)
            ->exists();

        // Jika tidak ada sama sekali user yang pakai 2FA, paksa kembali ke mode email (meskipun ada cookie lama)
        if (!$any2fa) {
            $loginPreference = 'email';
        } else {
            // Cek cookie preferensi login dari browser ini
            $loginPreference = $request->cookie('login_preference', 'email');
            
            // Jika sedang dalam proses 2-step (browser baru)
            if (session('show_2fa_step2')) {
                $loginPreference = '2fa';
            }
        }

        return view('auth.login', ['loginPreference' => $loginPreference]);
    }

    /**
     * Handle authentication attempt.
     */
    public function authenticate(Request $request)
    {
        Log::info('Login attempt started', ['email' => $request->email]);
        $throttleKey = 'login|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors(['email' => 'Terlalu banyak percobaan. Coba lagi dalam ' . $seconds . ' detik.']);
        }

        // ==========================================
        // JALUR 1: BROWSER SUDAH INGAT 2FA (Passwordless) ATAU STEP 2 DARI BROWSER BARU
        // ==========================================
        if ($request->filled('two_factor_code')) {
            $request->validate(['two_factor_code' => 'required|digits:6']);
            $google2fa = new \PragmaRX\Google2FA\Google2FA();
            
            $usersWith2fa = \App\Models\User::where('two_factor_enabled', true)
                ->whereNotNull('google2fa_secret')
                ->where('is_active', true)
                ->get();

            foreach ($usersWith2fa as $user) {
                if ($google2fa->verifyKey($user->google2fa_secret, $request->two_factor_code)) {
                    Auth::login($user, $request->boolean('remember', session('remember_me', false)));
                    RateLimiter::clear($throttleKey);
                    $request->session()->regenerate();
                    
                    // Tanamkan cookie bahwa browser ini milik user 2FA
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => true,
                            'redirect' => route('dashboard'),
                            'message' => 'Autentikasi berhasil!'
                        ])->withCookie(cookie()->forever('login_preference', '2fa'));
                    }
                    $this->logActivity($request, 'login_success', 'Login berhasil melalui verifikasi 2FA.');
                    return redirect()->intended(route('dashboard'))->withCookie(cookie()->forever('login_preference', '2fa'));
                }
            }

            RateLimiter::hit($throttleKey, 60);
            $this->logActivity($request, 'failed_2fa', 'Percobaan login 2FA gagal (kode salah).');

            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Kode autentikasi salah.'], 422);
            }

            return back()->withErrors(['two_factor_code' => 'Kode autentikasi salah.'])->with('show_2fa_step2', true);
        }

        // ==========================================
        // JALUR 2: BROWSER BELUM KENAL / TIDAK PAKAI 2FA
        // ==========================================
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $email = $request->input('email');
        $user = \App\Models\User::where('email', $email)->first();

        if (!$user) {
            RateLimiter::hit($throttleKey, 60);
            return back()->withErrors(['email' => 'Akun tidak ditemukan.'])->onlyInput('email');
        }

        if (!$user->is_active) {
            $this->logActivity($request, 'suspicious_access', 'Percobaan login ke akun nonaktif: ' . $email);
            return back()->withErrors(['email' => 'Akun dinonaktifkan.']);
        }

        // Cek Password
        if (!Auth::validate(['email' => $email, 'password' => $request->password])) {
            RateLimiter::hit($throttleKey, 60);
            $this->logActivity($request, 'failed_login', 'Gagal login (password salah) untuk email: ' . $email);
            
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Email atau password salah.'], 422);
            }

            return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
        }

        // Jika password benar, cek apakah dia punya 2FA
        if ($user->two_factor_enabled && !empty($user->google2fa_secret)) {
            // Berarti ini browser baru! Arahkan ke form 2FA (Step 2)
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'step2' => true,
                    'email' => $email,
                    'message' => 'Silakan masukkan kode 2FA.'
                ]);
            }

            return back()->with([
                'show_2fa_step2' => true,
                'email_2fa' => $email,
                'remember_me' => $request->boolean('remember')
            ])->withInput();
        }

        // Jika tidak punya 2FA, langsung login
        Auth::login($user, $request->boolean('remember'));
        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();
        
        $this->logActivity($request, 'login_success', 'Login berhasil (tanpa 2FA).');

        // Tanamkan cookie bahwa browser ini milik user Tanpa 2FA
        $response = redirect()->intended(route('dashboard'))->withCookie(cookie()->forever('login_preference', 'email'));

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'redirect' => route('dashboard'),
                'message' => 'Login berhasil! Mengalihkan...'
            ])->withCookie(cookie()->forever('login_preference', 'email'));
        }

        return $response;
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
            'password' => [
                'required',
                'confirmed',
                \Illuminate\Validation\Rules\Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->uncompromised(),
            ],
        ], [
            'password.uncompromised' => 'Kata sandi ini terdeteksi pernah bocor di internet. Silakan gunakan kata sandi lain demi keamanan Anda.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal harus 8 karakter.',
            'password.mixed' => 'Kata sandi harus mengandung campuran huruf besar dan kecil.',
            'password.numbers' => 'Kata sandi harus mengandung angka.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        // Pencegahan menggunakan password yang sama dengan yang lama
        $user = \App\Models\User::where('email', $request->email)->first();
        if ($user && Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Masukkan kata sandi yang berbeda. Anda sedang menggunakan kata sandi yang sudah pernah digunakan sebelumnya.']);
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
    public function logout(Request $request)
    {
        if (Auth::check()) {
            $this->logActivity($request, 'logout', 'Pengguna keluar dari sistem.');
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Extra security: Clear all custom session data
        $request->session()->flush();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'redirect' => route('login'),
                'message' => 'Logout berhasil. Sampai jumpa!'
            ]);
        }

        return redirect('/login');
    }
}
