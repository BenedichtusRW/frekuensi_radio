<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Balmon Lampung Monitoring Frekuensi</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo-balmon-lampung-transparent.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- ANTI-INSPECT & NAVIGATION PROTECTION (Pintu Depan) -->
    <script>
        // 0. FRAME-BUSTING (Mencegah website dibuka di Simulator/Iframe)
        if (window.self !== window.top) {
            window.top.location.href = window.self.location.href;
        }

        // 1. Back Button Buster
        (function() {
            window.history.pushState(null, null, window.location.href);
            window.onpopstate = function() {
                document.documentElement.innerHTML = ""; 
                window.history.go(1);
            };
        })();

        const redirect = () => {
            document.documentElement.innerHTML = ""; 
            window.location.replace("{{ url('/404-not-found') }}");
        };

        // 2. Matikan Klik Kanan & Shortcut
        document.addEventListener('contextmenu', e => { e.preventDefault(); redirect(); });
        document.onkeydown = function (e) {
            const forbidden = [
                e.keyCode == 123, // F12
                (e.ctrlKey && e.shiftKey && (e.keyCode == 73 || e.keyCode == 74)), // I/J
                (e.ctrlKey && e.keyCode == 85) // Ctrl+U
            ];
            if (forbidden.some(c => c)) { e.preventDefault(); redirect(); return false; }
        };

        // 3. Sensor Jebakan
        (function () {
            const checkSize = () => {
                const threshold = 160;
                if (window.outerWidth - window.innerWidth > threshold || window.outerHeight - window.innerHeight > threshold) {
                    redirect();
                }
            };

            const devtools = new Image();
            Object.defineProperty(devtools, 'id', { get: function () { redirect(); } });

            const checkDebugger = () => {
                const start = performance.now();
                debugger;
                const end = performance.now();
                if (end - start > 100) { redirect(); }
            };

            setInterval(() => {
                checkSize();
                checkDebugger();
            }, 1500); // Dioptimasi: Biar 2FA nggak lemot
        })();
    </script>

    <style>
        :root {
            --balmon-navy: #0f172a;
            --balmon-blue: #2563eb;
            --balmon-sky: #38bdf8;
            --balmon-slate: #64748b;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            background-image:
                radial-gradient(at 0% 0%, rgba(37, 99, 235, 0.05) 0, transparent 50%),
                radial-gradient(at 100% 100%, rgba(56, 189, 248, 0.03) 0, transparent 50%);
            margin: 0;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .login-wrapper {
            min-height: 100vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 2rem 1.5rem;
            z-index: 5;
        }

        .login-card {
            background: #fff;
            border-radius: 2rem;
            padding: 3rem;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f5f9;
            position: relative;
            z-index: 10;
            overflow: hidden;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .btn-login:active {
            transform: scale(0.98);
        }

        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(to right, var(--balmon-blue), var(--balmon-sky));
        }

        .brand-logo {
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        .brand-logo img {
            max-width: 100%;
            height: auto;
            object-fit: contain;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--balmon-navy);
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: var(--balmon-slate);
            font-size: 0.875rem;
            font-weight: 500;
        }

        .form-label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--balmon-slate);
            margin-bottom: 0.5rem;
            margin-left: 0.25rem;
        }

        .input-group-modern {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .input-group-modern .icon {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--balmon-slate);
            z-index: 10;
            pointer-events: none;
        }

        .input-group-modern .form-control {
            padding: 0.875rem 3.25rem !important;
            border-radius: 1rem;
            border: 1px solid #e2e8f0;
            font-weight: 600;
            font-size: 0.95rem !important;
            color: var(--balmon-navy);
            transition: all 0.3s ease;
        }

        .input-group-modern .form-control:focus {
            border-color: var(--balmon-blue);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.08);
            outline: none;
        }

        .toggle-password {
            position: absolute;
            right: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--balmon-slate);
            cursor: pointer;
            padding: 0;
            z-index: 11;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s;
        }

        .toggle-password:hover {
            color: var(--balmon-blue);
        }

        .btn-login {
            background: linear-gradient(135deg, var(--balmon-blue), #1d4ed8);
            border: none;
            border-radius: 1rem;
            padding: 1rem;
            color: white;
            font-weight: 700;
            font-size: 1rem;
            width: 100%;
            margin-top: 1rem;
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(37, 99, 235, 0.4);
            filter: brightness(1.1);
        }

        .error-alert {
            background-color: #fef2f2;
            border: 1px solid #fee2e2;
            color: #b91c1c;
            padding: 1rem;
            border-radius: 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .otp-input-container {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .otp-field {
            width: 46px;
            height: 56px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 800;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            color: var(--balmon-navy);
            transition: all 0.2s;
        }

        .otp-field:focus {
            border-color: var(--balmon-blue);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.08);
            outline: none;
        }

        .decoration-circle {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--balmon-blue), var(--balmon-sky));
            filter: blur(80px);
            opacity: 0.2;
            z-index: 1;
        }

        .circle-1 {
            width: 400px;
            height: 400px;
            top: -100px;
            left: -100px;
        }

        .circle-2 {
            width: 300px;
            height: 300px;
            bottom: -50px;
            right: -50px;
        }

        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.75rem;
            color: var(--balmon-slate);
        }

        .shield-icon-wrap {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        .shield-icon-wrap svg {
            width: 32px;
            height: 32px;
            color: var(--balmon-blue);
        }

        /* MOBILE SHIELD CSS */
        #mobile-shield {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #ffffff;
            z-index: 999999;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
        }

        #mobile-shield .shield-icon {
            width: 120px;
            height: 120px;
            background: #f8fafc;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            color: var(--balmon-blue);
        }

        #mobile-shield h2 {
            font-weight: 800;
            color: var(--balmon-navy);
            margin-bottom: 0.5rem;
            font-size: 1.5rem;
        }

        #mobile-shield p {
            color: var(--balmon-slate);
            max-width: 300px;
            line-height: 1.6;
            font-size: 0.95rem;
        }

        @media (max-width: 1024px) {
            #mobile-shield { display: flex; }
            .login-wrapper { display: none !important; }
        }
    </style>
