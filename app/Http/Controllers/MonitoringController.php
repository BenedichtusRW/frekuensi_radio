<?php

namespace App\Http\Controllers;

use App\Exports\MonitoringLogbookExport;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Monitoring;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

use App\Traits\LogsActivity;

class MonitoringController extends Controller
{
    use LogsActivity;
    private const XLSX_ARCHIVE_DIR = 'exports/xlsx';

    public function dashboard(Request $request)
    {
        $this->logActivity($request, 'visit_dashboard', 'Membuka halaman Dashboard');

        $user = auth()->user();
        $isSuperAdmin = $user->role === 'super_admin';
        
        // --- IMPROVED: GLOBAL SESSION-BASED FILTER ---
        if ($isSuperAdmin) {
            // Priority 1: Request (user sets a new filter)
            if ($request->has('user_id')) {
                $val = $request->query('user_id');
                if ($val === "" || $val === "all") {
                    session()->forget('global_petugas_filter_id');
                } else {
                    session(['global_petugas_filter_id' => $val]);
                }
                
                // If not an AJAX/Livewire request, redirect to clean URL
                if (!$request->ajax() && !$request->headers->has('X-Livewire')) {
                    return redirect()->route('dashboard');
                }
            }
            
            // Priority 2: Session (read existing filter)
            $userId = session('global_petugas_filter_id');
        } else {
            $userId = $user->id;
        }
        // ----------------------------------------------------

        $cacheDuration = now()->addMinutes(10);
        $cacheSuffix = $userId ? "_user_{$userId}" : "_all";

        // 1. CACHE SUMMARY STATS (Bulan Ini + All Time)
        $summary = Cache::remember('dashboard_summary_stats' . $cacheSuffix, $cacheDuration, function () use ($userId) {
            $currentMonth = now()->month;
            $currentYear = now()->year;

            $monthlySummary = Monitoring::query()
                ->selectRaw('COUNT(*) as total')
                ->selectRaw("SUM(CASE WHEN kategori = 'MF' THEN 1 ELSE 0 END) as mf")
                ->selectRaw("SUM(CASE WHEN kategori = 'HF Rutin' THEN 1 ELSE 0 END) as rutin")
                ->selectRaw("SUM(CASE WHEN kategori = 'HF Nelayan' THEN 1 ELSE 0 END) as nelayan")
                ->where('tahun', $currentYear)
                ->where('bulan', $currentMonth)
                ->when($userId, fn($q) => $q->where('user_id', $userId))
                ->first();

            $allSummary = Monitoring::query()
                ->selectRaw('COUNT(*) as total')
                ->selectRaw("SUM(CASE WHEN kategori = 'MF' THEN 1 ELSE 0 END) as mf")
                ->selectRaw("SUM(CASE WHEN kategori = 'HF Rutin' THEN 1 ELSE 0 END) as rutin")
                ->selectRaw("SUM(CASE WHEN kategori = 'HF Nelayan' THEN 1 ELSE 0 END) as nelayan")
                ->when($userId, fn($q) => $q->where('user_id', $userId))
                ->first();

            return [
                // Bulan ini
                'month_label'   => now()->translatedFormat('F Y'),
                'total_month'   => (int) ($monthlySummary->total ?? 0),
                'mf_month'      => (int) ($monthlySummary->mf ?? 0),
                'rutin_month'   => (int) ($monthlySummary->rutin ?? 0),
                'nelayan_month' => (int) ($monthlySummary->nelayan ?? 0),
                // All-time (total keseluruhan)
                'total_all'   => (int) ($allSummary->total ?? 0),
                'mf_all'      => (int) ($allSummary->mf ?? 0),
                'rutin_all'   => (int) ($allSummary->rutin ?? 0),
                'nelayan_all' => (int) ($allSummary->nelayan ?? 0),
            ];
        });

        // 2. PIE CHART - All Time Data
        $pieChart = [
            'labels' => ['MF', 'HF Rutin', 'HF Nelayan'],
            'values' => [
                $summary['mf_all'],
                $summary['rutin_all'],
                $summary['nelayan_all'],
            ],
        ];

        // 3. CACHE BAR CHART 7 HARI (Stacked by Category)
        $barChart = Cache::remember('dashboard_bar_chart' . $cacheSuffix, $cacheDuration, function () use ($userId) {
            $startDate = now()->subDays(6)->startOfDay();
            $endDate   = now()->endOfDay();

            // Fetch counts grouped by date and category
            $rows = Monitoring::query()
                ->selectRaw('tanggal, bulan, tahun, kategori, COUNT(*) as total')
                ->where('tahun', '>=', (int) $startDate->year)
                ->whereBetween('bulan', [(int) $startDate->month, (int) $endDate->month])
                ->when($userId, fn($q) => $q->where('user_id', $userId))
                ->groupBy('tahun', 'bulan', 'tanggal', 'kategori')
                ->get();

            $weekLabels = [];
            $datasets = [
                'mf'      => [],
                'rutin'   => [],
                'nelayan' => []
            ];

            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $label = $date->format('d M');
                $weekLabels[] = $label;
                
                $keyDate = (int) $date->day;
                $keyMonth = (int) $date->month;
                $keyYear = (int) $date->year;

                $dayData = $rows->filter(function($r) use ($keyDate, $keyMonth, $keyYear) {
                    return $r->tanggal == $keyDate && $r->bulan == $keyMonth && $r->tahun == $keyYear;
                });

                $datasets['mf'][]      = (int) ($dayData->where('kategori', 'MF')->first()?->total ?? 0);
                $datasets['rutin'][]   = (int) ($dayData->where('kategori', 'HF Rutin')->first()?->total ?? 0);
                $datasets['nelayan'][] = (int) ($dayData->where('kategori', 'HF Nelayan')->first()?->total ?? 0);
            }

            return ['labels' => $weekLabels, 'datasets' => $datasets];
        });

