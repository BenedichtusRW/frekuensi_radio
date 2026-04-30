@extends('layouts.app')

@section('title', 'Pengaturan - Balmon Lampung')
@section('page_title', 'Pengaturan')

@section('content')
    <style>
        .settings-hub {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            padding-top: 1rem;
        }

        .settings-card {
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid #e2e8f0;
            border-radius: 1.5rem;
            background: #ffffff;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            padding: 1.75rem;
            height: 200px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .settings-card:hover {
            transform: translateY(-8px);
            border-color: #3b82f6;
            box-shadow: 0 20px 25px -5px rgba(59, 130, 246, 0.08), 0 10px 10px -5px rgba(59, 130, 246, 0.04) !important;
        }

        .settings-card .icon-box {
            width: 52px;
            height: 52px;
            border-radius: 1.15rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
            background: #f8fafc;
            color: #64748b;
        }

        .settings-card h3 {
            font-size: 1.05rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .settings-card p {
            font-size: 0.8rem;
            color: #64748b;
            line-height: 1.5;
            margin-bottom: 0;
        }

        .settings-card .status-badge {
            position: absolute;
            top: 1.25rem;
            right: 1.25rem;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            padding: 0.25rem 0.6rem;
            border-radius: 2rem;
            background: #dcfce7;
            color: #166534;
        }

        .view-container {
            display: none;
            animation: fadeIn 0.4s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #64748b;
            font-weight: 600;
            font-size: 0.85rem;
            text-decoration: none;
            margin-bottom: 1.5rem;
            transition: all 0.2s ease;
            padding: 0.5rem 0.75rem;
            border-radius: 0.75rem;
        }

        .back-btn:hover {
            background: #f1f5f9;
            color: #1e293b;
            transform: translateX(-3px);
        }

        .fw-800 {
            font-weight: 800;
        }

        .text-navy {
            color: #0f172a;
        }

        .password-toggle {
            position: absolute;
            right: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            z-index: 10;
            transition: color 0.2s;
        }

        .password-toggle:hover {
            color: #475569;
        }

        .form-control.with-toggle {
            padding-right: 3.5rem;
        }

        #qr-code-placeholder svg {
            width: 140px !important;
            height: 140px !important;
            display: block;
            margin: 0 auto;
        }

        /* Skeleton Loader Styles */
        .skeleton {
            background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite linear;
            border-radius: 0.5rem;
        }

        @keyframes shimmer {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }
    </style>

    <div class="container-fluid p-0">
        <!-- SETTINGS HUB -->
        <div id="settings-hub" style="display: none;">
            <div class="settings-hub">
                <div class="settings-card" onclick="switchSettingsView('security')">
                    <div class="icon-box"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg></div>
                    <h3>Keamanan Akun</h3>
                    <p>Ganti kata sandi dan atur autentikasi dua faktor (2FA).</p>
                </div>

                @if(auth()->user()->role === 'super_admin')
                    <div class="settings-card" onclick="switchSettingsView('logs')">
                        <div class="icon-box"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                            </svg></div>
                        <h3>Log Aktivitas</h3>
                        <p>Pantau riwayat audit dan login sistem.</p>
                    </div>

                    <div class="settings-card" onclick="switchSettingsView('threats')">
                        <div class="icon-box" style="color: #dc2626;"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                <path d="m12 8 4 4"></path>
                                <path d="m16 8-4 4"></path>
                            </svg></div>
                        <h3>Aktivitas Mencurigakan</h3>
                        <p>Deteksi percobaan brute force atau akses ilegal.</p>
                    </div>

                    <div class="settings-card" onclick="switchSettingsView('users')">
                        <div class="icon-box"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg></div>
                        <h3>Kelola User</h3>
                        <p>Tambah anggota tim dan atur peran akun.</p>
                    </div>

                    <div class="settings-card" onclick="switchSettingsView('masterdata')">
                        <div class="icon-box" style="color: #0ea5e9;"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 22h14a2 2 0 0 0 2-2V7.5L14.5 2H6a2 2 0 0 0-2 2v4"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <path d="M2 15h10"></path>
                                <path d="m9 18 3-3-3-3"></path>
                            </svg></div>
                        <h3>Master Data</h3>
                        <p>Kelola daftar dropdown (Stasiun, Negara, dll).</p>
                    </div>

                @endif
            </div>
        </div>



        <!-- DETAIL VIEW: KEAMANAN -->
        <div id="view-security" class="view-container">
            <a href="javascript:void(0)" onclick="showSettingsHub()" class="back-btn"><svg width="18" height="18"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                    stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg> Kembali</a>
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    {{-- 2FA CARD --}}
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-body p-4" id="2fa-card-body">
                            <h6 class="fw-bold text-navy mb-2">Keamanan Dua Langkah (2FA)</h6>
                            <p class="text-slate-500 mb-4" style="font-size: 0.85rem; line-height: 1.6;">Lindungi akun Anda
                                dengan verifikasi tambahan melalui aplikasi authenticator.</p>

                            <div id="2fa-content-area">
                                @php
                                    $hasSecret = !empty(auth()->user()->google2fa_secret);
                                    $isEnabled = (bool) auth()->user()->two_factor_enabled;
                                @endphp
                                @if(!$hasSecret)
                                    <button class="btn btn-blue w-100 rounded-3 fw-bold py-2 shadow-sm"
                                        onclick="start2faSetup()">Aktifkan 2FA</button>
                                @else
                                    <div
                                        class="p-3 rounded-3 border bg-light d-flex align-items-center justify-content-between mb-3">
                                        <div class="d-flex align-items-center">
                                            <div id="2fa-status-icon"
                                                class="{{ $isEnabled ? 'bg-success' : 'bg-secondary' }} bg-opacity-10 p-2 rounded-2 me-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                    viewBox="0 0 24 24" fill="none"
                                                    stroke="{{ $isEnabled ? '#198754' : '#6c757d' }}" stroke-width="2.5"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                            </div>
                                            <span id="2fa-status-text"
                                                class="fw-bold small {{ $isEnabled ? 'text-success' : 'text-secondary' }}">{{ $isEnabled ? 'Keamanan Aktif' : 'Keamanan Nonaktif' }}</span>
                                        </div>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" id="2faSwitch" {{ $isEnabled ? 'checked' : '' }} onchange="toggle2fa(this.checked)">
                                        </div>
                                    </div>
                                    <button class="btn btn-outline-secondary btn-sm w-100 rounded-3 py-2"
                                        onclick="showReset2faModal()">Reset Authenticator</button>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- DANGER ZONE CARD (Pindah ke kiri) --}}
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-danger bg-opacity-10 p-2 rounded-2 me-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                        fill="none" stroke="#dc3545" stroke-width="2.5" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path
                                            d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z">
                                        </path>
                                        <line x1="12" y1="9" x2="12" y2="13"></line>
                                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                                    </svg>
                                </div>
                                <h6 class="text-danger fw-bold m-0" style="font-size: 0.9rem;">Hapus Akun Permanen</h6>
                            </div>
                            <p class="text-slate-500 mb-4" style="font-size: 0.75rem; line-height: 1.5;">Tindakan ini tidak
                                dapat dibatalkan. Semua data dan akses Anda akan dihapus selamanya.</p>
                            <button type="button" class="btn btn-outline-danger w-100 rounded-3 py-2 fw-bold"
                                onclick="showDeleteAccountModal()">Hapus Akun Saya</button>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-navy mb-4">Pengaturan Akses</h6>
                            <form onsubmit="updateSecurity(event)" novalidate>
                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-slate-700">Nama Lengkap <span
                                            class="text-danger">* <small id="err-name" class="d-none fw-normal">(Wajib
                                                diisi)</small></span></label>
                                    <input type="text" name="name" id="sec-name"
                                        class="form-control rounded-3 py-2 px-3 border bg-light bg-opacity-25"
                                        value="{{ auth()->user()->name }}" required
                                        style="font-size: 0.9rem; border-color: #dee2e6;">
                                </div>

                                @if(auth()->user()->role === 'super_admin')
                                    <div class="mb-4">
                                        <label class="form-label small fw-bold text-slate-700">Email Utama <span
                                                class="text-danger">* <small id="err-email" class="d-none fw-normal">(Wajib
                                                    diisi)</small></span></label>
                                        <input type="email" name="email" id="sec-email"
                                            class="form-control rounded-3 py-2 px-3 border bg-light bg-opacity-25"
                                            value="{{ auth()->user()->email }}" required
                                            style="font-size: 0.9rem; border-color: #dee2e6;">
                                        <div class="small text-slate-400 mt-1" style="font-size: 0.7rem;">Email resmi Balmon
                                            Lampung.</div>
                                    </div>
                                @else
                                    <input type="hidden" name="email" value="{{ auth()->user()->email }}">
                                @endif

                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-slate-700">Kata Sandi Saat Ini <span
                                            class="text-danger">* <small id="err-password" class="d-none fw-normal">(Wajib
                                                diisi)</small></span></label>
                                    <div class="input-group">
                                        <input type="password" name="current_password" id="sec-password"
                                            class="form-control rounded-start-3 py-2 px-3 border border-end-0 bg-light bg-opacity-25"
                                            required placeholder="Masukkan sandi lama"
                                            style="font-size: 0.9rem; border-color: #dee2e6;" autocomplete="new-password">
                                        <button type="button"
                                            class="btn btn-white border border-start-0 text-slate-400 px-3"
                                            onclick="togglePassword(this)" style="border-color: #dee2e6;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" class="eye-icon">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-slate-700">Kata Sandi Baru</label>
                                        <div class="input-group">
                                            <input type="password" name="password"
                                                class="form-control rounded-start-3 py-2 px-3 border border-end-0 bg-light bg-opacity-25"
                                                placeholder="Min. 8 karakter"
                                                style="font-size: 0.9rem; border-color: #dee2e6;">
                                            <button type="button"
                                                class="btn btn-white border border-start-0 text-slate-400 px-3"
                                                onclick="togglePassword(this)" style="border-color: #dee2e6;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round" class="eye-icon">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-slate-700">Konfirmasi Sandi</label>
                                        <div class="input-group">
                                            <input type="password" name="password_confirmation"
                                                class="form-control rounded-start-3 py-2 px-3 border border-end-0 bg-light bg-opacity-25"
                                                placeholder="Ulangi sandi baru"
                                                style="font-size: 0.9rem; border-color: #dee2e6;">
                                            <button type="button"
                                                class="btn btn-white border border-start-0 text-slate-400 px-3"
                                                onclick="togglePassword(this)" style="border-color: #dee2e6;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round" class="eye-icon">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="text-slate-500" style="font-size: 0.75rem;">Min. 8 karakter, huruf besar
                                            & angka. (Cth: BalmonLampung24)</div>
                                        <div class="text-slate-400 italic mt-1" style="font-size: 0.75rem;">* Kosongkan jika
                                            tidak ingin mengubah kata sandi.</div>
                                    </div>
                                </div>

                                <div class="mb-2 d-flex justify-content-between align-items-center pt-3 border-top">
                                    <button type="submit" id="btn-update-security"
                                        class="btn btn-blue rounded-3 fw-bold px-4 py-2 shadow-sm border-0">Simpan
                                        Perubahan</button>
                                    <div class="text-slate-400 small d-flex align-items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                            stroke-linejoin="round" class="me-2">
                                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                        </svg>
                                        Akses Terenkripsi
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(auth()->user()->role === 'super_admin')
            <!-- DETAIL VIEW: LOG -->
            <div id="view-logs" class="view-container">
                <a href="javascript:void(0)" onclick="showSettingsHub()" class="back-btn"><svg width="18" height="18"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                        stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg> Kembali</a>
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white fw-bold py-3">Log Aktivitas Sistem</div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.75rem;">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4">WAKTU & USER</th>
                                    <th>AKSI</th>
                                    <th>DESKRIPSI</th>
                                    <th class="px-4 text-end">IP ADDRESS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($activityLogs as $log)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center gap-3">
                                                @if($log->user)
                                                    @if($log->user->profile_photo)
                                                        <img src="{{ asset('storage/' . $log->user->profile_photo) }}" class="avatar-circle"
                                                            alt="Avatar"
                                                            onclick="viewFullAvatar(this.src, '{{ addslashes($log->user->name) }}')">
                                                    @else
                                                        <div class="avatar-placeholder">{{ strtoupper(substr($log->user->name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                @else
                                                    <div class="avatar-placeholder bg-secondary bg-opacity-10 text-secondary border-0">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                                            stroke-linecap="round" stroke-linejoin="round">
                                                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                                                            <line x1="8" y1="21" x2="16" y2="21"></line>
                                                            <line x1="12" y1="17" x2="12" y2="21"></line>
                                                        </svg></div>
                                                @endif

                                                <div class="flex-grow-1">
                                                    <div class="fw-bold text-navy d-flex align-items-center gap-2"
                                                        style="font-size: 0.8rem;">
                                                        {{ $log->user->name ?? 'Sistem' }}
                                                        @if($log->user)
                                                            @if($log->user->role === 'super_admin')
                                                                <span
                                                                    class="badge bg-primary bg-opacity-10 text-primary border-0 rounded-pill"
                                                                    style="font-size: 0.6rem; padding: 0.15rem 0.5rem;">Super Admin</span>
                                                            @else
                                                                <span
                                                                    class="badge bg-secondary bg-opacity-10 text-secondary border-0 rounded-pill"
                                                                    style="font-size: 0.6rem; padding: 0.15rem 0.5rem;">Admin Tim</span>
                                                            @endif
                                                        @endif
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2 text-slate-400"
                                                        style="font-size: 0.65rem;">
                                                        <span>{{ $log->user->email ?? 'Automated' }}</span>
                                                        <span>•</span>
                                                        <span>{{ $log->created_at->format('d/m/Y H:i') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span
                                                class="badge bg-light text-dark border">{{ strtoupper(str_replace('_', ' ', (string) $log->action)) }}</span>
                                        </td>
                                        <td>{{ $log->description }}</td>
                                        <td class="px-4 text-end text-slate-500 font-monospace" style="font-size: 0.7rem;">
                                            {{ $log->ip_address }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- DETAIL VIEW: THREATS -->
            <div id="view-threats" class="view-container">
                <a href="javascript:void(0)" onclick="showSettingsHub()" class="back-btn"><svg width="18" height="18"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                        stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg> Kembali</a>
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white fw-bold py-3 text-danger">Aktivitas Mencurigakan</div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.75rem;">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4">WAKTU</th>
                                    <th>IP ADDRESS</th>
                                    <th>JENIS ANCAMAN</th>
                                    <th class="px-4 text-end">STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $threats = $activityLogs->filter(fn($l) => in_array($l->action, ['failed_login', 'failed_2fa', 'brute_force_detected', 'suspicious_access'])); @endphp
                                @forelse ($threats as $t)
                                    <tr>
                                        <td class="px-4">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="fw-bold text-danger">{{ $t->ip_address }}</td>
                                        <td><span
                                                class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10">{{ strtoupper(str_replace('_', ' ', (string) $t->action)) }}</span>
                                        </td>
                                        <td class="px-4 text-end"><span class="text-success fw-bold">DIBLOKIR</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-slate-400">Tidak ada ancaman terdeteksi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- DETAIL VIEW: USERS -->
            <div id="view-users" class="view-container">
                <a href="javascript:void(0)" onclick="showSettingsHub()" class="back-btn"><svg width="18" height="18"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                        stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg> Kembali</a>
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center">
                        <span>Daftar Anggota Tim</span>
                        <button class="btn btn-sm btn-primary rounded-3" onclick="showUserModal()">+ Tambah</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.8rem;">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4">NAMA & EMAIL</th>
                                    <th>ROLE</th>
                                    <th class="text-center">STATUS</th>
                                    <th class="text-center">2FA</th>
                                    <th class="px-4 text-end">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(\App\Models\User::all() as $u)
                                    <tr id="user-row-{{ $u->id }}">
                                        <td class="px-4">
                                            <div class="d-flex align-items-center gap-3 py-2">
                                                @if($u->role !== 'super_admin')
                                                    @if($u->profile_photo)
                                                        <img src="{{ asset('storage/' . $u->profile_photo) }}" class="avatar-circle"
                                                            alt="Avatar" onclick="viewFullAvatar(this.src, '{{ addslashes($u->name) }}')">
                                                    @else
                                                        <div class="avatar-placeholder">{{ strtoupper(substr($u->name, 0, 1)) }}</div>
                                                    @endif
                                                @endif
                                                <div>
                                                    <div class="fw-bold text-navy">
                                                        {{ $u->name }}
                                                        @if($u->id === auth()->id())
                                                            <span class="badge bg-blue-100 text-blue-600 fw-normal ms-1"
                                                                style="font-size: 0.65rem; vertical-align: middle;">(Anda)</span>
                                                        @endif
                                                    </div>
                                                    <div class="text-slate-400 small">{{ $u->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge rounded-pill px-3"
                                                style="background: {{ $u->role === 'super_admin' ? '#eff6ff' : '#f8fafc' }}; color: {{ $u->role === 'super_admin' ? '#2563eb' : '#64748b' }};">{{ $u->role === 'super_admin' ? 'Super Admin' : 'Admin Tim' }}</span>
                                        </td>
                                        <td class="text-center" id="user-status-container-{{ $u->id }}">
                                            @if($u->is_active)
                                                <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill"
                                                    style="font-size: 0.65rem;">AKTIF</span>
                                            @else
                                                <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1 rounded-pill"
                                                    style="font-size: 0.65rem;">NONAKTIF</span>
                                            @endif
                                        </td>
                                        <td class="text-center">@if($u->two_factor_enabled)<span
                                        class="text-success fw-bold">AKTIF</span>@else<span
                                                class="text-slate-300">OFF</span>@endif</td>
                                        <td class="px-4 text-end">
                                            <div class="d-flex gap-2 justify-content-end">
                                                {{-- Tombol Toggle Status (Hanya jika bukan diri sendiri dan bukan sesama Super
                                                Admin) --}}
                                                @if($u->id !== auth()->id() && $u->role !== 'super_admin')
                                                    <button id="btn-status-{{ $u->id }}"
                                                        class="btn btn-sm {{ $u->is_active ? 'btn-outline-warning' : 'btn-outline-success' }} rounded-3"
                                                        title="{{ $u->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}"
                                                        onclick="toggleUserStatus({{ $u->id }}, '{{ $u->name }}', {{ $u->is_active ? 'true' : 'false' }})">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path>
                                                            <line x1="12" y1="2" x2="12" y2="12"></line>
                                                        </svg>
                                                    </button>
                                                @endif

                                                {{-- Tombol Edit selalu ada untuk semua user --}}
                                                <button class="btn btn-sm btn-outline-secondary rounded-3"
                                                    onclick="showUserModal({{ $u->id }}, '{{ $u->name }}', '{{ $u->email }}', '{{ $u->role }}', {{ $u->two_factor_enabled ? 'true' : 'false' }})">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                    </svg>
                                                </button>

                                                {{-- Tombol Hapus hanya muncul jika BUKAN diri sendiri dan BUKAN sesama Super Admin
                                                --}}
                                                @if($u->id !== auth()->id() && $u->role !== 'super_admin')
                                                    <button class="btn btn-sm btn-outline-danger rounded-3"
                                                        onclick="deleteUser({{ $u->id }})">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round">
                                                            <polyline points="3 6 5 6 21 6"></polyline>
                                                            <path
                                                                d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                                            </path>
                                                            <line x1="10" y1="11" x2="10" y2="17"></line>
                                                            <line x1="14" y1="11" x2="14" y2="17"></line>
                                                        </svg>
                                                    </button>
                                                @elseif($u->id !== auth()->id() && $u->role === 'super_admin')
                                                    <span class="btn btn-sm btn-light border-0 disabled text-slate-300"
                                                        title="Super Admin Terproteksi">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round">
                                                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                                        </svg>
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- 2FA SETUP MODAL -->
        <div id="qr-modal" class="modal-premium-overlay">
            <div class="modal-premium-container">
                <div class="modal-premium-content">
                    <!-- STEP 1: BACKUP KEY (DITAMPILKAN DULU) -->
                    <div id="2fa-step-1">
                        <h5 class="fw-800 text-navy mb-3" style="font-size: 1.1rem;">Langkah 1: Simpan Kode Cadangan</h5>
                        <p class="text-slate-500 mb-4" style="font-size: 0.8rem;">Salin dan simpan kode ini di tempat aman.
                            Anda juga bisa menggunakan kode ini untuk <b>input manual</b> di aplikasi Authenticator jika
                            tidak bisa scan QR.</p>

                        <div class="mb-4 text-start bg-light p-3 rounded-4 border border-dashed">
                            <label class="small fw-bold text-slate-500 mb-1 d-block">Kode Rahasia (Backup Key):</label>
                            <div class="d-flex gap-2 align-items-center">
                                <code id="2fa-secret-text" class="text-navy fw-bold"
                                    style="font-size: 1rem; letter-spacing: 1px;">XXXX-XXXX-XXXX-XXXX</code>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button class="btn btn-dark rounded-3 fw-bold py-2 shadow-sm" id="btn-copy-continue"
                                onclick="copyAndContinue()">Salin & Lanjutkan</button>
                            <button class="btn btn-link text-slate-400 text-decoration-none small"
                                onclick="hideQrModal()">Batal</button>
                        </div>
                    </div>

                    <!-- STEP 2: QR CODE (DISEMBUNYIKAN SAMPAI KODE DISALIN) -->
                    <div id="2fa-step-2" style="display: none;">
                        <h5 class="fw-800 text-navy mb-4" style="font-size: 1.1rem;">Langkah 2: Scan QR Code</h5>
                        <div id="qr-code-placeholder" class="mb-4 p-3 bg-white d-inline-block rounded-4 border"></div>

                        <div class="mb-4">
                            <input type="text" id="setup-verification-code" class="form-control text-center fw-800"
                                placeholder="000000" maxlength="6" style="font-size: 1.5rem; border-radius: 1rem;"
                                autocomplete="one-time-code" onkeyup="if(event.key === 'Enter') verify2faSetup()">
                            <div class="text-slate-400 mt-2" style="font-size: 0.75rem;">Masukkan 6 digit kode dari aplikasi
                                Google Authenticator.</div>
                        </div>
                        <div class="d-grid gap-2">
                            <button class="btn btn-dark rounded-3 fw-bold py-2 shadow-sm" id="btn-verify-setup"
                                onclick="verify2faSetup()">Verifikasi & Aktifkan</button>
                            <button class="btn btn-link text-slate-400 text-decoration-none small"
                                onclick="hideQrModal()">Batal</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2FA RESET MODAL -->
        <div id="reset2fa-modal" class="modal-premium-overlay">
            <div class="modal-premium-container" style="max-width: 400px;">
                <div class="modal-premium-content text-center">
                    <div class="confirm-icon-circle mb-3 mx-auto"
                        style="width: 64px; height: 64px; border-radius: 50%; background: #fff1f2; display: flex; align-items: center; justify-content: center;">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#e11d48" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    </div>
                    <h5 class="fw-800 text-navy mb-2" style="font-size: 1.1rem;">Reset Authenticator</h5>
                    <div class="text-slate-500 mb-2" style="font-size: 0.85rem;">Silakan masukkan kata sandi Anda untuk
                        mereset 2FA.</div>
                    <div class="text-danger mb-4" style="font-size: 0.75rem; font-weight: 600;">Seluruh perangkat
                        authenticator akan diputus.</div>

                    <div class="position-relative mb-4 text-start">
                        <input type="password" id="reset2fa-password-input" class="form-control rounded-3 with-toggle py-2"
                            placeholder="Masukkan kata sandi Anda" style="font-size: 0.95rem;"
                            onkeyup="if(event.key === 'Enter') submitReset2fa()">
                        <button type="button" class="password-toggle" onclick="togglePassword(this)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="eye-icon">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn-premium-ok flex-grow-1" id="btn-submit-reset2fa" onclick="submitReset2fa()">Reset
                            Sekarang</button>
                        <button class="btn-premium-cancel flex-grow-1" onclick="hideReset2faModal()">Batal</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- USER MANAGEMENT MODAL -->
        <div id="user-modal" class="modal-premium-overlay">
            <div class="modal-premium-container" style="max-width: 420px;">
                <div class="modal-premium-content text-start">
                    <h5 class="fw-800 text-navy mb-4" id="user-modal-title" style="font-size: 1.1rem;">Tambah Anggota Tim
                    </h5>
                    <form id="user-form" onsubmit="saveUser(event)">
                        <input type="hidden" id="user-id">
                        <div class="mb-3" id="container-user-name">
                            <label class="form-label small fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" id="user-name" class="form-control rounded-3" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" id="user-email" class="form-control rounded-3" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Role / Peran <span class="text-danger">*</span></label>
                            <select id="user-role" class="form-select rounded-3" required
                                onchange="toggleNameField(this.value)">
                                <option value="admin">Admin Tim</option>
                                <option value="super_admin">Super Admin</option>
                            </select>
                        </div>
                        <div class="mb-4" id="container-user-password">
                            <label class="form-label small fw-bold">Kata Sandi <span id="password-asterisk"
                                    class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="password" id="user-password" class="form-control rounded-3 with-toggle"
                                    placeholder="Minimal 8 karakter">
                                <button type="button" class="password-toggle" onclick="togglePassword(this)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="eye-icon">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </button>
                            </div>
                            <div class="text-slate-500 mt-1" style="font-size: 0.7rem;">Min. 8 karakter, huruf besar &
                                angka. (Cth: BalmonLampung24)</div>
                            <div id="password-hint" class="small text-slate-400 mt-1" style="font-size: 0.7rem;">* Kosongkan
                                jika tidak ingin mengubah kata sandi.</div>
                        </div>

                        <!-- Tombol Reset 2FA (Hanya muncul jika user pakai 2FA) -->
                        <div id="reset-2fa-container" class="mb-4" style="display:none;">
                            <div class="p-3 rounded-4 bg-rose-50 border border-rose-100 text-center">
                                <p class="small text-slate-600 mb-3" style="font-size: 0.75rem;">Petugas ini sedang
                                    menggunakan Autentikasi 2 Faktor (2FA).</p>
                                <button type="button" id="btn-reset-2fa-user"
                                    class="btn btn-outline-danger btn-sm rounded-3 fw-bold px-4 w-100"
                                    onclick="resetUser2fa()">Matikan Paksa 2FA (Reset)</button>
                            </div>
                        </div>

                        <!-- Tombol Kirim Tautan Reset (Hanya muncul untuk sesama Super Admin) -->
                        <div id="reset-link-container" class="mb-4" style="display:none;">
                            <div class="p-3 rounded-4 bg-blue-50 border border-blue-100 text-center">
                                <p class="small text-slate-600 mb-3" style="font-size: 0.75rem;">Anda tidak dapat mengubah
                                    password Super Admin lain secara langsung. Kirimkan tautan reset ke email mereka.</p>
                                <button type="button" id="btn-send-reset"
                                    class="btn btn-blue btn-sm rounded-3 fw-bold px-4 w-100" onclick="sendResetLink()">Kirim
                                    Tautan Reset Ke Email</button>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-dark rounded-3 fw-bold py-2" id="btn-save-user">Simpan
                                Anggota</button>
                            <button type="button" class="btn btn-link text-slate-400 text-decoration-none small"
                                onclick="hideUserModal()">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- DETAIL VIEW: MASTER DATA -->
        <div id="view-masterdata" class="view-container">
            <a href="javascript:void(0)" onclick="showSettingsHub()" class="back-btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg> Kembali
            </a>
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div
                    class="card-header bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <div class="fw-bold text-navy">Master Data Dropdown</div>
                        <div class="text-slate-400" style="font-size: 0.7rem;">Atur isi dropdown untuk form Input Laporan
                            Harian.</div>
                    </div>
                    <div class="d-flex gap-3 align-items-center">
                        {{-- TOGGLE HYBRID (Hanya muncul jika kategori Kelas Emisi) --}}
                        <div id="hybrid-config-container" class="d-none bg-light px-3 py-1 rounded-3 border d-flex align-items-center gap-3" style="height: 38px; min-width: 230px;">
                            <div class="d-flex flex-column text-start" style="line-height: 1.1;">
                                <span class="fw-bold text-navy" style="font-size: 0.7rem;">Mode Input Manual</span>
                                <span class="text-slate-400" style="font-size: 0.6rem;">Izinkan petugas ketik bebas</span>
                            </div>
                            <div class="form-check form-switch m-0 p-0 d-flex align-items-center">
                                <input class="form-check-input cursor-pointer" type="checkbox" id="hybridEmisiToggle" 
                                    style="width: 32px; height: 16px; margin: 0; margin-left: 10px;" onchange="toggleHybridEmisi(this.checked)">
                            </div>
                        </div>

                        <select id="master-data-category" class="form-select form-select-sm rounded-3"
                            style="width: auto; min-width: 160px;" onchange="loadMasterData()">
                            <option value="kategori">Jenis Laporan</option>
                            <option value="kelas_stasiun">Kelas Stasiun</option>
                            <option value="stasiun_monitor">Stasiun Monitor</option>
                            <option value="kode_negara">Negara</option>
                            <option value="administrasi_termonitor">Administrasi Termonitor</option>
                            <option value="kelas_emisi">Kelas Emisi</option>
                        </select>
                        <button id="btn-add-master-data" class="btn btn-primary btn-sm rounded-3 px-3 d-flex align-items-center gap-2"
                            onclick="showMasterDataModal()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Tambah
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 custom-table" style="font-size: 0.8rem;">
                        <thead class="table-light text-slate-500">
                            <tr>
                                <th class="ps-4 py-2 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Nilai
                                    / Opsi</th>
                                <th class="py-2 text-center text-uppercase"
                                    style="font-size: 0.7rem; letter-spacing: 0.5px; width: 120px;">Status</th>
                                <th class="pe-4 py-2 text-end text-uppercase"
                                    style="font-size: 0.7rem; letter-spacing: 0.5px; width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="master-data-tbody">
                            <!-- Skeleton Loader (Will be replaced by JS) -->
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="skeleton" style="height: 18px; width: 70%;"></div>
                                </td>
                                <td class="text-center">
                                    <div class="skeleton mx-auto" style="height: 22px; width: 50px; border-radius: 20px;">
                                    </div>
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="skeleton ms-auto" style="height: 30px; width: 60px; border-radius: 8px;">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="skeleton" style="height: 18px; width: 50%;"></div>
                                </td>
                                <td class="text-center">
                                    <div class="skeleton mx-auto" style="height: 22px; width: 50px; border-radius: 20px;">
                                    </div>
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="skeleton ms-auto" style="height: 30px; width: 60px; border-radius: 8px;">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="skeleton" style="height: 18px; width: 60%;"></div>
                                </td>
                                <td class="text-center">
                                    <div class="skeleton mx-auto" style="height: 22px; width: 50px; border-radius: 20px;">
                                    </div>
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="skeleton ms-auto" style="height: 30px; width: 60px; border-radius: 8px;">
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Master Data Modal -->
        <div id="master-data-modal" class="modal-premium-overlay">
            <div class="modal-premium-container">
                <div class="modal-premium-content text-start">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold text-navy mb-0" id="master-data-modal-title" style="font-size: 1.1rem;">Tambah
                            Opsi Baru</h5>
                        <button type="button" class="btn-close" onclick="hideMasterDataModal()"></button>
                    </div>
                    <form id="master-data-form" onsubmit="saveMasterData(event)">
                        <input type="hidden" id="md-id">
                        <input type="hidden" id="md-category">

                        <div class="mb-4">
                            <label class="form-label small fw-bold">Nilai / Nama Opsi <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="md-value" class="form-control rounded-3" required
                                placeholder="Contoh: MSHF LAMPUNG" style="text-transform: uppercase;">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-dark rounded-3 fw-bold py-2" id="btn-save-md">Simpan
                                Opsi</button>
                            <button type="button" class="btn btn-link text-slate-400 text-decoration-none small"
                                onclick="hideMasterDataModal()">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Delete Account Modal -->
    <div id="delete-account-modal" class="modal-premium-overlay">
        <div class="modal-premium-container">
            <div class="modal-premium-content">
                <div class="confirm-icon-circle">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
                        stroke="#dc3545" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z">
                        </path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                </div>
                <h5 class="fw-bold text-navy mb-2" style="font-size: 1.1rem;">Hapus Akun Permanen?</h5>
                <p class="text-slate-500 mb-3" style="font-size: 0.825rem; line-height: 1.5;">Data akun, log aktivitas, dan
                    hak akses Anda akan dihapus permanen dari sistem Balmon Lampung.</p>

                <div class="bg-danger bg-opacity-10 p-3 rounded-4 mb-4 text-start">
                    <label class="form-label text-danger fw-bold small mb-1" style="font-size: 0.75rem;">Konfirmasi Kata
                        Sandi</label>
                    <div class="position-relative">
                        <input type="password" id="delete-confirm-password"
                            class="form-control bg-white border-0 rounded-3 shadow-sm py-2 px-3 with-toggle" required
                            placeholder="Masukkan sandi Anda" style="font-size: 0.85rem;">
                        <button type="button" class="password-toggle" onclick="togglePassword(this)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="eye-icon">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-danger rounded-3 py-2 fw-bold" style="font-size: 0.85rem;"
                        onclick="confirmDeleteSelf()">Ya, Hapus Akun</button>
                    <button type="button" class="btn btn-link text-slate-400 text-decoration-none small"
                        onclick="hideDeleteAccountModal()">Batalkan</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Fix SweetAlert scrollbar jump issue globally & Set Premium Defaults
        if (typeof Swal !== 'undefined') {
            Swal = Swal.mixin({
                scrollbarPadding: false,
                heightAuto: false,
                background: '#ffffff',
                color: '#0f172a',
                buttonsStyling: false,
                customClass: {
                    popup: 'swal-premium-popup shadow-lg',
                    title: 'swal-premium-title',
                    htmlContainer: 'swal-premium-html',
                    confirmButton: 'btn btn-dark swal-premium-confirm mx-1',
                    cancelButton: 'btn btn-link text-slate-400 text-decoration-none swal-premium-cancel mx-1',
                    input: 'form-control swal-premium-input',
                    icon: 'mb-4'
                }
            });
        }


        function switchSettingsView(viewName) {
            const hub = document.getElementById('settings-hub');
            const target = document.getElementById('view-' + viewName);

            if (target && hub) {
                // Sembunyikan hub dan semua view lainnya
                hub.style.display = 'none';
                document.querySelectorAll('.view-container').forEach(v => {
                    v.style.display = 'none';
                    v.classList.remove('active');
                });

                // Tampilkan target view
                target.style.display = 'block';
                target.classList.add('active');

                // Scroll ke paling atas
                window.scrollTo(0, 0);

                // Simpan ke memory agar saat refresh tidak hilang
                sessionStorage.setItem('active_settings_view', viewName);

                // Auto-load data jika menu master data
                if (viewName === 'masterdata') {
                    loadMasterData();
                }
            }
        }

        function showSettingsHub() {
            // Bersihkan memory saat kembali ke hub
            sessionStorage.removeItem('active_settings_view');

            document.querySelectorAll('.view-container').forEach(v => {
                v.style.display = 'none';
                v.classList.remove('active');
            });
            document.getElementById('settings-hub').style.display = 'grid';
            window.scrollTo(0, 0);
        }

        function initSettingsNavigation() {
            const savedView = sessionStorage.getItem('active_settings_view');
            // Cek apakah ada di memory DAN pastikan elemennya benar-benar ada di halaman (Proteksi RBAC)
            if (savedView && document.getElementById('view-' + savedView)) {
                switchSettingsView(savedView);
            } else {
                showSettingsHub();
            }
        }

        document.addEventListener('DOMContentLoaded', initSettingsNavigation);
        document.addEventListener('livewire:navigated', initSettingsNavigation);
        async function start2faSetup() {
            const { value: password } = await Swal.fire({
                title: 'Konfirmasi Kata Sandi',
                html: `
                        <div class="text-slate-500 mb-3" style="font-size: 0.85rem;">Silakan masukkan kata sandi Anda untuk mengaktifkan 2FA.</div>
                        <div class="position-relative">
                            <input type="password" id="swal-password" class="swal2-input m-0 w-100 rounded-3" placeholder="Masukkan kata sandi Anda" style="font-size: 0.95rem; height: 3.2rem;">
                            <button type="button" class="password-toggle" onclick="togglePassword(this)" style="right: 1.25rem; top: 50%; transform: translateY(-50%); border: none; background: transparent; padding: 0;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon" style="color: #94a3b8;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            </button>
                        </div>
                    `,
                showCancelButton: true,
                confirmButtonText: 'Lanjutkan',
                cancelButtonText: 'Batal',
                icon: 'info',
                iconColor: '#3b82f6',
                preConfirm: () => {
                    const pass = document.getElementById('swal-password').value;
                    if (!pass) {
                        Swal.showValidationMessage('Kata sandi wajib diisi');
                    }
                    return pass;
                }
            });

            if (!password) return;

            try {
                const response = await fetch('{{ route("2fa.generate") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ password: password })
                });

                const data = await response.json();

                if (response.ok) {
                    document.getElementById('qr-code-placeholder').innerHTML = data.svg;
                    document.getElementById('2fa-secret-text').innerText = data.secret;

                    // Pastikan balik ke Step 1
                    document.getElementById('2fa-step-1').style.display = 'block';
                    document.getElementById('2fa-step-2').style.display = 'none';

                    document.getElementById('qr-modal').classList.add('active');
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Kata sandi salah.', iconColor: '#ef4444' });
                }
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan sistem.', iconColor: '#ef4444' });
            }
        }
        function hideQrModal() { document.getElementById('qr-modal').classList.remove('active'); }
        async function verify2faSetup() {
            const code = document.getElementById('setup-verification-code').value;
            if (code.length !== 6) return Swal.fire({ icon: 'warning', title: 'Kode Tidak Lengkap', text: 'Masukkan 6 digit kode.', iconColor: '#f59e0b' });
            try {
                const response = await fetch('{{ route("2fa.enable") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ code }) });
                const data = await response.json();
                if (data.success) {
                    Swal.fire({ icon: 'success', title: '2FA Aktif!', text: 'Sistem keamanan dua langkah berhasil diaktifkan.', timer: 2000, showConfirmButton: false, iconColor: '#10b981' });
                    setTimeout(() => location.reload(), 2000);
                } else {
                    Swal.fire({ icon: 'error', title: 'Verifikasi Gagal', text: data.message, iconColor: '#ef4444' });
                }
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Kesalahan', text: 'Terjadi kesalahan sistem.', iconColor: '#ef4444' });
            }
        }
        async function toggle2fa(enabled) {
            const statusIcon = document.getElementById('2fa-status-icon');
            const statusText = document.getElementById('2fa-status-text');
            const toggleSwitch = document.getElementById('2faSwitch');

            // Kunci tombol saat memproses agar tidak bisa di-spam klik
            toggleSwitch.disabled = true;

            try {
                const response = await fetch('{{ route("2fa.toggle") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ enabled })
                });
                const data = await response.json();

                if (!data.success) {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message, iconColor: '#ef4444' });
                    toggleSwitch.checked = !enabled; // Revert switch
                } else {
                    // Update UI Tanpa Refresh (Sat-Set)
                    if (enabled) {
                        statusIcon.className = 'bg-success bg-opacity-10 p-2 rounded-2 me-3';
                        statusIcon.querySelector('svg').setAttribute('stroke', '#198754');
                        statusText.className = 'fw-bold small text-success';
                        statusText.innerText = 'Keamanan Aktif';
                    } else {
                        statusIcon.className = 'bg-secondary bg-opacity-10 p-2 rounded-2 me-3';
                        statusIcon.querySelector('svg').setAttribute('stroke', '#6c757d');
                        statusText.className = 'fw-bold small text-secondary';
                        statusText.innerText = 'Keamanan Nonaktif';
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: enabled ? '2FA telah diaktifkan.' : '2FA telah dinonaktifkan.',
                        timer: 1500,
                        showConfirmButton: false,
                        iconColor: '#10b981'
                    });
                }
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Kesalahan', text: 'Terjadi kesalahan jaringan.', iconColor: '#ef4444' });
                toggleSwitch.checked = !enabled; // Revert switch
            } finally {
                // Buka kembali kunci tombol setelah proses selesai
                toggleSwitch.disabled = false;
            }
        }
        function showReset2faModal() {
            document.getElementById('reset2fa-password-input').value = '';
            document.getElementById('reset2fa-modal').classList.add('active');
        }

        function hideReset2faModal() {
            document.getElementById('reset2fa-modal').classList.remove('active');
        }

        async function submitReset2fa() {
            const password = document.getElementById('reset2fa-password-input').value;
            if (!password) {
                Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Kata sandi wajib diisi untuk mereset.', iconColor: '#eab308' });
                return;
            }

            const btn = document.getElementById('btn-submit-reset2fa');
            btn.disabled = true;
            btn.textContent = 'Memproses...';

            try {
                const response = await fetch('{{ route("2fa.reset") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ password: password })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    hideReset2faModal();
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: '2FA berhasil direset.', iconColor: '#10b981' }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Gagal mereset 2FA.', iconColor: '#ef4444' });
                }
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Kesalahan', text: 'Terjadi kesalahan sistem.', iconColor: '#ef4444' });
            } finally {
                btn.disabled = false;
                btn.textContent = 'Reset Sekarang';
            }
        }
        function previewProfilePhoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('profile-preview-settings').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        async function updateSecurity(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);

            // Sembunyikan semua error dulu
            document.querySelectorAll('[id^="err-"]').forEach(el => el.classList.add('d-none'));
            document.querySelectorAll('.form-control').forEach(el => el.classList.remove('is-invalid'));

            let hasError = false;

            // Validasi Nama
            if (!formData.get('name') || formData.get('name').trim() === '') {
                document.getElementById('err-name').classList.remove('d-none');
                document.getElementById('sec-name').classList.add('is-invalid');
                hasError = true;
            }

            // Validasi Email (Khusus Super Admin)
            const emailInput = document.getElementById('sec-email');
            if (emailInput && (!formData.get('email') || formData.get('email').trim() === '')) {
                document.getElementById('err-email').classList.remove('d-none');
                emailInput.classList.add('is-invalid');
                hasError = true;
            }

            // Validasi Password Konfirmasi
            if (!formData.get('current_password') || formData.get('current_password').trim() === '') {
                document.getElementById('err-password').classList.remove('d-none');
                document.getElementById('sec-password').classList.add('is-invalid');
                hasError = true;
            }

            if (hasError) {
                // Scroll ke field pertama yang error
                const firstError = document.querySelector('.is-invalid');
                if (firstError) firstError.focus();
                return;
            }

            const btn = document.getElementById('btn-update-security');
            btn.disabled = true; btn.textContent = 'Menyimpan...';

            try {
                const formData = new FormData(form);
                const response = await fetch('{{ route("security.update") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                const data = await response.json();

                if (response.ok) {
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 2000, showConfirmButton: false, iconColor: '#10b981' });
                    // Update nama di navbar secara instan
                    const displayEl = document.getElementById('user-name-display');
                    if (displayEl) {
                        displayEl.innerText = formData.get('name');
                    }

                    // Bersihkan kolom password setelah berhasil
                    form.reset(); // Mereset semua field ke awal
                    // Namun kita kembalikan nama dan email yang baru disimpan biar tetap tampil di form
                    document.getElementById('sec-name').value = formData.get('name');
                    const emailInput = document.getElementById('sec-email');
                    if (emailInput) emailInput.value = formData.get('email');
                } else {
                    let errorMsg = data.message;
                    if (data.errors) errorMsg = Object.values(data.errors).flat().join('\n');
                    Swal.fire({ icon: 'error', title: 'Gagal Memperbarui', text: errorMsg, iconColor: '#ef4444' });
                }
            } catch (err) {
                Swal.fire({ icon: 'error', title: 'Kesalahan Jaringan', text: 'Gagal terhubung ke server.', iconColor: '#ef4444' });
            }
            finally { btn.disabled = false; btn.textContent = 'Simpan Perubahan'; }
        }


        // USER MANAGEMENT FUNCTIONS
        function toggleNameField(role) {
            const container = document.getElementById('container-user-name');
            const input = document.getElementById('user-name');
            if (role === 'super_admin') {
                container.style.display = 'none';
                input.required = false;
                input.value = 'AUTO_GENERATED_SA'; // Placeholder logic marker
            } else {
                container.style.display = 'block';
                input.required = true;
                if (input.value === 'AUTO_GENERATED_SA') input.value = '';
            }
        }
    </script>

    <style>
        .btn-blue {
            background: #2563eb !important;
            color: white !important;
        }

        .bg-blue-50 {
            background-color: #eff6ff !important;
        }

        .text-blue-600 {
            color: #2563eb !important;
        }
    </style>

    <script>
        function showUserModal(id = null, name = '', email = '', role = 'admin', twoFactorEnabled = false) {
            document.getElementById('user-modal-title').textContent = id ? 'Edit Anggota Tim' : 'Tambah Anggota Tim';
            document.getElementById('user-id').value = id || '';
            document.getElementById('user-name').value = name;
            document.getElementById('user-email').value = email;
            document.getElementById('user-role').value = role;
            document.getElementById('user-password').value = '';

            const passwordHint = document.getElementById('password-hint');
            const passwordAsterisk = document.getElementById('password-asterisk');
            const reset2faContainer = document.getElementById('reset-2fa-container');
            const resetLinkContainer = document.getElementById('reset-link-container');
            const btnSave = document.getElementById('btn-save-user');
            const passwordContainer = document.getElementById('container-user-password');

            const roleSelect = document.getElementById('user-role');
            const nameInput = document.getElementById('user-name');
            const emailInput = document.getElementById('user-email');

            // Reset States
            roleSelect.disabled = false;
            nameInput.disabled = false;
            emailInput.disabled = false;
            passwordContainer.style.display = 'block';
            btnSave.style.display = 'block';
            reset2faContainer.style.display = 'none';
            resetLinkContainer.style.display = 'none';

            if (id) {
                passwordHint.style.display = 'block';
                passwordAsterisk.style.display = 'none';

                // Tampilkan container reset 2FA HANYA jika fitur 2FA sedang aktif di akun tersebut
                reset2faContainer.style.display = twoFactorEnabled ? 'block' : 'none';

                // Proteksi khusus: Jangan biarkan sesama Super Admin saling edit data sensitif (Termasuk Reset 2FA)
                if (role === 'super_admin' && id != '{{ auth()->id() }}') {
                    roleSelect.disabled = true;
                    nameInput.disabled = true;
                    emailInput.disabled = true;
                    passwordContainer.style.display = 'none';
                    btnSave.style.display = 'none';
                    reset2faContainer.style.display = 'none'; // Sembunyikan Reset 2FA sesama SA
                    resetLinkContainer.style.display = 'block'; // Tampilkan Link Reset Email sebagai gantinya
                }
            } else {
                passwordHint.style.display = 'none';
                passwordAsterisk.style.display = 'inline';
            }

            toggleNameField(role);
            document.getElementById('user-modal').classList.add('active');
        }

        function hideUserModal() { document.getElementById('user-modal').classList.remove('active'); }

        async function resetUser2fa() {
            const id = document.getElementById('user-id').value;
            const name = document.getElementById('user-name').value;
            if (!id) return;

            window.confirmSistem('Reset 2FA', `Apakah Anda yakin ingin mematikan paksa 2FA milik ${name}? Gunakan ini hanya jika petugas kehilangan akses ke perangkatnya.`, async function () {
                try {
                    const response = await fetch(`{{ url('users') }}/${id}/reset-2fa`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();
                    if (response.ok) {
                        hideUserModal();
                        Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, iconColor: '#10b981' });
                        // Refresh halaman agar state 2FA terbaru ter-update di tabel
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Gagal mereset 2FA.', iconColor: '#ef4444' });
                    }
                } catch (err) {
                    Swal.fire({ icon: 'error', title: 'Kesalahan Jaringan', text: 'Terjadi kesalahan jaringan.', iconColor: '#ef4444' });
                }
            });
        }

        async function saveUser(e) {
            e.preventDefault();
            const id = document.getElementById('user-id').value;
            const btn = document.getElementById('btn-save-user');
            btn.disabled = true; btn.textContent = 'Sedang Menyimpan...';

            const payload = {
                name: document.getElementById('user-name').value,
                email: document.getElementById('user-email').value,
                role: document.getElementById('user-role').value,
                password: document.getElementById('user-password').value,
            };

            const url = id ? `{{ url('users') }}/${id}` : `{{ route('users.store') }}`;
            const method = id ? 'PUT' : 'POST';

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();
                if (response.ok) {
                    hideUserModal(); // Sembunyikan modal agar background tidak menumpuk ganda (menghitam)
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 2000, showConfirmButton: false, iconColor: '#10b981' });
                    setTimeout(() => location.reload(), 2000);
                } else {
                    let errorMsg = data.message;
                    if (data.errors) {
                        errorMsg = Object.values(data.errors).flat().join('\n');
                    }
                    Swal.fire({ icon: 'error', title: 'Gagal Menyimpan', text: errorMsg, iconColor: '#ef4444' });
                }
            } catch (err) {
                Swal.fire({ icon: 'error', title: 'Kesalahan Jaringan', text: 'Terjadi kesalahan jaringan.', iconColor: '#ef4444' });
            } finally {
                btn.disabled = false;
                btn.textContent = id ? 'Perbarui Anggota' : 'Simpan Anggota';
            }
        }

        async function deleteUser(id) {
            window.confirmSistem('Hapus Anggota', 'Apakah Anda yakin ingin menghapus anggota ini?', async function () {

                try {
                    const response = await fetch(`{{ url('users') }}/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();
                    if (response.ok) {
                        Swal.fire({ icon: 'success', title: 'Dihapus!', text: data.message, timer: 2000, showConfirmButton: false, iconColor: '#10b981' });
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Gagal menghapus anggota.', iconColor: '#ef4444' });
                    }
                } catch (err) {
                    Swal.fire({ icon: 'error', title: 'Kesalahan Jaringan', text: 'Terjadi kesalahan jaringan.', iconColor: '#ef4444' });
                }
            });
        }

        async function toggleUserStatus(id, name, isActive) {
            const title = isActive ? 'Nonaktifkan Akun?' : 'Aktifkan Akun?';
            const text = isActive
                ? `Akun ${name} tidak akan bisa login lagi, tapi data laporannya tetap tersimpan.`
                : `Akun ${name} akan bisa kembali login ke sistem.`;

            window.confirmSistem(title, text, async function () {
                try {
                    const response = await fetch(`{{ url('users') }}/${id}/toggle-status`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();
                    if (response.ok) {
                        // UPDATE UI SECARA INSTAN TANPA REFRESH
                        const isNowActive = !isActive;
                        const badgeContainer = document.getElementById(`user-status-container-${id}`);
                        const btn = document.getElementById(`btn-status-${id}`);

                        // Update Badge
                        if (isNowActive) {
                            badgeContainer.innerHTML = '<span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill" style="font-size: 0.65rem;">AKTIF</span>';
                            btn.className = 'btn btn-sm btn-outline-warning rounded-3';
                            btn.title = 'Nonaktifkan Akun';
                        } else {
                            badgeContainer.innerHTML = '<span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1 rounded-pill" style="font-size: 0.65rem;">NONAKTIF</span>';
                            btn.className = 'btn btn-sm btn-outline-success rounded-3';
                            btn.title = 'Aktifkan Akun';
                        }

                        // Update fungsi onclick tombol agar tahu status terbaru
                        btn.onclick = () => toggleUserStatus(id, name, isNowActive);

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false,
                            iconColor: '#10b981'
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: data.message, iconColor: '#ef4444' });
                    }
                } catch (err) {
                    Swal.fire({ icon: 'error', title: 'Kesalahan Jaringan', text: 'Gagal terhubung ke server.', iconColor: '#ef4444' });
                }
            });
        }

        function showDeleteAccountModal() {
            document.getElementById('delete-account-modal').classList.add('active');
        }

        function hideDeleteAccountModal() {
            document.getElementById('delete-account-modal').classList.remove('active');
        }

        async function confirmDeleteSelf() {
            const password = document.getElementById('delete-confirm-password').value;
            if (!password) {
                return Swal.fire({ icon: 'warning', title: 'Kata Sandi Wajib', text: 'Silakan masukkan kata sandi Anda untuk konfirmasi.', iconColor: '#f59e0b' });
            }

            try {
                const response = await fetch(`{{ url('users') }}/{{ auth()->id() }}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ password: password })
                });

                const data = await response.json();
                if (response.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Akun Dihapus',
                        text: 'Akun Anda telah dihapus. Anda akan dialihkan ke halaman login.',
                        showConfirmButton: true,
                        iconColor: '#10b981'
                    }).then(() => {
                        window.location.href = '{{ route("login") }}';
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Gagal menghapus akun.', iconColor: '#ef4444' });
                    hideDeleteAccountModal();
                }
            } catch (err) {
                Swal.fire({ icon: 'error', title: 'Kesalahan Jaringan', text: 'Terjadi kesalahan jaringan.', iconColor: '#ef4444' });
                hideDeleteAccountModal();
            }
        }

        async function sendResetLink() {
            const id = document.getElementById('user-id').value;
            const btn = document.getElementById('btn-send-reset');
            if (!id) return;

            btn.disabled = true;
            btn.textContent = 'Mengirim...';

            try {
                const response = await fetch(`{{ url('users') }}/${id}/reset-password`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                if (response.ok) {
                    hideUserModal();
                    Swal.fire({ icon: 'success', title: 'Tautan Dikirim!', text: data.message, iconColor: '#10b981' });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal Mengirim', text: data.message || 'Gagal mengirim email reset.', iconColor: '#ef4444' });
                }
            } catch (err) {
                Swal.fire({ icon: 'error', title: 'Kesalahan Jaringan', text: 'Terjadi kesalahan jaringan.', iconColor: '#ef4444' });
            } finally {
                btn.disabled = false;
                btn.textContent = 'Kirim Tautan Reset';
            }
        }

        // ================= MASTER DATA FUNCTIONS =================
        let masterDataCache = {};
        
        async function loadMasterData() {
            const category = document.getElementById('master-data-category').value;

            // Tampilkan/Sembunyikan Toggle Hybrid khusus untuk Kelas Emisi
            const hybridContainer = document.getElementById('hybrid-config-container');
            if (category === 'kelas_emisi') {
                hybridContainer.classList.remove('d-none');
                fetchHybridEmisiConfig();
            } else {
                hybridContainer.classList.add('d-none');
            }

            // Perceived Performance: Jika ada di cache, tampilkan INSTAN tanpa skeleton
            if (masterDataCache[category]) {
                renderMasterDataTable(masterDataCache[category]);
                // Tetap update di background (Silent Refresh) agar data selalu fresh
                fetchMasterData(category, false);
            } else {
                // Jika belum ada, tampilkan skeleton
                showMasterDataSkeleton();
                fetchMasterData(category, true);
            }

            // Sync UI visibility based on mode
            updateMasterDataUIVisibility(category);
        }

        function updateMasterDataUIVisibility(category) {
            const isManualMode = document.getElementById('hybridEmisiToggle').checked;
            const btnAdd = document.getElementById('btn-add-master-data');
            
            if (category === 'kelas_emisi' && isManualMode) {
                btnAdd.classList.add('d-none');
            } else {
                btnAdd.classList.remove('d-none');
            }
        }

        async function fetchHybridEmisiConfig() {
            try {
                const response = await fetch(`{{ route('master-data.get-config') }}?key=kelas_emisi_manual`);
                const config = await response.json();
                const toggle = document.getElementById('hybridEmisiToggle');
                toggle.checked = !!config.is_active;
                
                // Update UI state after fetching config
                const category = document.getElementById('master-data-category').value;
                updateMasterDataUIVisibility(category);
                if (category === 'kelas_emisi' && toggle.checked) {
                    renderMasterDataTable([]); // Will show "Mode Manual Aktif" message
                }
            } catch (e) {
                console.error('Failed to fetch config');
            }
        }

        async function toggleHybridEmisi(manualMode) {
            const toggle = document.getElementById('hybridEmisiToggle');
            const category = document.getElementById('master-data-category').value;

            // Debounce: Disable toggle during request
            toggle.disabled = true;

            try {
                // Pakai endpoint toggleStatus yang sudah ada, tapi untuk record config
                const configRes = await fetch(`{{ route('master-data.get-config') }}?key=kelas_emisi_manual`);
                const config = await configRes.json();
                
                const response = await fetch(`{{ url('master-data') }}/${config.id}/toggle-status`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });

                if (response.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Konfigurasi Diperbarui',
                        text: manualMode ? 'Mode Input Manual Aktif (Dropdown Dimatikan).' : 'Mode Dropdown Aktif (Manual Dimatikan).',
                        timer: 1500,
                        showConfirmButton: false,
                        iconColor: '#10b981'
                    });

                    // Update UI immediately
                    updateMasterDataUIVisibility(category);
                    if (category === 'kelas_emisi') {
                        if (manualMode) {
                            renderMasterDataTable([]); // This will trigger the manual mode message
                        } else {
                            loadMasterData(); // Refresh to show the table again
                        }
                    }
                }
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal memperbarui konfigurasi.' });
                toggle.checked = !manualMode; // Revert if failed
            } finally {
                toggle.disabled = false;
            }
        }

        function showMasterDataSkeleton() {
            const tbody = document.getElementById('master-data-tbody');
            tbody.innerHTML = `
                    <tr><td class="ps-4 py-3"><div class="skeleton" style="height: 18px; width: 70%;"></div></td><td class="text-center"><div class="skeleton mx-auto" style="height: 22px; width: 50px; border-radius: 20px;"></div></td><td class="pe-4 text-end"><div class="skeleton ms-auto" style="height: 30px; width: 60px; border-radius: 8px;"></div></td></tr>
                    <tr><td class="ps-4 py-3"><div class="skeleton" style="height: 18px; width: 50%;"></div></td><td class="text-center"><div class="skeleton mx-auto" style="height: 22px; width: 50px; border-radius: 20px;"></div></td><td class="pe-4 text-end"><div class="skeleton ms-auto" style="height: 30px; width: 60px; border-radius: 8px;"></div></td></tr>
                    <tr><td class="ps-4 py-3"><div class="skeleton" style="height: 18px; width: 60%;"></div></td><td class="text-center"><div class="skeleton mx-auto" style="height: 22px; width: 50px; border-radius: 20px;"></div></td><td class="pe-4 text-end"><div class="skeleton ms-auto" style="height: 30px; width: 60px; border-radius: 8px;"></div></td></tr>
                `;
        }

        async function fetchMasterData(category, updateUI = true) {
            try {
                const response = await fetch(`{{ route('master-data.index') }}?category=${category}`, {
                    headers: { 'Accept': 'application/json' }
                });
                if (!response.ok) throw new Error('Network response was not ok');
                const data = await response.json();

                // Simpan ke cache
                masterDataCache[category] = data;

                if (updateUI) {
                    renderMasterDataTable(data);
                }
            } catch (error) {
                if (updateUI) {
                    const tbody = document.getElementById('master-data-tbody');
                    tbody.innerHTML = `<tr><td colspan="3" class="text-center py-4 text-danger">Gagal memuat data: ${error.message}</td></tr>`;
                }
            }
        }

        function renderMasterDataTable(data) {
            const tbody = document.getElementById('master-data-tbody');
            const category = document.getElementById('master-data-category').value;
            const isManualMode = document.getElementById('hybridEmisiToggle').checked;

            if (category === 'kelas_emisi' && isManualMode) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="3" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center opacity-50">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="mb-3 text-slate-400">
                                    <path d="M12 19l7-7 3 3-7 7-3-3z"></path>
                                    <path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"></path>
                                    <path d="M2 2l7.586 7.586"></path>
                                    <circle cx="11" cy="11" r="2"></circle>
                                </svg>
                                <div class="fw-bold text-navy">Mode Input Manual Aktif</div>
                                <div class="text-slate-400" style="font-size: 0.75rem;">Petugas sekarang mengisi Kelas Emisi secara bebas (Teks).<br>Master Data dropdown tidak digunakan.</div>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }

            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" class="text-center py-4 text-slate-400">Belum ada opsi untuk kategori ini.</td></tr>';
                return;
            }

            let html = '';
            data.forEach(item => {
                const statusBadge = item.is_active
                    ? '<span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill" style="font-size: 0.65rem;">AKTIF</span>'
                    : '<span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1 rounded-pill" style="font-size: 0.65rem;">NONAKTIF</span>';

                const toggleBtnClass = item.is_active ? 'btn-outline-warning' : 'btn-outline-success';
                const toggleBtnTitle = item.is_active ? 'Nonaktifkan' : 'Aktifkan';

                html += `
                        <tr id="md-row-${item.id}">
                            <td class="ps-4 py-3 fw-bold text-navy" id="md-val-${item.id}">${item.value}</td>
                            <td class="py-3 text-center" id="md-status-${item.id}">${statusBadge}</td>
                            <td class="pe-4 py-3 text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    <button id="md-btn-toggle-${item.id}" class="btn btn-sm ${toggleBtnClass} rounded-3" title="${toggleBtnTitle}" onclick="toggleMasterData(${item.id}, ${item.is_active})">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path><line x1="12" y1="2" x2="12" y2="12"></line></svg>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary rounded-3" onclick="showMasterDataModal(${item.id}, '${item.value.replace(/'/g, "\\'")}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger rounded-3" onclick="deleteMasterData(${item.id})">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
            });
            tbody.innerHTML = html;
        }

        function showMasterDataModal(id = null, value = '') {
            const categorySelect = document.getElementById('master-data-category');
            const category = categorySelect.value;
            const inputVal = document.getElementById('md-value');

            document.getElementById('md-id').value = id || '';
            inputVal.value = value;
            document.getElementById('md-category').value = category;

            // Set Placeholder dinamis berdasarkan kategori
            let placeholder = 'MASUKKAN NILAI...';
            switch(category) {
                case 'kelas_emisi': placeholder = 'CONTOH: A3E'; break;
                case 'kategori': placeholder = 'CONTOH: MF RUTIN'; break;
                case 'kelas_stasiun': placeholder = 'CONTOH: FX'; break;
                case 'stasiun_monitor': placeholder = 'CONTOH: MSHF LAMPUNG'; break;
                case 'kode_negara': placeholder = 'CONTOH: INDONESIA (INS)'; break;
                case 'administrasi_termonitor': placeholder = 'CONTOH: INS'; break;
            }
            inputVal.placeholder = placeholder;

            document.getElementById('master-data-modal-title').textContent = id ? 'Edit Opsi' : 'Tambah Opsi Baru';
            document.getElementById('btn-save-md').textContent = id ? 'Perbarui Opsi' : 'Simpan Opsi';

            document.getElementById('master-data-modal').classList.add('active');
        }

        function hideMasterDataModal() {
            document.getElementById('master-data-modal').classList.remove('active');
        }

        async function saveMasterData(e) {
            e.preventDefault();
            const id = document.getElementById('md-id').value;
            const category = document.getElementById('md-category').value;
            const value = document.getElementById('md-value').value;
            const btn = document.getElementById('btn-save-md');

            btn.disabled = true;
            btn.textContent = 'Menyimpan...';

            const url = id ? `{{ url('master-data') }}/${id}` : `{{ route('master-data.store') }}`;
            const method = id ? 'PUT' : 'POST';

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ category, value })
                });

                const data = await response.json();
                if (response.ok) {
                    hideMasterDataModal();
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 1500, showConfirmButton: false, iconColor: '#10b981' });
                    loadMasterData(); // Refresh tabel
                } else {
                    let errorMsg = data.message;
                    if (data.errors) errorMsg = Object.values(data.errors).flat().join('\n');
                    Swal.fire({ icon: 'error', title: 'Gagal', text: errorMsg, iconColor: '#ef4444' });
                }
            } catch (err) {
                Swal.fire({ icon: 'error', title: 'Kesalahan', text: 'Gagal terhubung ke server.', iconColor: '#ef4444' });
            } finally {
                btn.disabled = false;
                btn.textContent = id ? 'Perbarui Opsi' : 'Simpan Opsi';
            }
        }

        async function toggleMasterData(id, isActive) {
            try {
                const response = await fetch(`{{ url('master-data') }}/${id}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                if (response.ok) {
                    const isNowActive = data.is_active;
                    const badgeContainer = document.getElementById(`md-status-${id}`);
                    const btn = document.getElementById(`md-btn-toggle-${id}`);

                    if (isNowActive) {
                        badgeContainer.innerHTML = '<span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill" style="font-size: 0.65rem;">AKTIF</span>';
                        btn.className = 'btn btn-sm btn-outline-warning rounded-3';
                        btn.title = 'Nonaktifkan';
                    } else {
                        badgeContainer.innerHTML = '<span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1 rounded-pill" style="font-size: 0.65rem;">NONAKTIF</span>';
                        btn.className = 'btn btn-sm btn-outline-success rounded-3';
                        btn.title = 'Aktifkan';
                    }

                    btn.onclick = () => toggleMasterData(id, isNowActive);
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message, iconColor: '#ef4444' });
                }
            } catch (err) {
                Swal.fire({ icon: 'error', title: 'Kesalahan', text: 'Gagal menghubungi server.', iconColor: '#ef4444' });
            }
        }

        async function deleteMasterData(id) {
            window.confirmSistem('Hapus Opsi', 'Apakah Anda yakin ingin menghapus opsi ini? Jika data ini pernah dipakai, lebih baik di-nonaktifkan saja.', async function () {
                try {
                    const response = await fetch(`{{ url('master-data') }}/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();
                    if (response.ok) {
                        Swal.fire({ icon: 'success', title: 'Dihapus!', text: data.message, timer: 1500, showConfirmButton: false, iconColor: '#10b981' });
                        loadMasterData();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: data.message, iconColor: '#ef4444' });
                    }
                } catch (err) {
                    Swal.fire({ icon: 'error', title: 'Kesalahan', text: 'Terjadi kesalahan jaringan.', iconColor: '#ef4444' });
                }
            });
        }

        // Hapus auto-load berdasarkan session karena fitur memori sudah dimatikan
        function checkMasterDataAutoLoad() {
            loadMasterData();
        }

        document.addEventListener('DOMContentLoaded', () => {
            checkMasterDataAutoLoad();
        });
        document.addEventListener('livewire:navigated', () => {
            checkMasterDataAutoLoad();
        });

        function copyToClipboard(elementId) {
            const text = document.getElementById(elementId).innerText;
            return navigator.clipboard.writeText(text);
        }

        function copyAndContinue() {
            copyToClipboard('2fa-secret-text').then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Disalin!',
                    text: 'Kode cadangan berhasil disalin. Sekarang silakan scan QR Code.',
                    timer: 1500,
                    showConfirmButton: false,
                    iconColor: '#10b981'
                });

                // Transisi ke Step 2
                document.getElementById('2fa-step-1').style.display = 'none';
                const step2 = document.getElementById('2fa-step-2');
                step2.style.display = 'block';
                step2.style.animation = 'fadeIn 0.5s ease-out';
            });
        }
    </script>
@endsection