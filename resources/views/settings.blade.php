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
        .settings-card h3 { font-size: 1.05rem; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem; }
        .settings-card p { font-size: 0.8rem; color: #64748b; line-height: 1.5; margin-bottom: 0; }
        .settings-card .status-badge { position: absolute; top: 1.25rem; right: 1.25rem; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; padding: 0.25rem 0.6rem; border-radius: 2rem; background: #dcfce7; color: #166534; }
        .view-container { display: none; animation: fadeIn 0.4s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .back-btn { display: inline-flex; align-items: center; gap: 0.5rem; color: #64748b; font-weight: 600; font-size: 0.85rem; text-decoration: none; margin-bottom: 1.5rem; transition: all 0.2s ease; padding: 0.5rem 0.75rem; border-radius: 0.75rem; }
        .back-btn:hover { background: #f1f5f9; color: #1e293b; transform: translateX(-3px); }
        .fw-800 { font-weight: 800; }
        .text-navy { color: #0f172a; }
        .password-toggle { position: absolute; right: 1.25rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94a3b8; cursor: pointer; padding: 0; display: flex; align-items: center; z-index: 10; transition: color 0.2s; }
        .password-toggle:hover { color: #475569; }
        .form-control.with-toggle { padding-right: 3.5rem; }
        .avatar-circle { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 2px solid #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .avatar-placeholder { width: 32px; height: 32px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-weight: 700; font-size: 0.7rem; border: 2px solid #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        #qr-code-placeholder svg { width: 140px !important; height: 140px !important; display: block; margin: 0 auto; }
    </style>

    <div class="container-fluid p-0">
        <!-- SETTINGS HUB -->
        <div id="settings-hub" style="display: none;">
            <div class="settings-hub">
                <div class="settings-card" onclick="switchSettingsView('security')">
                    <span class="status-badge">Aktif</span>
                    <div class="icon-box"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg></div>
                    <h3>Keamanan Akun</h3>
                    <p>Ganti kata sandi dan atur autentikasi dua faktor (2FA).</p>
                </div>

                @if(auth()->user()->role === 'super_admin')
                    <div class="settings-card" onclick="switchSettingsView('logs')">
                        <span class="status-badge">Aktif</span>
                        <div class="icon-box"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg></div>
                        <h3>Log Aktivitas</h3>
                        <p>Pantau riwayat audit dan login sistem.</p>
                    </div>

                    <div class="settings-card" onclick="switchSettingsView('threats')">
                        <span class="status-badge">Aktif</span>
                        <div class="icon-box" style="color: #dc2626;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><path d="m12 8 4 4"></path><path d="m16 8-4 4"></path></svg></div>
                        <h3>Aktivitas Mencurigakan</h3>
                        <p>Deteksi percobaan brute force atau akses ilegal.</p>
                    </div>

                    <div class="settings-card" onclick="switchSettingsView('users')">
                        <span class="status-badge">Aktif</span>
                        <div class="icon-box"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg></div>
                        <h3>Kelola User</h3>
                        <p>Tambah anggota tim dan atur peran akun.</p>
                    </div>

                    <div class="settings-card" onclick="switchSettingsView('masterdata')">
                        <span class="status-badge">Aktif</span>
                        <div class="icon-box" style="color: #0ea5e9;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h14a2 2 0 0 0 2-2V7.5L14.5 2H6a2 2 0 0 0-2 2v4"></path><polyline points="14 2 14 8 20 8"></polyline><path d="M2 15h10"></path><path d="m9 18 3-3-3-3"></path></svg></div>
                        <h3>Master Data</h3>
                        <p>Kelola daftar dropdown (Stasiun, Negara, dll).</p>
                    </div>

                @endif
            </div>
        </div>



        <!-- DETAIL VIEW: KEAMANAN -->
        <div id="view-security" class="view-container">
            <a href="javascript:void(0)" onclick="showSettingsHub()" class="back-btn"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg> Kembali</a>
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    {{-- 2FA CARD --}}
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-navy mb-2">Keamanan Dua Langkah (2FA)</h6>
                            <p class="text-slate-500 mb-4" style="font-size: 0.85rem; line-height: 1.6;">Lindungi akun Anda dengan verifikasi tambahan melalui aplikasi authenticator.</p>
                            
                            @php
                                $hasSecret = !empty(auth()->user()->google2fa_secret);
                                $isEnabled = (bool)auth()->user()->two_factor_enabled;
                            @endphp
                            @if(!$hasSecret)
                                <button class="btn btn-blue w-100 rounded-3 fw-bold py-2 shadow-sm" onclick="start2faSetup()">Aktifkan 2FA</button>
                            @else
                                <div class="p-3 rounded-3 border bg-light d-flex align-items-center justify-content-between mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success bg-opacity-10 p-2 rounded-2 me-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#198754" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        </div>
                                        <span class="fw-bold small text-success">Keamanan Aktif</span>
                                    </div>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="2faSwitch" {{ $isEnabled ? 'checked' : '' }} onchange="toggle2fa(this.checked)">
                                    </div>
                                </div>
                                <button class="btn btn-outline-secondary btn-sm w-100 rounded-3 py-2" onclick="reset2fa()">Atur Ulang Perangkat</button>
                            @endif
                        </div>
                    </div>

                    {{-- DANGER ZONE CARD (Pindah ke kiri) --}}
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-danger bg-opacity-10 p-2 rounded-2 me-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                                </div>
                                <h6 class="text-danger fw-bold m-0" style="font-size: 0.9rem;">Hapus Akun Permanen</h6>
                            </div>
                            <p class="text-slate-500 mb-4" style="font-size: 0.75rem; line-height: 1.5;">Tindakan ini tidak dapat dibatalkan. Semua data dan akses Anda akan dihapus selamanya.</p>
                            <button type="button" class="btn btn-outline-danger w-100 rounded-3 py-2 fw-bold" onclick="showDeleteAccountModal()">Hapus Akun Saya</button>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-navy mb-4">Pengaturan Akses</h6>
                            <form onsubmit="updateSecurity(event)">
                                <input type="hidden" name="name" value="{{ auth()->user()->name }}">
                                
                                @if(auth()->user()->role === 'super_admin')
                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-slate-700">Email Utama <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control rounded-3 py-2 px-3 border bg-light bg-opacity-25" value="{{ auth()->user()->email }}" required style="font-size: 0.9rem; border-color: #dee2e6;">
                                    <div class="text-slate-400 mt-1" style="font-size: 0.75rem;">Email resmi Balmon Lampung.</div>
                                </div>
                                @else
                                    <input type="hidden" name="email" value="{{ auth()->user()->email }}">
                                @endif

                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-slate-700">Kata Sandi Saat Ini <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" name="current_password" class="form-control rounded-start-3 py-2 px-3 border border-end-0 bg-light bg-opacity-25" required placeholder="Masukkan sandi lama" style="font-size: 0.9rem; border-color: #dee2e6;">
                                        <button type="button" class="btn btn-white border border-start-0 text-slate-400 px-3" onclick="togglePassword(this)" style="border-color: #dee2e6;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-slate-700">Kata Sandi Baru</label>
                                        <div class="input-group">
                                            <input type="password" name="password" class="form-control rounded-start-3 py-2 px-3 border border-end-0 bg-light bg-opacity-25" placeholder="Min. 8 karakter" style="font-size: 0.9rem; border-color: #dee2e6;">
                                            <button type="button" class="btn btn-white border border-start-0 text-slate-400 px-3" onclick="togglePassword(this)" style="border-color: #dee2e6;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-slate-700">Konfirmasi Sandi</label>
                                        <div class="input-group">
                                            <input type="password" name="password_confirmation" class="form-control rounded-start-3 py-2 px-3 border border-end-0 bg-light bg-opacity-25" placeholder="Ulangi sandi baru" style="font-size: 0.9rem; border-color: #dee2e6;">
                                            <button type="button" class="btn btn-white border border-start-0 text-slate-400 px-3" onclick="togglePassword(this)" style="border-color: #dee2e6;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="text-slate-400 italic" style="font-size: 0.75rem;">* Kosongkan jika tidak ingin mengubah sandi.</div>
                                    </div>
                                </div>

                                <div class="mb-2 d-flex justify-content-between align-items-center pt-3 border-top">
                                    <button type="submit" id="btn-update-security" class="btn btn-blue rounded-3 fw-bold px-4 py-2 shadow-sm border-0">Simpan Perubahan</button>
                                    <div class="text-slate-400 small d-flex align-items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
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
                <a href="javascript:void(0)" onclick="showSettingsHub()" class="back-btn"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg> Kembali</a>
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white fw-bold py-3">Log Aktivitas Sistem</div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.75rem;">
                            <thead class="bg-light">
                                <tr><th class="px-4">WAKTU & USER</th><th>AKSI</th><th>DESKRIPSI</th><th class="px-4 text-end">IP ADDRESS</th></tr>
                            </thead>
                            <tbody>
                                @foreach ($activityLogs as $log)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center gap-3">
                                                @if($log->user && $log->user->role !== 'super_admin')
                                                    @if($log->user->profile_photo)
                                                        <img src="{{ asset('storage/' . $log->user->profile_photo) }}" class="avatar-circle" alt="Avatar">
                                                    @else
                                                        <div class="avatar-placeholder">{{ strtoupper(substr($log->user->name, 0, 1)) }}</div>
                                                    @endif
                                                @endif
                                                <div>
                                                    <div class="fw-bold text-navy">{{ $log->user->name ?? 'System' }}</div>
                                                    <div class="text-slate-400 small" style="font-size: 0.65rem;">{{ $log->created_at->format('d/m/Y H:i') }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-light text-dark border">{{ strtoupper(str_replace('_', ' ', (string)$log->action)) }}</span></td>
                                        <td>{{ $log->description }}</td>
                                        <td class="px-4 text-end text-slate-500 font-monospace" style="font-size: 0.7rem;">{{ $log->ip_address }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- DETAIL VIEW: THREATS -->
            <div id="view-threats" class="view-container">
                <a href="javascript:void(0)" onclick="showSettingsHub()" class="back-btn"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg> Kembali</a>
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white fw-bold py-3 text-danger">Log Deteksi Ancaman</div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.75rem;">
                            <thead class="bg-light">
                                <tr><th class="px-4">WAKTU</th><th>IP ADDRESS</th><th>JENIS ANCAMAN</th><th class="px-4 text-end">STATUS</th></tr>
                            </thead>
                            <tbody>
                                @php $threats = $activityLogs->filter(fn($l) => in_array($l->action, ['failed_login', 'failed_2fa', 'brute_force_detected', 'suspicious_access'])); @endphp
                                @forelse ($threats as $t)
                                    <tr>
                                        <td class="px-4">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="fw-bold text-danger">{{ $t->ip_address }}</td>
                                        <td><span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10">{{ strtoupper(str_replace('_', ' ', (string)$t->action)) }}</span></td>
                                        <td class="px-4 text-end"><span class="text-success fw-bold">DIBLOKIR</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center py-5 text-slate-400">Tidak ada ancaman terdeteksi.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- DETAIL VIEW: USERS -->
            <div id="view-users" class="view-container">
                <a href="javascript:void(0)" onclick="showSettingsHub()" class="back-btn"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg> Kembali</a>
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center">
                        <span>Daftar Anggota Tim</span>
                        <button class="btn btn-sm btn-primary rounded-3" onclick="showUserModal()">+ Tambah</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.8rem;">
                            <thead class="bg-light">
                                <tr><th class="px-4">NAMA & EMAIL</th><th>ROLE</th><th class="text-center">STATUS</th><th class="text-center">2FA</th><th class="px-4 text-end">AKSI</th></tr>
                            </thead>
                            <tbody>
                                @foreach(\App\Models\User::all() as $u)
                                    <tr id="user-row-{{ $u->id }}">
                                        <td class="px-4">
                                            <div class="d-flex align-items-center gap-3 py-2">
                                                @if($u->role !== 'super_admin')
                                                    @if($u->profile_photo)
                                                        <img src="{{ asset('storage/' . $u->profile_photo) }}" class="avatar-circle" alt="Avatar">
                                                    @else
                                                        <div class="avatar-placeholder">{{ strtoupper(substr($u->name, 0, 1)) }}</div>
                                                    @endif
                                                @endif
                                                <div>
                                                    <div class="fw-bold text-navy">
                                                        {{ $u->name }} 
                                                        @if($u->id === auth()->id()) 
                                                            <span class="badge bg-blue-100 text-blue-600 fw-normal ms-1" style="font-size: 0.65rem; vertical-align: middle;">(Anda)</span> 
                                                        @endif
                                                    </div>
                                                    <div class="text-slate-400 small">{{ $u->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge rounded-pill px-3" style="background: {{ $u->role === 'super_admin' ? '#eff6ff' : '#f8fafc' }}; color: {{ $u->role === 'super_admin' ? '#2563eb' : '#64748b' }};">{{ $u->role === 'super_admin' ? 'Super Admin' : 'Admin Tim' }}</span></td>
                                        <td class="text-center" id="user-status-container-{{ $u->id }}">
                                            @if($u->is_active)
                                                <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill" style="font-size: 0.65rem;">AKTIF</span>
                                            @else
                                                <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1 rounded-pill" style="font-size: 0.65rem;">NONAKTIF</span>
                                            @endif
                                        </td>
                                        <td class="text-center">@if($u->two_factor_enabled)<span class="text-success fw-bold">AKTIF</span>@else<span class="text-slate-300">OFF</span>@endif</td>
                                        <td class="px-4 text-end">
                                            <div class="d-flex gap-2 justify-content-end">
                                                {{-- Tombol Toggle Status (Hanya jika bukan diri sendiri dan bukan sesama Super Admin) --}}
                                                @if($u->id !== auth()->id() && $u->role !== 'super_admin')
                                                <button id="btn-status-{{ $u->id }}" 
                                                        class="btn btn-sm {{ $u->is_active ? 'btn-outline-warning' : 'btn-outline-success' }} rounded-3" 
                                                        title="{{ $u->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}"
                                                        onclick="toggleUserStatus({{ $u->id }}, '{{ $u->name }}', {{ $u->is_active ? 'true' : 'false' }})">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path><line x1="12" y1="2" x2="12" y2="12"></line></svg>
                                                </button>
                                                @endif

                                                {{-- Tombol Edit selalu ada untuk semua user --}}
                                                <button class="btn btn-sm btn-outline-secondary rounded-3" 
                                                        onclick="showUserModal({{ $u->id }}, '{{ $u->name }}', '{{ $u->email }}', '{{ $u->role }}')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                </button>
                                                
                                                {{-- Tombol Hapus hanya muncul jika BUKAN diri sendiri dan BUKAN sesama Super Admin --}}
                                                @if($u->id !== auth()->id() && $u->role !== 'super_admin')
                                                <button class="btn btn-sm btn-outline-danger rounded-3" onclick="deleteUser({{ $u->id }})">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                                </button>
                                                @elseif($u->id !== auth()->id() && $u->role === 'super_admin')
                                                    <span class="btn btn-sm btn-light border-0 disabled text-slate-300" title="Super Admin Terproteksi">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
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
                    <h5 class="fw-800 text-navy mb-4" style="font-size: 1.1rem;">Scan QR Code</h5>
                    <div id="qr-code-placeholder" class="mb-4 p-3 bg-white d-inline-block rounded-4 border"></div>
                    <div class="mb-4">
                        <input type="text" id="setup-code" class="form-control text-center fw-800" placeholder="000000" maxlength="6" style="font-size: 1.5rem; border-radius: 1rem;">
                        <div class="text-slate-400 mt-2" style="font-size: 0.75rem;">Masukkan 6 digit kode dari aplikasi.</div>
                    </div>
                    <div class="d-grid gap-2">
                        <button class="btn btn-dark rounded-3 fw-bold py-2 shadow-sm" id="btn-verify-setup" onclick="verify2faSetup()">Verifikasi & Aktifkan</button>
                        <button class="btn btn-link text-slate-400 text-decoration-none small" onclick="hideQrModal()">Batal</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- USER MANAGEMENT MODAL -->
        <div id="user-modal" class="modal-premium-overlay">
            <div class="modal-premium-container" style="max-width: 420px;">
                <div class="modal-premium-content text-start">
                    <h5 class="fw-800 text-navy mb-4" id="user-modal-title" style="font-size: 1.1rem;">Tambah Anggota Tim</h5>
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
                            <select id="user-role" class="form-select rounded-3" required onchange="toggleNameField(this.value)">
                                <option value="admin">Admin Tim</option>
                                <option value="super_admin">Super Admin</option>
                            </select>
                        </div>
                        <div class="mb-4" id="container-user-password">
                            <label class="form-label small fw-bold">Kata Sandi <span id="password-asterisk" class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="password" id="user-password" class="form-control rounded-3 with-toggle" placeholder="Minimal 8 karakter">
                                <button type="button" class="password-toggle" onclick="togglePassword(this)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                </button>
                            </div>
                            <div id="password-hint" class="small text-slate-400 mt-1" style="font-size: 0.7rem;">* Kosongkan jika tidak ingin mengubah password.</div>
                        </div>

                        <div id="reset-link-container" class="mb-4" style="display:none;">
                            <div class="p-3 rounded-4 bg-blue-50 border border-blue-100 text-center">
                                <p class="small text-slate-600 mb-3" style="font-size: 0.75rem;">Kirimkan tautan resmi ke email pemilik akun ini agar mereka dapat meriset kata sandi secara mandiri.</p>
                                <button type="button" id="btn-send-reset" class="btn btn-blue btn-sm rounded-3 fw-bold px-4" onclick="sendResetLink()">Kirim Tautan Reset</button>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-dark rounded-3 fw-bold py-2" id="btn-save-user">Simpan Anggota</button>
                            <button type="button" class="btn btn-link text-slate-400 text-decoration-none small" onclick="hideUserModal()">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- DETAIL VIEW: MASTER DATA -->
        <div id="view-masterdata" class="view-container">
            <a href="javascript:void(0)" onclick="showSettingsHub()" class="back-btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg> Kembali
            </a>
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <div class="fw-bold text-navy">Master Data Dropdown</div>
                        <div class="text-slate-400" style="font-size: 0.7rem;">Atur isi dropdown untuk form Input Laporan Harian.</div>
                    </div>
                    <div class="d-flex gap-2">
                        <select id="master-data-category" class="form-select form-select-sm rounded-3" style="width: auto; min-width: 160px;" onchange="loadMasterData()">
                            <option value="kelas_stasiun">Kelas Stasiun</option>
                            <option value="stasiun_monitor">Stasiun Monitor</option>
                            <option value="kode_negara">Negara</option>
                            <option value="administrasi_termonitor">Administrasi Termonitor</option>
                        </select>
                        <button class="btn btn-primary btn-sm rounded-3 px-3 d-flex align-items-center gap-2" onclick="showMasterDataModal()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            Tambah
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 custom-table" style="font-size: 0.8rem;">
                        <thead class="table-light text-slate-500">
                            <tr>
                                <th class="ps-4 py-2 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Nilai / Opsi</th>
                                <th class="py-2 text-center text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px; width: 120px;">Status</th>
                                <th class="pe-4 py-2 text-end text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px; width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="master-data-tbody">
                            <tr><td colspan="3" class="text-center py-4 text-slate-400">Memuat data...</td></tr>
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
                        <h5 class="fw-bold text-navy mb-0" id="master-data-modal-title" style="font-size: 1.1rem;">Tambah Opsi Baru</h5>
                        <button type="button" class="btn-close" onclick="hideMasterDataModal()"></button>
                    </div>
                    <form id="master-data-form" onsubmit="saveMasterData(event)">
                        <input type="hidden" id="md-id">
                        <input type="hidden" id="md-category">
                        
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Nilai / Nama Opsi <span class="text-danger">*</span></label>
                            <input type="text" id="md-value" class="form-control rounded-3" required placeholder="Contoh: MSHF LAMPUNG" style="text-transform: uppercase;">
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-dark rounded-3 fw-bold py-2" id="btn-save-md">Simpan Opsi</button>
                            <button type="button" class="btn btn-link text-slate-400 text-decoration-none small" onclick="hideMasterDataModal()">Batal</button>
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
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                </div>
                <h5 class="fw-bold text-navy mb-2" style="font-size: 1.1rem;">Hapus Akun Permanen?</h5>
                <p class="text-slate-500 mb-3" style="font-size: 0.825rem; line-height: 1.5;">Data akun, log aktivitas, dan hak akses Anda akan dihapus permanen dari sistem Balmon Lampung.</p>
                
                <div class="bg-danger bg-opacity-10 p-3 rounded-4 mb-4 text-start">
                    <label class="form-label text-danger fw-bold small mb-1" style="font-size: 0.75rem;">Konfirmasi Kata Sandi</label>
                    <div class="position-relative">
                        <input type="password" id="delete-confirm-password" class="form-control bg-white border-0 rounded-3 shadow-sm py-2 px-3 with-toggle" required placeholder="Masukkan sandi Anda" style="font-size: 0.85rem;">
                        <button type="button" class="password-toggle" onclick="togglePassword(this)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                    </div>
                </div>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-danger rounded-3 py-2 fw-bold" style="font-size: 0.85rem;" onclick="confirmDeleteSelf()">Ya, Hapus Akun</button>
                    <button type="button" class="btn btn-link text-slate-400 text-decoration-none small" onclick="hideDeleteAccountModal()">Batalkan</button>
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
                document.querySelectorAll('.view-container').forEach(v => v.style.display = 'none');
                
                // Tampilkan target view
                target.style.display = 'block';
                
                // Simpan posisi menu di memori sementara browser
                sessionStorage.setItem('active_settings_menu', viewName);
                
                // Scroll ke paling atas
                window.scrollTo(0, 0);

                // Auto-load data jika menu master data
                if (viewName === 'masterdata') {
                    loadMasterData();
                }
            }
        }

        function showSettingsHub() {
            document.querySelectorAll('.view-container').forEach(v => v.style.display = 'none');
            document.getElementById('settings-hub').style.display = 'block';
            
            // Hapus memori posisi menu
            sessionStorage.removeItem('active_settings_menu');
            
            window.scrollTo(0, 0);
        }

        // Jalankan otomatis saat halaman dimuat ulang (termasuk navigasi Livewire)
        function initSettingsNavigation() {
            const savedMenu = sessionStorage.getItem('active_settings_menu');
            const hub = document.getElementById('settings-hub');
            
            if (hub) {
                if (savedMenu) {
                    switchSettingsView(savedMenu);
                } else {
                    // Jika tidak ada memori menu, tampilkan hub utama
                    hub.style.display = 'block';
                }
            }
        }

        document.addEventListener('DOMContentLoaded', initSettingsNavigation);
        document.addEventListener('livewire:navigated', initSettingsNavigation);
        async function start2faSetup() {
            const { value: password } = await Swal.fire({
                title: 'Konfirmasi Password',
                html: `
                    <div class="text-slate-500 mb-3" style="font-size: 0.85rem;">Silakan masukkan password Anda untuk mengaktifkan 2FA.</div>
                    <div class="position-relative">
                        <input type="password" id="swal-password" class="swal2-input m-0 w-100 rounded-3" placeholder="Masukkan password Anda" style="font-size: 0.95rem; height: 3.2rem;">
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
                        Swal.showValidationMessage('Password wajib diisi');
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
                    document.getElementById('qr-modal').classList.add('active');
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Password salah.', iconColor: '#ef4444' });
                }
            } catch (e) { 
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan sistem.', iconColor: '#ef4444' }); 
            }
        }
        function hideQrModal() { document.getElementById('qr-modal').classList.remove('active'); }
        async function verify2faSetup() {
            const code = document.getElementById('setup-code').value;
            if(code.length !== 6) return Swal.fire({ icon: 'warning', title: 'Kode Tidak Lengkap', text: 'Masukkan 6 digit kode.', iconColor: '#f59e0b' });
            try {
                const response = await fetch('{{ route("2fa.enable") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ code }) });
                const data = await response.json();
                if(data.success) {
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
            try {
                const response = await fetch('{{ route("2fa.toggle") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ enabled }) });
                const data = await response.json();
                if(!data.success) {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message, iconColor: '#ef4444' });
                    setTimeout(() => location.reload(), 2000);
                } else {
                    location.reload();
                }
            } catch (e) { 
                Swal.fire({ icon: 'error', title: 'Kesalahan', text: 'Terjadi kesalahan.', iconColor: '#ef4444' });
                setTimeout(() => location.reload(), 2000);
            }
        }
        async function reset2fa() {
            const { value: password } = await Swal.fire({
                title: 'Konfirmasi Password',
                html: `
                    <div class="text-slate-500 mb-2" style="font-size: 0.85rem;">Silakan masukkan password Anda untuk mereset 2FA.</div>
                    <div class="text-danger mb-3" style="font-size: 0.75rem; font-weight: 600;">Seluruh perangkat authenticator akan diputus.</div>
                    <div class="position-relative">
                        <input type="password" id="swal-password-reset" class="swal2-input m-0 w-100 rounded-3" placeholder="Masukkan password Anda" style="font-size: 0.95rem; height: 3.2rem;">
                        <button type="button" class="password-toggle" onclick="togglePassword(this)" style="right: 1.25rem; top: 50%; transform: translateY(-50%); border: none; background: transparent; padding: 0;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon" style="color: #94a3b8;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Reset Sekarang',
                cancelButtonText: 'Batal',
                icon: 'warning',
                iconColor: '#ef4444',
                preConfirm: () => {
                    const pass = document.getElementById('swal-password-reset').value;
                    if (!pass) {
                        Swal.showValidationMessage('Password wajib diisi');
                    }
                    return pass;
                }
            });

            if (!password) return;

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
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: '2FA berhasil direset.', iconColor: '#10b981' }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Gagal mereset 2FA.', iconColor: '#ef4444' });
                }
            } catch (e) { 
                Swal.fire({ icon: 'error', title: 'Kesalahan', text: 'Terjadi kesalahan sistem.', iconColor: '#ef4444' });
            }
        }
        function previewProfilePhoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profile-preview-settings').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        async function updateSecurity(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-update-security');
            const form = e.target;
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
                
                if(response.ok) {
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 2000, showConfirmButton: false, iconColor: '#10b981' });
                    setTimeout(() => location.reload(), 2000);
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
        .btn-blue { background: #2563eb !important; color: white !important; }
        .bg-blue-50 { background-color: #eff6ff !important; }
        .text-blue-600 { color: #2563eb !important; }
    </style>

    <script>
        function showUserModal(id = null, name = '', email = '', role = 'admin') {
            document.getElementById('user-id').value = id || '';
            document.getElementById('user-name').value = name;
            document.getElementById('user-email').value = email;
            document.getElementById('user-role').value = role;
            document.getElementById('user-password').value = '';
            
            // Tampilkan kolom nama jika:
            // 1. Sedang EDIT (id ada)
            // 2. Atau jika sedang TAMBAH tapi rolenya BUKAN super_admin
            const container = document.getElementById('container-user-name');
            const input = document.getElementById('user-name');
            if (id || role !== 'super_admin') {
                container.style.display = 'block';
                input.required = true;
                if (input.value === 'AUTO_GENERATED_SA') input.value = '';
            } else {
                container.style.display = 'none';
                input.required = false;
                input.value = 'AUTO_GENERATED_SA';
            }

            // Jika sedang edit Super Admin (bukan diri sendiri), kunci SEMUA field agar tidak bisa diubah sesama SA
            const roleSelect = document.getElementById('user-role');
            const nameInput = document.getElementById('user-name');
            const emailInput = document.getElementById('user-email');
            const passwordInput = document.getElementById('user-password');
            const passwordContainer = document.getElementById('container-user-password');
            const resetLinkContainer = document.getElementById('reset-link-container');

            const btnSave = document.getElementById('btn-save-user');
            if (id && role === 'super_admin' && id != '{{ auth()->id() }}') {
                roleSelect.disabled = true;
                nameInput.disabled = true;
                emailInput.disabled = true;
                passwordContainer.style.display = 'none';
                resetLinkContainer.style.display = 'block';
                btnSave.style.display = 'none';
            } else {
                roleSelect.disabled = false;
                nameInput.disabled = false;
                emailInput.disabled = false;
                passwordContainer.style.display = 'block';
                resetLinkContainer.style.display = 'none';
                btnSave.style.display = 'block';
                passwordInput.placeholder = id ? 'Minimal 8 karakter (opsional)' : 'Minimal 8 karakter';
            }
            
            if (id) {
                document.getElementById('user-modal-title').textContent = 'Edit Anggota Tim';
                document.getElementById('btn-save-user').textContent = 'Perbarui Anggota';
                document.getElementById('password-hint').style.display = 'block';
                document.getElementById('password-asterisk').style.display = 'none';
                document.getElementById('user-password').required = false;
            } else {
                document.getElementById('user-modal-title').textContent = 'Tambah Anggota Tim';
                document.getElementById('btn-save-user').textContent = 'Simpan Anggota';
                document.getElementById('password-hint').style.display = 'none';
                document.getElementById('password-asterisk').style.display = 'inline';
                document.getElementById('user-password').required = true;
            }
            
            document.getElementById('user-modal').classList.add('active');
        }

        function hideUserModal() { document.getElementById('user-modal').classList.remove('active'); }

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
            window.confirmSistem('Hapus Anggota', 'Apakah Anda yakin ingin menghapus anggota ini?', async function() {
            
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

            window.confirmSistem(title, text, async function() {
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
                return Swal.fire({ icon: 'warning', title: 'Password Wajib', text: 'Silakan masukkan password Anda untuk konfirmasi.', iconColor: '#f59e0b' });
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
        async function loadMasterData() {
            const category = document.getElementById('master-data-category').value;
            const tbody = document.getElementById('master-data-tbody');
            tbody.innerHTML = '<tr><td colspan="3" class="text-center py-4 text-slate-400">Memuat data...</td></tr>';

            try {
                const response = await fetch(`{{ route('master-data.index') }}?category=${category}`, {
                    headers: { 'Accept': 'application/json' }
                });

                if (!response.ok) throw new Error('Network response was not ok');
                
                const data = await response.json();

                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="3" class="text-center py-4 text-slate-400">Belum ada opsi untuk kategori ini.</td></tr>';
                    return;
                }

                tbody.innerHTML = '';
                data.forEach(item => {
                    const statusBadge = item.is_active 
                        ? '<span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill" style="font-size: 0.65rem;">AKTIF</span>'
                        : '<span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1 rounded-pill" style="font-size: 0.65rem;">NONAKTIF</span>';
                    
                    const toggleBtnClass = item.is_active ? 'btn-outline-warning' : 'btn-outline-success';
                    const toggleBtnTitle = item.is_active ? 'Nonaktifkan' : 'Aktifkan';

                    tbody.innerHTML += `
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
            } catch (error) {
                tbody.innerHTML = `<tr><td colspan="3" class="text-center py-4 text-danger">Gagal memuat data: ${error.message}</td></tr>`;
            }
        }

        function showMasterDataModal(id = null, value = '') {
            const categorySelect = document.getElementById('master-data-category');
            const category = categorySelect.value;
            const inputVal = document.getElementById('md-value');
            
            document.getElementById('md-id').value = id || '';
            inputVal.value = value;
            document.getElementById('md-category').value = category;
            
            // Set Placeholder dinamis berdasarkan kategori
            let placeholder = 'Contoh: MSHF LAMPUNG';
            if (category === 'kelas_stasiun') placeholder = 'CONTOH: AL, AM, ATAU AT';
            else if (category === 'kode_negara') placeholder = 'CONTOH: INDONESIA (INS)';
            else if (category === 'administrasi_termonitor') placeholder = 'CONTOH: INS';
            
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
            window.confirmSistem('Hapus Opsi', 'Apakah Anda yakin ingin menghapus opsi ini? Jika data ini pernah dipakai, lebih baik di-nonaktifkan saja.', async function() {
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
        
        // Panggil loadMasterData otomatis saat view masterdata aktif (termasuk navigasi Livewire)
        function checkMasterDataAutoLoad() {
            if (sessionStorage.getItem('active_settings_menu') === 'masterdata') {
                loadMasterData();
            }
        }

        document.addEventListener('DOMContentLoaded', checkMasterDataAutoLoad);
        document.addEventListener('livewire:navigated', checkMasterDataAutoLoad);

    </script>
@endsection