        // 4. CACHE MONTHLY CHART (Stacked by Category)
        $monthlyChart = Cache::remember('dashboard_monthly_chart' . $cacheSuffix, $cacheDuration, function () use ($userId) {
            $currentYear = now()->year;

            $rows = Monitoring::query()
                ->selectRaw('bulan, kategori, COUNT(*) as total')
                ->where('tahun', $currentYear)
                ->when($userId, fn($q) => $q->where('user_id', $userId))
                ->groupBy('bulan', 'kategori')
                ->get();

            $monthLabels = [];
            $datasets = [
                'mf'      => [],
                'rutin'   => [],
                'nelayan' => []
            ];
            $monthNames = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

            for ($m = 1; $m <= 12; $m++) {
                $monthLabels[] = $monthNames[$m - 1];
                
                $monthData = $rows->where('bulan', $m);
                
                $datasets['mf'][]      = (int) ($monthData->where('kategori', 'MF')->first()?->total ?? 0);
                $datasets['rutin'][]   = (int) ($monthData->where('kategori', 'HF Rutin')->first()?->total ?? 0);
                $datasets['nelayan'][] = (int) ($monthData->where('kategori', 'HF Nelayan')->first()?->total ?? 0);
            }

            return [
                'labels'   => $monthLabels,
                'datasets' => $datasets,
                'year'     => $currentYear,
            ];
        });

        // 5. RECENT MONITORING DATA (5 terakhir) - Tanpa cache agar benar-benar real-time saat halaman di-refresh
        $recentMonitoring = Monitoring::query()
            ->with('user:id,name') // Eager load petugas
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->get(['id', 'user_id', 'kategori', 'tahun', 'bulan', 'tanggal', 'created_at']);

        // Fetch users for Super Admin filter dropdown
        $users = [];
        if ($isSuperAdmin) {
            $users = \App\Models\User::where('role', '!=', 'super_admin')
                ->orderBy('is_active', 'DESC')
                ->orderBy('name', 'ASC')
                ->get()
                ->map(function($u) {
                    $u->name = $u->is_active ? $u->name : $u->name . ' (Nonaktif)';
                    return $u;
                });
        }