</head>

<body>
    {{-- Mobile Shield Overlay --}}
    <div id="mobile-shield">
        <div class="shield-icon">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                <line x1="8" y1="21" x2="16" y2="21"></line>
                <line x1="12" y1="17" x2="12" y2="21"></line>
            </svg>
        </div>
        <h2>Desktop Only Access</h2>
        <p>Silahkan anda akses website ini melalui device laptop atau komputer untuk pengalaman terbaik.</p>
        <div style="margin-top: 2rem; font-size: 0.7rem; color: #cbd5e1; letter-spacing: 1px; font-weight: 700;">
            BALMON LAMPUNG PORTAL
        </div>
    </div>
    <div class="login-wrapper">
        <div class="decoration-circle circle-1"></div>
        <div class="decoration-circle circle-2"></div>

        <main class="login-card">
            <div class="login-header">
                <div class="brand-logo">
                    <img src="{{ asset('images/logo-balmon-lampung-transparent.png') }}" alt="Logo Balmon">
                </div>
                <h1>Portal Monitoring</h1>
                <p id="login-subtitle">Silakan masuk ke sistem</p>
            </div>

            @if ($errors->any())
                <div class="error-alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="login-form" autocomplete="off">
                @csrf
                {{-- Decoy inputs to trick Chrome Autofill --}}
                <input type="text" name="chrome_autofill_decoy_user"
                    style="position: absolute; top: -9999px; left: -9999px;">
                <input type="password" name="chrome_autofill_decoy_pass"
                    style="position: absolute; top: -9999px; left: -9999px;">

                {{-- ===== 2FA ONLY (PASSWORDLESS / STEP 2) ===== --}}
                <div id="view-2fa" style="display: {{ $loginPreference === '2fa' ? 'block' : 'none' }}">
                    <div class="text-center mb-4">
                        <div class="shield-icon-wrap">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                <path d="m9 12 2 2 4-4" />
                            </svg>
                        </div>
                        <p style="color:var(--balmon-slate); font-size:0.85rem;">
                            Masukkan 6-digit kode dari <b>Google Authenticator</b> Anda untuk masuk instan.
                        </p>
                    </div>

                    <div class="otp-input-container">
                        <input type="tel" maxlength="1" class="otp-field" data-otp="1" autocomplete="one-time-code"
                            inputmode="numeric" value="">
                        <input type="tel" maxlength="1" class="otp-field" data-otp="2" autocomplete="off"
                            inputmode="numeric" value="">
                        <input type="tel" maxlength="1" class="otp-field" data-otp="3" autocomplete="off"
                            inputmode="numeric" value="">
                        <input type="tel" maxlength="1" class="otp-field" data-otp="4" autocomplete="off"
                            inputmode="numeric" value="">
                        <input type="tel" maxlength="1" class="otp-field" data-otp="5" autocomplete="off"
                            inputmode="numeric" value="">
                        <input type="tel" maxlength="1" class="otp-field" data-otp="6" autocomplete="off"
                            inputmode="numeric" value="">
                    </div>

                    <input type="hidden" name="two_factor_code" id="two_factor_code">

                    <button type="submit" class="btn btn-login mb-3">
                        Verifikasi & Masuk
                    </button>

                    <div class="text-center">
                        <a href="javascript:void(0)" id="back-to-login-link" onclick="backToLogin()"
                            class="text-decoration-none"
                            style="display: none; font-size: 0.85rem; color: var(--balmon-slate); font-weight: 600;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" style="margin-right: 4px;">
                                <path d="m15 18-6-6 6-6" />
                            </svg>
                            Kembali ke Login
                        </a>
                    </div>
                </div>


                {{-- ===== EMAIL + PASSWORD ===== --}}
                <div id="view-email" style="display: {{ $loginPreference === '2fa' ? 'none' : 'block' }}">
                    <div class="form-group mb-3">
                        <label class="form-label">Email Petugas</label>
                        <div class="input-group-modern">
                            <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect width="20" height="16" x="2" y="4" rx="2" />
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                            </svg>
                            <input type="email" name="email" id="email_input" class="form-control"
                                placeholder="Masukkan Email Petugas" value="{{ old('email', $lastEmail ?? '') }}" autofocus>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Kata Sandi</label>
                        <div class="input-group-modern">
                            <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                            <input type="password" name="password" id="password" class="form-control"
                                placeholder="••••••••">
                            <button type="button" class="toggle-password" onclick="togglePassword('password', this)">
                                <svg class="eye-open" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                <svg class="eye-closed d-none" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24" />
                                    <path
                                        d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68" />
                                    <path d="M6.61 6.61A13.52 13.52 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61" />
                                    <line x1="2" y1="2" x2="22" y2="22" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-4 px-1">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember"
                                style="font-size: 0.8rem; font-weight: 600; color: var(--balmon-slate); cursor: pointer;">
                                Ingat Saya
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-login mb-3">
                        Masuk ke Sistem
                    </button>
                </div>

                {{-- Hidden 2FA Identity (Unique name to avoid conflict with step 1) --}}
                <input type="hidden" name="email_step2" id="email_2fa_hidden" value="{{ session('email_2fa') ?? $lastEmail ?? '' }}">

            </form>

            <div class="login-footer">
                &copy; {{ date('Y') }} KOMDIGI - Balmon Lampung
            </div>
        </main>
    </div>

    <script>
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const eyeOpen = button.querySelector('.eye-open');
            const eyeClosed = button.querySelector('.eye-closed');

            if (input.type === 'password') {
                input.type = 'text';
                eyeOpen.classList.add('d-none');
                eyeClosed.classList.remove('d-none');
            } else {
                input.type = 'password';
                eyeOpen.classList.remove('d-none');
                eyeClosed.classList.add('d-none');
            }
        }
    </script>

    <script>
        // Set focus to the first empty OTP field or email on load
        document.addEventListener('DOMContentLoaded', function () {
            @if($loginPreference === '2fa')
                document.getElementById('login-subtitle').innerText = 'Verifikasi Keamanan 2FA';
                document.querySelector('.otp-field').focus();
            @else
                document.getElementById('login-subtitle').innerText = 'Silakan masuk ke sistem';
                document.getElementById('email_input').focus();
            @endif
            // Clear OTP fields on load
            document.querySelectorAll('.otp-field').forEach(f => f.value = '');
        });

        function backToLogin() {
            document.getElementById('view-2fa').style.display = 'none';
            document.getElementById('view-email').style.display = 'block';
            document.getElementById('login-subtitle').innerText = 'Silakan masuk ke sistem';
            document.getElementById('email_input').focus();
        }
    </script>

    <script>
        // OTP field logic — no external libraries needed
        const fields = document.querySelectorAll('.otp-field');
        const hiddenCode = document.getElementById('two_factor_code');

        fields.forEach((field, i) => {
            field.addEventListener('input', e => {
                const v = e.target.value.replace(/\D/g, '');
                e.target.value = v;
                if (v && i < fields.length - 1) fields[i + 1].focus();
                syncCode();
            });
            field.addEventListener('keydown', e => {
                if (e.key === 'Backspace' && !e.target.value && i > 0) {
                    fields[i - 1].focus();
                }
            });
            // Handle paste
            field.addEventListener('paste', e => {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
                for (let j = 0; j < Math.min(paste.length, fields.length - i); j++) {
                    fields[i + j].value = paste[j];
                }
                const last = Math.min(i + paste.length, fields.length) - 1;
                fields[last].focus();
                syncCode();
            });
        });

        function syncCode() {
            let code = '';
            fields.forEach(f => code += f.value);
            hiddenCode.value = code;
            // Auto-submit on 6 digits
            if (code.length === 6) {
                handleLoginSubmit(new Event('submit'));
            }
        }

        async function handleLoginSubmit(e) {
            if (e) e.preventDefault();

            const form = document.getElementById('login-form');
            
            // Sync email if step 2 is active but email_2fa_hidden is empty
            const hEmail = document.getElementById('email_2fa_hidden');
            const iEmail = document.getElementById('email_input');
            if (hEmail && !hEmail.value && iEmail && iEmail.value) {
                hEmail.value = iEmail.value;
            }

            const formData = new FormData(form);
            const btn = document.querySelector('.btn-login');
            const originalBtnText = btn ? btn.innerHTML : '';

            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm text-light me-2" role="status" aria-hidden="true"></span>Memproses...';
            }

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    if (data.step2) {
                        // Switch to 2FA Step 2 (AJAX Style)
                        document.getElementById('view-email').style.display = 'none';
                        document.getElementById('view-2fa').style.display = 'block';
                        document.getElementById('login-subtitle').innerText = 'Verifikasi Keamanan 2FA';
                        document.getElementById('email_2fa_hidden').value = data.email;
                        document.getElementById('back-to-login-link').style.display = 'inline-block';
                        setTimeout(() => document.querySelector('.otp-field').focus(), 100);
                        if (btn) {
                            btn.disabled = false;
                            btn.innerHTML = 'Verifikasi & Masuk';
                        }
                    } else if (data.redirect) {
                        // Success Redirect instantly (Must be a hard refresh to load Dashboard CSS/JS)
                        window.location.href = data.redirect;
                    }
                } else {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = originalBtnText;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Masuk',
                        text: data.message || 'Email atau password salah.',
                        confirmButtonColor: '#2563eb'
                    });
                    // Reset OTP fields if failed on 2FA
                    if (document.getElementById('view-2fa') && document.getElementById('view-2fa').style.display !== 'none') {
                        fields.forEach(f => f.value = '');
                        hiddenCode.value = '';
                        fields[0].focus();
                    }
                }
            } catch (error) {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalBtnText;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Sistem',
                    text: 'Terjadi kesalahan saat menghubungi server.',
                    confirmButtonColor: '#2563eb'
                });
            }
        }

        document.getElementById('login-form').onsubmit = handleLoginSubmit;
    </script>
</body>

</html>