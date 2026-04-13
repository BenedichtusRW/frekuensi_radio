<?php

namespace App\Http\Controllers;

use App\Exports\MonitoringLogbookExport;
use App\Exports\MonitoringTypeExport;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Monitoring;
use App\Models\MonitoringExport;
use App\Models\MonitoringLog;
use App\Services\MonitoringLogImportService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class MonitoringController extends Controller
{
    private const XLSX_ARCHIVE_DIR = 'exports/xlsx';

    public function dashboard(Request $request)
    {
        $this->logActivity($request, 'visit_dashboard', 'Membuka halaman Dashboard');

        // AUTO-CLEANUP: 1 dari 10 request akan membersihkan log > 30 hari (tidak blokir response)
        if (random_int(1, 10) === 1) {
            ActivityLog::pruneOldLogs(30);
        }

        $cacheDuration = now()->addMinutes(10);

        // 1. CACHE SUMMARY STATS (Bulan Ini + All Time)
        $summary = Cache::remember('dashboard_summary_stats', $cacheDuration, function () {
            $currentMonth = now()->month;
            $currentYear = now()->year;

            $monthlyBaseQuery = Monitoring::query()
                ->where('tahun', $currentYear)
                ->where('bulan', $currentMonth);

            return [
                // Bulan ini
                'month_label'   => now()->translatedFormat('F Y'),
                'total_month'   => (clone $monthlyBaseQuery)->count(),
                'mf_month'      => (clone $monthlyBaseQuery)->where('kategori', 'MF')->count(),
                'rutin_month'   => (clone $monthlyBaseQuery)->where('kategori', 'HF Rutin')->count(),
                'nelayan_month' => (clone $monthlyBaseQuery)->where('kategori', 'HF Nelayan')->count(),
                // All-time (total keseluruhan)
                'total_all'   => Monitoring::count(),
                'mf_all'      => Monitoring::where('kategori', 'MF')->count(),
                'rutin_all'   => Monitoring::where('kategori', 'HF Rutin')->count(),
                'nelayan_all' => Monitoring::where('kategori', 'HF Nelayan')->count(),
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
        $barChart = Cache::remember('dashboard_barchart_stats_v2', $cacheDuration, function () {
            $startDate = now()->subDays(6)->startOfDay();
            $endDate   = now()->endOfDay();

            // Fetch counts grouped by date and category
            $rows = Monitoring::query()
                ->selectRaw('tanggal, bulan, tahun, kategori, COUNT(*) as total')
                ->where('tahun', '>=', (int) $startDate->year)
                ->whereBetween('bulan', [(int) $startDate->month, (int) $endDate->month])
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
        $monthlyChart = Cache::remember('dashboard_monthly_stats_v2', $cacheDuration, function () {
            $currentYear = now()->year;

            $rows = Monitoring::query()
                ->selectRaw('bulan, kategori, COUNT(*) as total')
                ->where('tahun', $currentYear)
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

        // 5. RECENT ACTIVITY LOG (20 aksi terbaru)
        $activityLogs = ActivityLog::latest()->limit(20)->get();

        return view('dashboard', [
            'summary'       => $summary,
            'pieChart'      => $pieChart,
            'barChart'      => $barChart,
            'monthlyChart'  => $monthlyChart,
            'activityLogs'  => $activityLogs,
        ]);
    }

    public function create()
    {
        return view('input', [
            'dropdownOptions' => $this->buildDropdownOptions(),
        ]);
    }

    /**
     * Catat aktivitas pengguna ke tabel activity_logs.
     * Dijalankan secara silent (tidak pernah melempar exception).
     */
    private function logActivity(Request $request, string $action, string $description): void
    {
        try {
            $ip = $request->ip();
            $ua = $request->userAgent() ?? '';

            // Deteksi browser
            $browser = 'Unknown';
            $browserMap = [
                'Edg'     => 'Microsoft Edge',
                'OPR'     => 'Opera',
                'Chrome'  => 'Chrome',
                'Firefox' => 'Firefox',
                'Safari'  => 'Safari',
                'MSIE'    => 'IE',
            ];
            foreach ($browserMap as $key => $name) {
                if (str_contains($ua, $key)) { $browser = $name; break; }
            }

            // Deteksi OS
            $platform = 'Unknown';
            if (str_contains($ua, 'Windows'))     $platform = 'Windows';
            elseif (str_contains($ua, 'Android')) $platform = 'Android';
            elseif (str_contains($ua, 'iPhone'))  $platform = 'iOS (iPhone)';
            elseif (str_contains($ua, 'iPad'))    $platform = 'iOS (iPad)';
            elseif (str_contains($ua, 'Mac'))     $platform = 'macOS';
            elseif (str_contains($ua, 'Linux'))   $platform = 'Linux';

            // Deteksi tipe device
            $device = 'Desktop';
            if (preg_match('/iPhone|Android.*Mobile/i', $ua)) $device = 'Mobile';
            elseif (preg_match('/iPad|Tablet/i', $ua))         $device = 'Tablet';

            // Lookup ISP dari IP (di-cache 24 jam per IP)
            $isp = 'N/A';
            if ($ip && !in_array($ip, ['127.0.0.1', '::1'])) {
                $isp = Cache::remember('isp_' . md5($ip), now()->addHours(24), function () use ($ip) {
                    try {
                        $ctx = stream_context_create(['http' => ['timeout' => 3]]);
                        $res = @file_get_contents("http://ip-api.com/json/{$ip}?fields=isp,org", false, $ctx);
                        if ($res) {
                            $d = json_decode($res, true);
                            return $d['isp'] ?? $d['org'] ?? 'Unknown';
                        }
                    } catch (\Throwable) {}
                    return 'Unknown';
                });
            } else {
                $isp = 'Loopback/Localhost';
            }

            ActivityLog::create([
                'ip_address'  => $ip,
                'browser'     => $browser,
                'platform'    => $platform,
                'device'      => $device,
                'isp'         => $isp,
                'action'      => $action,
                'description' => $description,
            ]);
        } catch (\Throwable) {
            // Fail silently agar tidak merusak fitur utama
        }
    }

    public function index(Request $request)
    {
        $this->logActivity($request, 'visit_laporan', 'Membuka halaman Daftar Laporan');

        $filters = $this->extractMonitoringFilters($request);
        $editMonitoring = null;
        $editTableNumber = $this->toNullableInt((string) $request->query('no', ''));

        if ($request->filled('edit_id')) {
            $editMonitoring = Monitoring::find((int) $request->query('edit_id'));
        }

        $monitorings = $this->monitoringFilteredQuery($filters)
            ->paginate(10)
            ->withQueryString();

        return view('laporan', [
            'monitorings' => $monitorings,
            'filters' => $filters,
            'pageTitle' => 'Daftar Laporan',
            'dropdownOptions' => $this->buildDropdownOptions(),
            'editMonitoring' => $editMonitoring,
            'editTableNumber' => $editTableNumber,
        ]);
    }

    public function exportLaporan(Request $request)
    {
        $this->logActivity($request, 'export_xlsx', 'Export data laporan ke XLSX');

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

        return redirect()
            ->back()
            ->with('success', 'Data monitoring berhasil disimpan.');
    }

    /**
     * Clear all dashboard-related caches to reflect new data.
     */
    private function clearDashboardCache(): void
    {
        Cache::forget('dashboard_summary_stats');
        Cache::forget('dashboard_barchart_stats_v2');
        Cache::forget('dashboard_monthly_stats_v2');
    }

    public function edit(Request $request, int $id)
    {
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

        $monitoring->update($validated);

        // CLEAR DASHBOARD CACHE: Force real-time update
        $this->clearDashboardCache();

        $this->logActivity($request, 'edit_data', 'Edit data ID #' . $id . ': Frekuensi ' . ($validated['frekuensi_khz'] ?? '-') . ' kHz');

        $editTableNumber = $this->toNullableInt((string) $request->input('edit_table_no', ''));

        return redirect()
            ->route('monitoring.index')
            ->with('success', 'Data monitoring berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $monitoring = Monitoring::findOrFail($id);
        $monitoring->delete();

        return redirect()
            ->route('monitoring.index')
            ->with('success', 'Data monitoring berhasil dihapus.');
    }

    public function history()
    {
        [$recentFiles, $archivedFiles] = $this->getHistoryFiles();

        return view('history', [
            'recentFiles' => $recentFiles,
            'archivedFiles' => $archivedFiles,
        ]);
    }

    public function upload(Request $request, MonitoringLogImportService $importService)
    {
        $validated = $request->validate([
            'monitoring_file' => ['required', 'file', 'mimes:csv,xlsx'],
            'purge_existing_data' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('purge_existing_data')) {
            MonitoringLog::query()->where('is_archived', false)->delete();
        }

        $uploadedFile = $validated['monitoring_file'];
        $storedPath = $uploadedFile->store('uploads', 'local');

        $absolutePath = Storage::disk('local')->path($storedPath);
        $importedRows = $importService->import(
            $absolutePath,
            $uploadedFile->getClientOriginalName(),
            'accurate',
        );

        $forcedRows = MonitoringLog::query()
            ->where('source_file', $uploadedFile->getClientOriginalName())
            ->where('is_archived', false)
            ->where('is_forced_classification', true)
            ->count();

        $otherRows = MonitoringLog::query()
            ->where('source_file', $uploadedFile->getClientOriginalName())
            ->where('is_archived', false)
            ->where('monitoring_type', 'other')
            ->count();

        $notice = null;
        if ($otherRows > 0) {
            $notice = "Terdapat {$otherRows} baris di luar range resmi. Silakan cek menu Perlu Verifikasi sebelum laporan dikirim.";
        }

        $redirect = redirect()
            ->route('pilah.preview', ['file' => $uploadedFile->getClientOriginalName()])
            ->with('success', "Import selesai (akurasi resmi). {$importedRows} baris baru disimpan. {$forcedRows} baris memakai klasifikasi paksa.");

        if ($notice !== null) {
            $redirect->with('warning', $notice);
        }

        return $redirect;
    }

    public function preview(Request $request)
    {
        $sourceFile = (string) $request->query('file', '');

        if ($sourceFile === '') {
            return redirect()->route('history')->with('warning', 'Pilih file terlebih dahulu dari menu History untuk melihat preview hasil pilah.');
        }

        [, $counts] = $this->buildSummaryAndCounts($sourceFile);

        $previewNelayan = MonitoringLog::query()
            ->where('source_file', $sourceFile)
            ->where('is_archived', false)
            ->where('monitoring_type', 'nelayan')
            ->orderBy('source_row')
            ->limit(10)
            ->get();

        $previewRutin = MonitoringLog::query()
            ->where('source_file', $sourceFile)
            ->where('is_archived', false)
            ->where('monitoring_type', 'rutin')
            ->orderBy('source_row')
            ->limit(10)
            ->get();

        $previewMf = MonitoringLog::query()
            ->where('source_file', $sourceFile)
            ->where('is_archived', false)
            ->where('monitoring_type', 'mf')
            ->orderBy('source_row')
            ->limit(10)
            ->get();

        return view('pilah-preview', [
            'sourceFile' => $sourceFile,
            'previewNelayan' => $previewNelayan,
            'previewRutin' => $previewRutin,
            'previewMf' => $previewMf,
            'counts' => $counts,
        ]);
    }

    private function getHistoryFiles(): array
    {
        $recentFiles = MonitoringLog::query()
            ->where('is_archived', false)
            ->select('source_file')
            ->distinct()
            ->latest('id')
            ->limit(10)
            ->pluck('source_file');

        $archivedFiles = MonitoringLog::query()
            ->where('is_archived', true)
            ->select('source_file')
            ->distinct()
            ->latest('archived_at')
            ->limit(10)
            ->pluck('source_file');

        return [$recentFiles, $archivedFiles];
    }

    public function nelayan(Request $request)
    {
        $sourceFile = $request->query('file');
        [, $counts] = $this->buildSummaryAndCounts($sourceFile);

        $logs = MonitoringLog::query()
            ->where('is_archived', false)
            ->where('monitoring_type', 'nelayan')
            ->when($sourceFile, fn ($query) => $query->where('source_file', $sourceFile))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('nelayan', [
            'logs' => $logs,
            'sourceFile' => $sourceFile,
            'counts' => $counts,
        ]);
    }

    public function rutin(Request $request)
    {
        $sourceFile = $request->query('file');
        [, $counts] = $this->buildSummaryAndCounts($sourceFile);

        $logs = MonitoringLog::query()
            ->where('is_archived', false)
            ->where('monitoring_type', 'rutin')
            ->when($sourceFile, fn ($query) => $query->where('source_file', $sourceFile))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('rutin', [
            'logs' => $logs,
            'sourceFile' => $sourceFile,
            'counts' => $counts,
        ]);
    }

    public function mf(Request $request)
    {
        $sourceFile = $request->query('file');
        [, $counts] = $this->buildSummaryAndCounts($sourceFile);

        $logs = MonitoringLog::query()
            ->where('is_archived', false)
            ->where('monitoring_type', 'mf')
            ->when($sourceFile, fn ($query) => $query->where('source_file', $sourceFile))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('mf', [
            'logs' => $logs,
            'sourceFile' => $sourceFile,
            'counts' => $counts,
        ]);
    }

    public function verifikasi(Request $request)
    {
        $sourceFile = $request->query('file');

        $logs = MonitoringLog::query()
            ->where('is_archived', false)
            ->where('monitoring_type', 'other')
            ->when($sourceFile, fn ($query) => $query->where('source_file', $sourceFile))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('verifikasi', [
            'logs' => $logs,
            'sourceFile' => $sourceFile,
        ]);
    }

    public function exportNelayan(Request $request)
    {
        $sourceFile = $request->query('file');

        return Excel::download(new MonitoringTypeExport('nelayan', $sourceFile), 'monitoring_hf_nelayan.xlsx');
    }

    public function exportRutin(Request $request)
    {
        $sourceFile = $request->query('file');

        return Excel::download(new MonitoringTypeExport('rutin', $sourceFile), 'monitoring_hf_rutin.xlsx');
    }

    public function exportMf(Request $request)
    {
        $sourceFile = $request->query('file');

        return Excel::download(new MonitoringTypeExport('mf', $sourceFile), 'monitoring_hf_medium_frequency.xlsx');
    }

    public function exportVerifikasi(Request $request)
    {
        $sourceFile = $request->query('file');

        return Excel::download(new MonitoringTypeExport('other', $sourceFile), 'monitoring_perlu_verifikasi.xlsx');
    }

    public function exportAllZip(Request $request): BinaryFileResponse
    {
        if (!class_exists(ZipArchive::class)) {
            abort(500, 'Ekstensi ZIP PHP belum aktif. Aktifkan ext-zip pada PHP/XAMPP terlebih dahulu.');
        }

        $sourceFile = $request->query('file');

        $tempDir = storage_path('app/temp');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $token = now()->format('Ymd_His') . '_' . Str::random(6);
        $zipPath = $tempDir . DIRECTORY_SEPARATOR . "monitoring_exports_{$token}.zip";

        $zip = new ZipArchive();
        $openResult = $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($openResult !== true) {
            abort(500, 'Gagal membuat file ZIP export.');
        }

        $zip->addFromString(
            'monitoring_hf_nelayan.xlsx',
            Excel::raw(new MonitoringTypeExport('nelayan', $sourceFile), \Maatwebsite\Excel\Excel::XLSX),
        );
        $zip->addFromString(
            'monitoring_hf_rutin.xlsx',
            Excel::raw(new MonitoringTypeExport('rutin', $sourceFile), \Maatwebsite\Excel\Excel::XLSX),
        );
        $zip->addFromString(
            'monitoring_hf_medium_frequency.xlsx',
            Excel::raw(new MonitoringTypeExport('mf', $sourceFile), \Maatwebsite\Excel\Excel::XLSX),
        );

        $zip->close();

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function archiveFile(Request $request)
    {
        $validated = $request->validate([
            'source_file' => ['required', 'string'],
        ]);

        MonitoringLog::query()
            ->where('source_file', $validated['source_file'])
            ->where('is_archived', false)
            ->update([
                'is_archived' => true,
                'archived_at' => now(),
            ]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'File berhasil diarsipkan dari daftar aktif.');
    }

    public function deleteFile(Request $request)
    {
        $validated = $request->validate([
            'source_file' => ['required', 'string'],
        ]);

        MonitoringLog::query()
            ->where('source_file', $validated['source_file'])
            ->delete();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Data file berhasil dihapus permanen dari database.');
    }

    public function restoreFile(Request $request)
    {
        $validated = $request->validate([
            'source_file' => ['required', 'string'],
        ]);

        MonitoringLog::query()
            ->where('source_file', $validated['source_file'])
            ->where('is_archived', true)
            ->update([
                'is_archived' => false,
                'archived_at' => null,
            ]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'File arsip berhasil dipulihkan ke daftar aktif.');
    }

    private function buildSummaryAndCounts(?string $sourceFile): array
    {
        $baseQuery = MonitoringLog::query()
            ->where('is_archived', false)
            ->when($sourceFile, fn ($query) => $query->where('source_file', $sourceFile));

        $counts = [
            'nelayan' => (clone $baseQuery)->where('monitoring_type', 'nelayan')->count(),
            'rutin' => (clone $baseQuery)->where('monitoring_type', 'rutin')->count(),
            'mf' => (clone $baseQuery)->where('monitoring_type', 'mf')->count(),
            'other' => (clone $baseQuery)->where('monitoring_type', 'other')->count(),
        ];

        $summary = [
            'total_terdeteksi' => (clone $baseQuery)->count(),
            'teridentifikasi' => (clone $baseQuery)->whereNotNull('identifikasi')->count(),
            'tba' => (clone $baseQuery)->whereNull('administrasi_termonitor')->count(),
            'indikasi_gangguan' => (clone $baseQuery)->whereNotNull('tidak_sesuai_rr')->count(),
            'perlu_verifikasi' => $counts['other'],
        ];

        return [$summary, $counts];
    }

    private function buildDropdownOptions(): array
    {
        $administrasiOptions = $this->getDistinctMonitoringLogValues('administrasi_termonitor', ['INS']);

        return [
            'stasiun_monitor' => $this->getDistinctMonitoringLogValues('stasiun_monitor', ['MSHF LAMPUNG']),
            'administrasi_termonitor' => $this->prioritizeDropdownValue($administrasiOptions, 'INS'),
            'kelas_stasiun' => ['AL', 'AM', 'AT', 'BC', 'BT', 'FA', 'FB', 'FC', 'FD', 'FG', 'FL', 'FP', 'FX', 'LR', 'MA', 'ML', 'MO', 'MR', 'MS', 'NL', 'NR', 'OD', 'OE', 'PL', 'RM', 'RN', 'SA', 'SM', 'SS', 'TC', 'UV', 'UW'],
        ];
    }

    private function getDistinctMonitoringLogValues(string $column, array $fallback): array
    {
        $values = MonitoringLog::query()
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
            'q' => trim((string) $request->query('q', '')),
        ];
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

    private function monitoringFilteredQuery(array $filters): Builder
    {
        return Monitoring::query()
            ->select('id', 'kode_negara', 'stasiun_monitor', 'frekuensi_khz', 'tanggal', 'bulan', 'tahun', 'jam_mulai', 'jam_akhir', 'kuat_medan_dbuvm', 'identifikasi', 'administrasi_termonitor', 'kelas_stasiun', 'lebar_band', 'kelas_emisi', 'longitude_derajat', 'longitude_arah', 'longitude_menit', 'latitude_derajat', 'latitude_arah', 'latitude_menit', 'north_bearing', 'akurasi', 'tidak_sesuai_rr', 'informasi_tambahan', 'kategori', 'created_at', 'updated_at')
            ->when($filters['kategori'] ?? null, fn ($query, $kategori) => $query->where('kategori', $kategori))
            ->when($filters['bulan'] ?? null, fn ($query, $bulan) => $query->where('bulan', $bulan))
            ->when($filters['tanggal'] ?? null, fn ($query, $tanggal) => $query->where('tanggal', $tanggal))
            ->when($filters['tahun'] ?? null, fn ($query, $tahun) => $query->where('tahun', $tahun))
            ->when(($filters['q'] ?? '') !== '', function ($query) use ($filters) {
                $keyword = trim((string) $filters['q']);
                $normalizedKeyword = preg_replace('/\s+/', ' ', $keyword) ?? $keyword;
                $terms = array_values(array_filter(explode(' ', $normalizedKeyword)));

                $searchColumn = (string) ($filters['search_in'] ?? 'identifikasi');

                $query->where(function ($inner) use ($searchColumn, $normalizedKeyword, $terms) {
                    // Match full phrase first for exact intent.
                    $inner->where(function ($phraseQuery) use ($searchColumn, $normalizedKeyword) {
                        $phraseQuery->where($searchColumn, 'like', "%{$normalizedKeyword}%");
                    });

                    // Fallback: match each token so differences in spacing still return results.
                    if (!empty($terms)) {
                        $inner->orWhere(function ($tokenQuery) use ($searchColumn, $terms) {
                            foreach ($terms as $term) {
                                $tokenQuery->where(function ($singleTermQuery) use ($searchColumn, $term) {
                                    $singleTermQuery->where($searchColumn, 'like', "%{$term}%");
                                });
                            }
                        });
                    }
                });
            })
            ->orderByRaw('COALESCE(tahun, 0) DESC')
            ->orderByRaw('COALESCE(bulan, 0) DESC')
            ->orderByRaw('COALESCE(tanggal, 0) DESC')
            ->orderByRaw('COALESCE(jam_mulai, "") DESC')
            ->orderBy('id', 'DESC');
    }



}