        return view('dashboard', [
            'summary'            => $summary,
            'pieChart'           => $pieChart,
            'barChart'           => $barChart,
            'monthlyChart'       => $monthlyChart,
            'recentMonitoring'   => $recentMonitoring,
            'users'              => $users,
            'selectedUserId'     => $userId,
        ]);
    }

    public function settings(Request $request)
    {
        $this->logActivity($request, 'visit_settings', 'Membuka halaman Pengaturan');
        
        $activityLogs = [];
        if (auth()->user()->role === 'super_admin') {
            $activityLogs = ActivityLog::latest()->limit(20)->get();
        }
        
        return view('settings', [
            'activityLogs' => $activityLogs
        ]);
    }

    /**
     * Generate QR Code for 2FA Setup
     */
    public function generate2faQr(Request $request)
    {
        $request->validate([
            'password' => 'required'
        ]);

        $user = Auth::user();

        if (!\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Password salah.'], 422);
        }

        $google2fa = new \PragmaRX\Google2FA\Google2FA();

        // Generate a new secret key
        $secretKey = $google2fa->generateSecretKey();
        
        // Temporarily store secret in session for verification step
        session(['2fa_pending_secret' => $secretKey]);

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            'Portal Monitoring',
            $user->email,
            $secretKey
        );

        $renderer = new \BaconQrCode\Renderer\Image\SvgImageBackEnd();
        $writer = new \BaconQrCode\Writer(new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(200),
            $renderer
        ));

        $qrCodeSvg = $writer->writeString($qrCodeUrl);

        return response()->json([
            'svg' => $qrCodeSvg,
            'secret' => $secretKey
        ]);
    }

    /**
     * Verify and Enable 2FA for the first time
     */
    public function enable2fa(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6'
        ]);

        $user = Auth::user();
        $secret = session('2fa_pending_secret');

        if (!$secret) {
            return response()->json(['success' => false, 'message' => 'Sesi setup kadaluarsa. Silakan coba lagi.'], 400);
        }

        $google2fa = new \PragmaRX\Google2FA\Google2FA();
        $valid = $google2fa->verifyKey($secret, $request->code);

        if ($valid) {
            $user->google2fa_secret = $secret;
            $user->two_factor_enabled = true;
            $user->save();

            session()->forget('2fa_pending_secret');
            
            $this->logActivity($request, 'enable_2fa', 'Mengaktifkan Autentikasi 2 Faktor');

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Kode verifikasi salah.'], 422);
    }

    /**
     * Toggle 2FA status (On/Off)
     */
    public function toggle2fa(Request $request)
    {
        $request->validate([
            'enabled' => 'required|boolean'
        ]);

        $user = Auth::user();
        
        if (empty($user->google2fa_secret)) {
            return response()->json(['success' => false, 'message' => 'Harap lakukan setup 2FA terlebih dahulu.'], 400);
        }

        $user->two_factor_enabled = $request->enabled;
        $user->save();

        $status = $request->enabled ? 'Mengaktifkan' : 'Menonaktifkan';
        $this->logActivity($request, 'toggle_2fa', $status . ' Autentikasi 2 Faktor');

        $response = response()->json(['success' => true]);
        
        // Jika dimatikan, hapus cookie preferensi login agar kembali ke email/password
        if (!$request->enabled) {
            $response->withCookie(cookie()->forget('login_preference'));
        } else {
            // Jika dinyalakan, tanamkan cookie 2fa
            $response->withCookie(cookie()->forever('login_preference', '2fa'));
        }

        return $response;
    }

    /**
     * Reset 2FA — clears secret so user can re-scan a new QR code
     */
    public function reset2fa(Request $request)
    {
        $request->validate([
            'password' => 'required'
        ]);

        $user = Auth::user();
        
        if (!\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Konfirmasi password salah.'], 422);
        }

        $user->google2fa_secret = null;
        $user->two_factor_enabled = false;
        $user->save();

        $this->logActivity($request, 'reset_2fa', 'Mereset Autentikasi 2 Faktor');

        return response()->json(['success' => true]);
    }

    /**
     * Update Password & Profil
     */
    public function updateSecurity(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^(?!Admin\s*\d*$).+/i'],
            'email' => ['required', 'email', \Illuminate\Validation\Rule::unique('users')->ignore($user->id)],
            'current_password' => 'required',
            'new_password' => [
                'nullable',
                'confirmed',
                \Illuminate\Validation\Rules\Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->uncompromised(),
                new \App\Rules\StrongPassword
            ],
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
        ], [
            'name.regex' => 'Mohon gunakan nama asli Anda.',
            'email.unique' => 'Email sudah digunakan oleh akun lain.',
            'new_password.min' => 'Password baru minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok.',
            'new_password.uncompromised' => 'Kata sandi ini terdeteksi pernah bocor di internet. Silakan gunakan kata sandi lain demi keamanan Anda.',
            'new_password.mixed' => 'Password baru harus mengandung campuran huruf besar dan kecil.',
            'new_password.numbers' => 'Password baru harus mengandung angka.',
            'profile_photo.image' => 'File harus berupa gambar.',
            'profile_photo.max' => 'Ukuran foto maksimal 1MB.',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Kata sandi saat ini salah.'
            ], 422);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        
        // Handle Profile Photo Update
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->profile_photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_photo);
            }
            // Store new photo
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->profile_photo = $path;
        }

        if ($request->filled('new_password')) {
            $user->password = Hash::make($request->new_password);
            $action = 'change_security_profile';
            $desc = 'Mengubah profil, email, dan kata sandi akun';
        } else {
            $action = 'change_profile';
            $desc = 'Mengubah informasi profil dan email';
        }
        
        $user->save();

        $this->logActivity($request, $action, $desc);

        return response()->json([
            'success' => true,
            'message' => 'Profil dan keamanan berhasil diperbarui.'
        ]);
    }

    public function create()
    {
        return view('input', [
            'dropdownOptions' => $this->buildDropdownOptions(),
        ]);
    }



    public function index(Request $request)
    {
        $this->logActivity($request, 'visit_laporan', 'Membuka halaman Daftar Laporan');

        $isSuperAdmin = auth()->user()->role === 'super_admin';
        
        // --- GLOBAL FILTER SYNC ---
        if ($isSuperAdmin) {
            if ($request->has('user_id')) {
                $val = $request->query('user_id');
                if ($val === "" || $val === "all") {
                    session()->forget('global_petugas_filter_id');
                } else {
                    session(['global_petugas_filter_id' => $val]);
                }
            }
        }

        $filters = $this->extractMonitoringFilters($request);
        
        // If no user_id in request, try session for Super Admin
        if ($isSuperAdmin && empty($filters['user_id'])) {
            $filters['user_id'] = session('global_petugas_filter_id');
        }

        $editMonitoring = null;
        $editTableNumber = $this->toNullableInt((string) $request->query('no', ''));

        if ($request->filled('edit_id')) {
            $editMonitoring = Monitoring::find((int) $request->query('edit_id'));
            if ($editMonitoring) {
                $this->authorizeMonitoring($editMonitoring);
            }
        }

        $perPage = $this->toNullableInt((string) $request->query('per_page', '10')) ?? 10;
        // Safety cap: Maksimal 50 data per halaman agar browser tidak berat (karena kolom sangat banyak)
        if ($perPage > 50) $perPage = 50;
        if ($perPage < 1) $perPage = 10;

        $monitorings = $this->monitoringFilteredQuery($filters)
            ->paginate($perPage)
            ->withQueryString();

        // AJAX Optimization: Return ONLY the table partial, not the full layout
        if ($request->ajax()) {
            return view('partials.laporan-table', [
                'monitorings' => $monitorings,
                'editMonitoring' => $editMonitoring,
                'editTableNumber' => $editTableNumber,
            ]);
        }

        $users = [];
        if (auth()->user()->role === 'super_admin') {
            $users = \App\Models\User::orderBy('name')->get(['id', 'name', 'role']);
        }

        return view('laporan', [
            'monitorings' => $monitorings,
            'filters' => $filters,
            'pageTitle' => 'Daftar Laporan',
            'dropdownOptions' => $this->buildDropdownOptions(),
            'editMonitoring' => $editMonitoring,
            'users' => $users,
            'editTableNumber' => $editTableNumber,
        ]);
    }

    public function exportLaporan(Request $request)
    {
        $filters = $this->extractMonitoringFilters($request);
        $filterDesc = $this->formatFilterDescription($filters);
        $this->logActivity($request, 'export_xlsx', 'Export data laporan ke XLSX' . ($filterDesc ? ' berdasarkan ' . $filterDesc : ''));

        ini_set('max_execution_time', 300);
        $filters = $this->extractMonitoringFilters($request);
        $rows = $this->monitoringFilteredQuery($filters)->get();
        $filename = $this->buildExportFilename($filters, 'xlsx');

        return Excel::download(new MonitoringLogbookExport($rows), $filename);
    }

    private function buildExportFilename(array $filters, string $extension): string
    {
        $parts = ['logbook'];

        $kategori = trim((string) ($filters['kategori'] ?? ''));
        if ($kategori === '') {
            $parts[] = 'All';
        } else {
            $safeKategori = preg_replace('/[\\\\\/:*?"<>|]+/', ' ', $kategori) ?? $kategori;
            $parts[] = str_replace(' ', '_', trim($safeKategori));
        }

        $datePart = $this->buildCsvDatePart($filters);
        if ($datePart !== null) {
            $parts[] = $datePart;
        }

        $parts[] = now()->format('Ymd');

        return implode('_', $parts) . '.' . $extension;
    }


    private function buildCsvDatePart(array $filters): ?string
    {
        $tanggalLengkap = trim((string) ($filters['tanggal_lengkap'] ?? ''));
        if ($tanggalLengkap !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalLengkap)) {
            return str_replace('-', '', $tanggalLengkap);
        }

        $tanggal = trim((string) ($filters['tanggal'] ?? ''));
        $bulan = trim((string) ($filters['bulan'] ?? ''));
        $tahun = trim((string) ($filters['tahun'] ?? ''));

        // If all date parts are present, use compact full date token: YYYYMMDD.
        if ($tanggal !== '' && $bulan !== '' && $tahun !== '') {
            return str_pad($tahun, 4, '0', STR_PAD_LEFT)
                . str_pad($bulan, 2, '0', STR_PAD_LEFT)
                . str_pad($tanggal, 2, '0', STR_PAD_LEFT);
        }

        $parts = [];
        if ($tanggal !== '') {
            $parts[] = 'tgl' . str_pad($tanggal, 2, '0', STR_PAD_LEFT);
        }
        if ($bulan !== '') {
            $parts[] = 'bln' . str_pad($bulan, 2, '0', STR_PAD_LEFT);
        }
        if ($tahun !== '') {
            $parts[] = 'thn' . $tahun;
        }

        if (empty($parts)) {
            return null;
        }

        return implode('_', $parts);
    }



    public function store(Request $request)
    {
        $request->validate([
            'kategori' => ['required', 'string', 'in:MF,HF Rutin,HF Nelayan'],
            'kode_negara' => ['required', 'string', 'max:10'],
            'stasiun_monitor' => ['required', 'string', 'max:255'],
            'frekuensi_khz' => ['required', 'numeric'],
            'mulai_pengamatan' => ['required', 'date_format:Y-m-d\TH:i'],
            'selesai_pengamatan' => ['nullable', 'date_format:Y-m-d\TH:i'],
            'selesai_pengamatan_waktu' => ['nullable', 'date_format:H:i'],
            'identifikasi' => ['required', 'string', 'max:255'],
            'kelas_stasiun' => ['required', 'string', 'max:50'],
            'lebar_band' => ['required', 'string', 'max:50'],
            'kelas_emisi' => ['required', 'string', 'max:50'],
        ]);

        $mulaiInput = (string) $request->input('mulai_pengamatan', '');
        $selesaiInput = (string) $request->input('selesai_pengamatan', '');
        $selesaiWaktuInput = (string) $request->input('selesai_pengamatan_waktu', '');

        if ($selesaiInput === '' && $selesaiWaktuInput !== '' && str_contains($mulaiInput, 'T')) {
            $tanggalMulai = explode('T', $mulaiInput)[0];
            $selesaiInput = $tanggalMulai . 'T' . $selesaiWaktuInput;
            $request->merge(['selesai_pengamatan' => $selesaiInput]);
        }

        if ($selesaiInput === '') {
            return redirect()
                ->back()
                ->withErrors(['selesai_pengamatan_waktu' => 'Selesai Pengamatan wajib diisi.'])
                ->withInput();
        }

        $mulaiPengamatan = Carbon::createFromFormat('Y-m-d\TH:i', (string) $request->input('mulai_pengamatan'));
        $selesaiPengamatan = Carbon::createFromFormat('Y-m-d\TH:i', (string) $request->input('selesai_pengamatan'));

        if ($selesaiPengamatan->lt($mulaiPengamatan)) {
            return redirect()
                ->back()
                ->withErrors(['selesai_pengamatan' => 'Selesai Pengamatan harus sama atau lebih besar dari Mulai Pengamatan.'])
                ->withInput();
        }

        $request->merge([
            'tanggal' => (int) $mulaiPengamatan->day,
            'bulan' => (int) $mulaiPengamatan->month,
            'tahun' => (int) $mulaiPengamatan->year,
            'jam_mulai' => $mulaiPengamatan->format('H.i'),
            'jam_akhir' => $selesaiPengamatan->format('H.i'),
        ]);

        $validated = $request->validate($this->monitoringValidationRules());

        // Sanitasi agresif pencegahan XSS (Hardening)
        foreach ($validated as $key => $value) {
            if (is_string($value)) {
                $validated[$key] = htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
            }
        }

        $validated = $this->normalizeNumericFields($validated);
        
        // Jika Super Admin menginput atas nama orang lain
        if (auth()->user()->role === 'super_admin' && $request->filled('user_id')) {
            $validated['user_id'] = $request->input('user_id');
        } else {
            $validated['user_id'] = Auth::id();
        }

        // PENCEGAHAN DUPLIKASI DOUBLE POST (Safety Net)
        $fiveMinutesAgo = now()->subMinutes(5);
        $isDuplicate = Monitoring::query()
            ->where('frekuensi_khz', $validated['frekuensi_khz'])
            ->where('tanggal', $validated['tanggal'])
            ->where('bulan', $validated['bulan'])
            ->where('tahun', $validated['tahun'])
            ->where('stasiun_monitor', $validated['stasiun_monitor'])
            ->where('created_at', '>=', $fiveMinutesAgo)
            ->exists();

        if ($isDuplicate) {
            return redirect()
                ->back()
                ->withErrors(['frekuensi_khz' => 'Data serupa sudah diinput sebelumnya dalam 5 menit terakhir. Mohon jangan klik tautan berkali-kali.'])
                ->withInput();
        }

        Monitoring::create($validated);

        // CLEAR DASHBOARD CACHE: Force real-time update
        $this->clearDashboardCache();

        $this->logActivity($request, 'add_data', 'Tambah data baru: Frekuensi ' . ($validated['frekuensi_khz'] ?? '-') . ' kHz, Kategori: ' . ($validated['kategori'] ?? '-'));

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data laporan berhasil disimpan.'
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Data monitoring berhasil disimpan.');
    }

    /**
     * Clear all dashboard-related caches to reflect new data.
     */
    private function clearDashboardCache(): void
    {
        $baseKeys = [
            'dashboard_summary_stats',
            'dashboard_pie_chart',
            'dashboard_bar_chart',
            'dashboard_monthly_chart',
            'dashboard_recent_monitoring'
        ];

        foreach ($baseKeys as $key) {
            // Clear current user cache
            Cache::forget($key . "_user_" . auth()->id());
            // Clear "All" cache
            Cache::forget($key . "_all");
            // Clear the legacy non-suffixed keys
            Cache::forget($key);
            
            // If Super Admin is making changes, we might need to clear the filtered user's cache too.
            // But since we clear "_all", the main dashboard will be refreshed anyway.
        }
    }

    public function edit(Request $request, int $id)
    {
        $monitoring = Monitoring::findOrFail($id);
        $this->authorizeMonitoring($monitoring);

        $editTableNumber = $this->toNullableInt((string) $request->query('no', ''));

        return redirect()
            ->route('monitoring.index', [
                'edit_id' => $id,
                'no' => $editTableNumber,
            ]);
    }

    public function update(Request $request, int $id)
    {
        $monitoring = Monitoring::findOrFail($id);
        $this->authorizeMonitoring($monitoring);

        $mulaiPengamatanInput = (string) $request->input('mulai_pengamatan', '');
        $selesaiPengamatanWaktuInput = (string) $request->input('selesai_pengamatan_waktu', '');

        if ($mulaiPengamatanInput !== '' || $selesaiPengamatanWaktuInput !== '') {
            $request->validate([
                'mulai_pengamatan' => ['required', 'date_format:Y-m-d\TH:i'],
                'selesai_pengamatan_waktu' => ['required', 'date_format:H:i'],
            ]);

            $mulaiPengamatan = Carbon::createFromFormat('Y-m-d\TH:i', $mulaiPengamatanInput);
            $selesaiPengamatan = Carbon::createFromFormat(
                'Y-m-d\TH:i',
                $mulaiPengamatan->format('Y-m-d') . 'T' . $selesaiPengamatanWaktuInput
            );

            if ($selesaiPengamatan->lt($mulaiPengamatan)) {
                return redirect()
                    ->back()
                    ->withErrors(['selesai_pengamatan_waktu' => 'Selesai Pengamatan harus sama atau lebih besar dari Mulai Pengamatan.'])
                    ->withInput();
            }

            $request->merge([
                'tanggal' => (int) $mulaiPengamatan->day,
                'bulan' => (int) $mulaiPengamatan->month,
                'tahun' => (int) $mulaiPengamatan->year,
                'jam_mulai' => $mulaiPengamatan->format('H.i'),
                'jam_akhir' => $selesaiPengamatan->format('H.i'),
            ]);
        }

        $jamMulai = (string) $request->input('jam_mulai', '');
        $jamAkhir = (string) $request->input('jam_akhir', '');

        $jamMulaiMinutes = $this->toMinutesFromJam($jamMulai);
        $jamAkhirMinutes = $this->toMinutesFromJam($jamAkhir);

        if ($jamMulaiMinutes !== null && $jamAkhirMinutes !== null && $jamAkhirMinutes < $jamMulaiMinutes) {
            return redirect()
                ->back()
                ->withErrors(['jam_akhir' => 'Selesai Pengamatan harus sama atau lebih besar dari Mulai Pengamatan.'])
                ->withInput();
        }

        if ($jamMulai !== '' && str_contains($jamMulai, ':')) {
            $request->merge(['jam_mulai' => str_replace(':', '.', $jamMulai)]);
        }

        if ($jamAkhir !== '' && str_contains($jamAkhir, ':')) {
            $request->merge(['jam_akhir' => str_replace(':', '.', $jamAkhir)]);
        }

        $validated = $request->validate($this->monitoringValidationRules());

        // Sanitasi agresif pencegahan XSS (Hardening)
        foreach ($validated as $key => $value) {
            if (is_string($value)) {
                $validated[$key] = htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
            }
        }

        $validated = $this->normalizeNumericFields($validated);

        $monitoring->update($validated);

        // CLEAR DASHBOARD CACHE: Force real-time update
        $this->clearDashboardCache();

        $this->logActivity($request, 'edit_data', 'Edit data ID #' . $id . ': Frekuensi ' . ($validated['frekuensi_khz'] ?? '-') . ' kHz');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data laporan berhasil diperbarui.'
            ]);
        }

        $editTableNumber = $this->toNullableInt((string) $request->input('edit_table_no', ''));

        return redirect()
            ->route('monitoring.index')
            ->with('success', 'Data monitoring berhasil diperbarui.');
    }

    public function destroy(Request $request, int $id)
    {
        $monitoring = Monitoring::findOrFail($id);
        $this->authorizeMonitoring($monitoring);
        
        $monitoring->delete();

        // CLEAR DASHBOARD CACHE: Force real-time update
        $this->clearDashboardCache();

        $this->logActivity($request, 'delete_data', 'Menghapus data laporan ID #' . $id);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data monitoring berhasil dihapus.'
            ]);
        }

        return redirect()
            ->route('monitoring.index')
            ->with('success', 'Data monitoring berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih.');
        }

        $query = Monitoring::whereIn('id', $ids);
        if (auth()->user()->role !== 'super_admin') {
            $query->where('user_id', auth()->id());
        }
        $query->delete();
        
        $this->clearDashboardCache();
        $this->logActivity($request, 'bulk_delete', 'Menghapus massal ' . count($ids) . ' data laporan');

        return redirect()->route('monitoring.index')->with('success', 'Data terpilih berhasil dihapus.');
    }

    public function deleteAll(Request $request)
    {
        // SECURITY: Only super_admin can delete all data
        if (auth()->user()->role !== 'super_admin') {
            $this->logActivity($request, 'unauthorized_access', 'Percobaan hapus semua data oleh non-super_admin');
            return redirect()->back()->with('error', 'Hanya Super Admin yang diizinkan menghapus seluruh data.');
        }

        $filters = $this->extractMonitoringFilters($request);
        $filterDesc = $this->formatFilterDescription($filters);
        $query = $this->monitoringFilteredQuery($filters);
        
        $count = $query->count();
        if ($count === 0) {
            return redirect()->back()->with('error', 'Tidak ada data untuk dihapus.');
        }

        $query->delete();
        
        $this->clearDashboardCache();
        $this->logActivity($request, 'delete_all', 'Menghapus SEMUA data laporan (' . $count . ' data)' . ($filterDesc ? ' berdasarkan ' . $filterDesc : ''));

        return redirect()->route('monitoring.index')->with('success', 'Semua data (' . $count . ') berhasil dihapus.');
    }

    public function downloadBackup(Request $request)
    {
        // SECURITY: Only super_admin can backup
        if (auth()->user()->role !== 'super_admin') {
            abort(403);
        }

        $this->logActivity($request, 'database_backup', 'Melakukan backup database lengkap');

        $tables = DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database');
        $tablesField = 'Tables_in_' . $dbName;
        
        $sqlDump = "-- Database Backup\n";
        $sqlDump .= "-- Generated at: " . now()->toDateTimeString() . "\n\n";
        $sqlDump .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            $tableName = $table->$tablesField;
            
            // Generate Create Table
            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`")[0];
            $sqlDump .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $sqlDump .= $createTable->{'Create Table'} . ";\n\n";
            
            // Generate Inserts
            $rows = DB::table($tableName)->get();
            foreach ($rows as $row) {
                $rowArray = (array)$row;
                $columns = array_keys($rowArray);
                $values = array_values($rowArray);
                
                $escapedValues = array_map(function($val) {
                    if ($val === null) return "NULL";
                    return "'" . addslashes((string)$val) . "'";
                }, $values);
                
                $sqlDump .= "INSERT INTO `{$tableName}` (`" . implode("`, `", $columns) . "`) VALUES (" . implode(", ", $escapedValues) . ");\n";
            }
            $sqlDump .= "\n";
        }

        $sqlDump .= "SET FOREIGN_KEY_CHECKS=1;";

        $filename = 'backup_db_' . date('Y_m_d_His') . '.sql';

        return response($sqlDump)
            ->header('Content-Type', 'application/sql')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function buildDropdownOptions(): array
    {
        return Cache::remember('laporan_dropdown_options', now()->addHours(24), function() {
            $masterData = \App\Models\MasterData::where('is_active', true)->get()->groupBy('category');

            return [
                'kelas_stasiun' => $masterData->has('kelas_stasiun') ? $masterData['kelas_stasiun']->pluck('value')->toArray() : ['AL', 'AM', 'AT', 'BC', 'BT', 'FA', 'FB', 'FC', 'FD', 'FG', 'FL', 'FP', 'FX', 'LR', 'MA', 'ML', 'MO', 'MR', 'MS', 'NL', 'NR', 'OD', 'OE', 'PL', 'RM', 'RN', 'SA', 'SM', 'SS', 'TC', 'UV', 'UW'],
                'stasiun_monitor' => $masterData->has('stasiun_monitor') ? $masterData['stasiun_monitor']->pluck('value')->toArray() : ['MSHF LAMPUNG'],
                'administrasi_termonitor' => $masterData->has('administrasi_termonitor') ? $masterData['administrasi_termonitor']->pluck('value')->toArray() : ['INS'],
                'kode_negara' => $masterData->has('kode_negara') ? $masterData['kode_negara']->pluck('value')->toArray() : ['INDONESIA (INS)']
            ];
        });
    }

    private function getDistinctMonitoringValues(string $column, array $fallback): array
    {
        $values = Monitoring::query()
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->select($column)
            ->distinct()
            ->orderBy($column)
            ->pluck($column)
            ->map(fn ($value) => trim((string) $value))
            ->filter(fn (string $value) => $value !== '')
            ->unique()
            ->values()
            ->all();

        return !empty($values) ? $values : $fallback;
    }

    private function prioritizeDropdownValue(array $values, string $priority): array
    {
        $normalized = array_values(array_unique(array_filter(array_map(
            fn ($value) => trim((string) $value),
            $values
        ), fn ($value) => $value !== '')));

        $priorityExists = in_array($priority, $normalized, true);
        $rest = array_values(array_filter($normalized, fn ($value) => $value !== $priority));

        return $priorityExists ? array_merge([$priority], $rest) : $normalized;
    }

    private function monitoringValidationRules(): array
    {
        return [
            'kategori' => ['required', 'string', 'in:MF,HF Rutin,HF Nelayan'],
            'kode_negara' => ['nullable', 'string', 'max:10'],
            'stasiun_monitor' => ['nullable', 'string', 'max:255'],
            'frekuensi_khz' => ['nullable', 'numeric'],
            'tanggal' => ['nullable', 'integer', 'between:1,31'],
            'bulan' => ['nullable', 'integer', 'between:1,12'],
            'tahun' => ['nullable', 'integer', 'between:2000,2100'],
            'jam_mulai' => ['nullable', 'string', 'max:10'],
            'jam_akhir' => ['nullable', 'string', 'max:10'],
            'kuat_medan_dbuvm' => ['nullable', 'numeric'],
            'identifikasi' => ['nullable', 'string', 'max:255'],
            'administrasi_termonitor' => ['nullable', 'string', 'max:50'],
            'kelas_stasiun' => ['nullable', 'string', 'max:50'],
            'lebar_band' => ['nullable', 'string', 'max:50'],
            'kelas_emisi' => ['nullable', 'string', 'max:50'],
            'perkiraan_lokasi_sumber_pancaran' => ['nullable', 'string', 'max:255'],
            'longitude_derajat' => ['nullable', 'string', 'max:10'],
            'longitude_arah' => ['nullable', 'string', 'max:10'],
            'longitude_menit' => ['nullable', 'string', 'max:10'],
            'latitude_derajat' => ['nullable', 'string', 'max:10'],
            'latitude_arah' => ['nullable', 'string', 'max:10'],
            'latitude_menit' => ['nullable', 'string', 'max:10'],
            'north_bearing' => ['nullable', 'string', 'max:20'],
            'akurasi' => ['nullable', 'string', 'max:30'],
            'tidak_sesuai_rr' => ['nullable', 'string', 'max:50'],
            'informasi_tambahan' => ['nullable', 'string'],
        ];
    }

    private function normalizeNumericFields(array $validated): array
    {
        $nullableFloatFields = ['kuat_medan_dbuvm'];
        foreach ($nullableFloatFields as $field) {
            if (array_key_exists($field, $validated)) {
                $validated[$field] = $this->toNullableFloat((string) $validated[$field]);
            }
        }

        $nullableIntFields = ['tanggal', 'bulan', 'tahun'];
        foreach ($nullableIntFields as $field) {
            if (array_key_exists($field, $validated)) {
                $validated[$field] = $this->toNullableInt((string) $validated[$field]);
            }
        }

        if (array_key_exists('frekuensi_khz', $validated)) {
            $validated['frekuensi_khz'] = $this->toNullableFloat((string) $validated['frekuensi_khz']);
        }

        return $validated;
    }

    private function extractMonitoringFilters(Request $request): array
    {
        $tanggalLengkap = trim((string) $request->query('tanggal_lengkap', ''));
        $parsedDate = $this->parseTanggalLengkap($tanggalLengkap);
        $searchIn = trim((string) $request->query('search_in', 'identifikasi'));
        $allowedSearchIn = ['identifikasi', 'frekuensi_khz', 'stasiun_monitor', 'administrasi_termonitor'];

        if (!in_array($searchIn, $allowedSearchIn, true)) {
            $searchIn = 'identifikasi';
        }

        // If full date provided, use it; otherwise use separate tanggal/bulan/tahun
        $bulan = $parsedDate['bulan'] ?? $request->query('bulan');
        $tanggal = $parsedDate['tanggal'] ?? $request->query('tanggal');
        $tahun = $parsedDate['tahun'] ?? $request->query('tahun');

        return [
            'kategori' => $request->query('kategori'),
            'bulan' => $bulan,
            'tanggal' => $tanggal,
            'tahun' => $tahun,
            'tanggal_lengkap' => $tanggalLengkap,
            'search_in' => $searchIn,
            'user_id' => $request->query('user_id'),
            'q' => trim((string) $request->query('q', '')),
        ];
    }

    private function formatFilterDescription(array $filters): string
    {
        $parts = [];

        if (!empty($filters['kategori'])) {
            $parts[] = 'Kategori: ' . $filters['kategori'];
        }

        if (!empty($filters['user_id'])) {
            $user = \App\Models\User::find($filters['user_id']);
            $parts[] = 'Petugas: ' . ($user ? $user->name : $filters['user_id']);
        }

        if (!empty($filters['tanggal'])) {
            $parts[] = 'Tgl: ' . $filters['tanggal'];
        }

        if (!empty($filters['bulan'])) {
            $parts[] = 'Bln: ' . $filters['bulan'];
        }

        if (!empty($filters['tahun'])) {
            $parts[] = 'Thn: ' . $filters['tahun'];
        }

        if (!empty($filters['q'])) {
            $searchIn = $filters['search_in'] ?? 'identifikasi';
            $label = match ($searchIn) {
                'identifikasi' => 'Identifikasi',
                'frekuensi_khz' => 'Frekuensi',
                'stasiun_monitor' => 'Stasiun',
                'administrasi_termonitor' => 'Administrasi',
                default => 'Keyword',
            };
            $parts[] = $label . ': "' . $filters['q'] . '"';
        }

        return !empty($parts) ? 'filter (' . implode(', ', $parts) . ')' : '';
    }

    private function parseTanggalLengkap(string $tanggalLengkap): ?array
    {
        if ($tanggalLengkap === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalLengkap)) {
            return null;
        }

        [$tahun, $bulan, $tanggal] = array_map('intval', explode('-', $tanggalLengkap));

        if (!checkdate($bulan, $tanggal, $tahun)) {
            return null;
        }

        return [
            'tahun' => $tahun,
            'bulan' => $bulan,
            'tanggal' => $tanggal,
        ];
    }

    private function toNullableString(string $value): ?string
    {
        $value = trim($value);

        return $value === '' ? null : $value;
    }

    private function toNullableInt(string $value): ?int
    {
        $value = trim($value);
        if ($value === '' || !is_numeric($value)) {
            return null;
        }

        return (int) $value;
    }

    private function toNullableFloat(string $value): ?float
    {
        $value = trim(str_replace(',', '.', $value));
        if ($value === '' || !is_numeric($value)) {
            return null;
        }

        return (float) $value;
    }

    private function toMinutesFromJam(string $time): ?int
    {
        $normalized = trim(str_replace('.', ':', $time));
        if ($normalized === '' || !preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $normalized, $matches)) {
            return null;
        }

        return ((int) $matches[1] * 60) + (int) $matches[2];
    }

    private function monitoringFilteredQuery(array $filters): Builder|\Illuminate\Database\Query\Builder
    {
        $query = Monitoring::query()
            ->with('user:id,name')
            ->select('id', 'user_id', 'kode_negara', 'stasiun_monitor', 'frekuensi_khz', 'tanggal', 'bulan', 'tahun', 'jam_mulai', 'jam_akhir', 'kuat_medan_dbuvm', 'identifikasi', 'administrasi_termonitor', 'kelas_stasiun', 'lebar_band', 'kelas_emisi', 'longitude_derajat', 'longitude_arah', 'longitude_menit', 'latitude_derajat', 'latitude_arah', 'latitude_menit', 'north_bearing', 'akurasi', 'tidak_sesuai_rr', 'informasi_tambahan', 'kategori', 'created_at', 'updated_at');

        // SECURITY: Admin biasa hanya bisa melihat datanya sendiri
        if (auth()->user()->role !== 'super_admin') {
            $query->where('user_id', auth()->id());
        } else {
            // Super Admin bisa filter berdasarkan petugas tertentu
            if (isset($filters['user_id']) && $filters['user_id'] !== '') {
                $query->where('user_id', $filters['user_id']);
            }
        }

        return $query
            ->when($filters['kategori'] ?? null, fn ($query, $kategori) => $query->where('kategori', $kategori))
            ->when($filters['bulan'] ?? null, fn ($query, $bulan) => $query->where('bulan', $bulan))
            ->when($filters['tanggal'] ?? null, fn ($query, $tanggal) => $query->where('tanggal', $tanggal))
            ->when($filters['tahun'] ?? null, fn ($query, $tahun) => $query->where('tahun', $tahun))
            // OPTIMIZED: Simplified search query (was: nested WHERE with OR clauses)
            // Now: Simple LIKE search on selected column - 10x faster, index-friendly
            ->when(($filters['q'] ?? '') !== '', function ($query) use ($filters) {
                $keyword = trim((string) $filters['q']);
                $searchColumn = (string) ($filters['search_in'] ?? 'identifikasi');
                $query->where($searchColumn, 'like', "%{$keyword}%");
            })
            ->orderBy('tahun', 'DESC')
            ->orderBy('bulan', 'DESC')
            ->orderBy('tanggal', 'DESC')
            ->orderBy('jam_mulai', 'DESC')
            ->orderBy('id', 'DESC');
    }
    private function authorizeMonitoring(Monitoring $monitoring): void
    {
        if (auth()->user()->role !== 'super_admin' && $monitoring->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk memodifikasi data ini.');
        }
    }
}
