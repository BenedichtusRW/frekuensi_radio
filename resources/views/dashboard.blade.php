@extends('layouts.app')

@section('title', 'Dashboard Overview - Balmon Lampung')
@section('page_title', 'Dashboard')

@section('content')
    <style>
        .metric-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: none !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
            border-radius: 20px !important;
            background: #ffffff;
        }
        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        }
        .chart-card {
            border: none !important;
            border-radius: 20px !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05) !important;
            background: #ffffff;
        }
        .activity-log-card {
            border: 1px solid #f1f5f9 !important;
        }
        
        /* Fixed Responsive Icon Boxes */
        .icon-box-wrapper {
            width: 36px; height: 36px; min-width: 36px;
        }
        @media (min-width: 768px) {
            .icon-box-wrapper { width: 48px; height: 48px; min-width: 48px; }
        }
    </style>

    <!-- Super Admin Filter Row -->
    @if(auth()->check() && auth()->user()->role === 'super_admin')
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-end">
            <div class="d-flex align-items-center gap-2 bg-white px-3 py-2 rounded-pill shadow-sm" style="border: 1px solid #f1f5f9;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <span class="text-slate-500 fw-bold" style="font-size: 0.75rem;">Filter Petugas:</span>
                <select name="user_id" class="form-select form-select-sm border-0 fw-bold text-dark cursor-pointer bg-slate-50" style="font-size: 0.8rem; width: auto; min-width: 150px; border-radius: 12px; padding-left: 10px;" onchange="Livewire.navigate('{{ route('dashboard') }}?user_id=' + this.value)">
                    <option value="">Semua Petugas (Keseluruhan)</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ (string)($selectedUserId ?? '') === (string)$user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    @endif

    <!-- Metric Cards Row (Tightened) -->
    <div class="row g-3 mb-3">
        <div class="col-3">
            <div class="card metric-card h-100 overflow-hidden">
                <div class="card-body p-2 p-md-4 d-flex flex-column flex-md-row align-items-center text-center text-md-start gap-1 gap-md-3">
                    <div class="rounded-circle bg-slate-50 text-slate-700 border border-slate-100 d-flex align-items-center justify-content-center icon-box-wrapper">
                        <x-icon icon="total_data" width="20" height="20" stroke="currentColor" />
                    </div>
                    <div class="overflow-hidden w-100">
                        <div class="text-xs font-bold text-slate-500 text-uppercase mb-0 text-truncate">
                            <span class="d-md-none" style="font-size: 0.5rem;">Total</span>
                            <span class="d-none d-md-inline" style="font-size: 0.65rem; letter-spacing: 0.05em;">Total Data</span>
                        </div>
                        <div class="fw-bold mb-0 text-dark" style="font-size: 0.85rem; @media (min-width: 768px) { font-size: 1.25rem !important; }">
                            <span class="d-md-none">{{ number_format($summary['total_all'] ?? 0) }}</span>
                            <span class="d-none d-md-inline" style="font-size: 1.25rem;">{{ number_format($summary['total_all'] ?? 0) }}</span>
                        </div>
                    </div>
                </div>
                <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 3px; background: #475569;"></div>
            </div>
        </div>
        <div class="col-3">
            <div class="card metric-card h-100 overflow-hidden">
                <div class="card-body p-2 p-md-4 d-flex flex-column flex-md-row align-items-center text-center text-md-start gap-1 gap-md-3">
                    <div class="rounded-circle bg-blue-50 text-blue-600 border border-blue-100 d-flex align-items-center justify-content-center icon-box-wrapper">
                        <x-icon icon="medium_frequency" width="20" height="20" stroke="currentColor" />
                    </div>
                    <div class="overflow-hidden w-100">
                        <div class="text-xs font-bold text-slate-500 text-uppercase mb-0 text-truncate">
                            <span class="d-md-none" style="font-size: 0.5rem;">MF</span>
                            <span class="d-none d-md-inline" style="font-size: 0.65rem; letter-spacing: 0.05em;">HF Medium Frequency</span>
                        </div>
                        <div class="fw-bold mb-0 text-dark">
                            <span class="d-md-none" style="font-size: 0.85rem;">{{ number_format($summary['mf_all'] ?? 0) }}</span>
                            <span class="d-none d-md-inline" style="font-size: 1.25rem;">{{ number_format($summary['mf_all'] ?? 0) }}</span>
                        </div>
                    </div>
                </div>
                <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 3px; background: #2563eb;"></div>
            </div>
        </div>
        <div class="col-3">
            <div class="card metric-card h-100 overflow-hidden">
                <div class="card-body p-2 p-md-4 d-flex flex-column flex-md-row align-items-center text-center text-md-start gap-1 gap-md-3">
                    <div class="rounded-circle bg-emerald-50 text-emerald-600 border border-emerald-100 d-flex align-items-center justify-content-center icon-box-wrapper">
                        <x-icon icon="rutin" width="20" height="20" stroke="currentColor" />
                    </div>
                    <div class="overflow-hidden w-100">
                        <div class="text-xs font-bold text-slate-500 text-uppercase mb-0 text-truncate">
                            <span class="d-md-none" style="font-size: 0.5rem;">Rutin</span>
                            <span class="d-none d-md-inline" style="font-size: 0.65rem; letter-spacing: 0.05em;">HF Rutin</span>
                        </div>
                        <div class="fw-bold mb-0 text-dark">
                            <span class="d-md-none" style="font-size: 0.85rem;">{{ number_format($summary['rutin_all'] ?? 0) }}</span>
                            <span class="d-none d-md-inline" style="font-size: 1.25rem;">{{ number_format($summary['rutin_all'] ?? 0) }}</span>
                        </div>
                    </div>
                </div>
                <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 3px; background: #10b981;"></div>
            </div>
        </div>
        <div class="col-3">
            <div class="card metric-card h-100 overflow-hidden">
                <div class="card-body p-2 p-md-4 d-flex flex-column flex-md-row align-items-center text-center text-md-start gap-1 gap-md-3">
                    <div class="rounded-circle bg-rose-50 text-rose-600 border border-rose-100 d-flex align-items-center justify-content-center icon-box-wrapper">
                        <x-icon icon="nelayan" width="20" height="20" stroke="currentColor" />
                    </div>
                    <div class="overflow-hidden w-100">
                        <div class="text-xs font-bold text-slate-500 text-uppercase mb-0 text-truncate">
                            <span class="d-md-none" style="font-size: 0.5rem;">Nelayan</span>
                            <span class="d-none d-md-inline" style="font-size: 0.65rem; letter-spacing: 0.05em;">HF Nelayan</span>
                        </div>
                        <div class="fw-bold mb-0 text-dark">
                            <span class="d-md-none" style="font-size: 0.85rem;">{{ number_format($summary['nelayan_all'] ?? 0) }}</span>
                            <span class="d-none d-md-inline" style="font-size: 1.25rem;">{{ number_format($summary['nelayan_all'] ?? 0) }}</span>
                        </div>
                    </div>
                </div>
                <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 3px; background: #f43f5e;"></div>
            </div>
        </div>
    </div>

    <!-- Main Charts Row (Side-by-Side Stacked) -->
    <div class="row g-3 mb-3">
        <div class="col-12 col-lg-6">
            <div class="card chart-card overflow-hidden h-100">
                <div class="card-header bg-white py-3 border-bottom border-slate-50 d-flex align-items-center gap-2">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="12" width="4" height="9"/><rect x="10" y="3" width="4" height="18"/><rect x="17" y="7" width="4" height="14"/></svg>
                    <span class="fw-bold text-dark" style="font-size: 0.85rem;">Aktivitas 7 Hari Terakhir</span>
                </div>
                <div class="card-body py-2">
                    <div style="height: 240px;">
                        <canvas id="barChart" data-chart="{{ json_encode($barChart ?? ['labels' => [], 'datasets' => []]) }}"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card chart-card overflow-hidden h-100">
                <div class="card-header bg-white py-3 border-bottom border-slate-50 d-flex align-items-center gap-2">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8 10 17"></polyline><polyline points="23 6 23 12"></polyline></svg>
                    <span class="fw-bold text-dark" style="font-size: 0.85rem;">Aktivitas Monitoring {{ date('Y') }}</span>
                </div>
                <div class="card-body py-2">
                    <div style="height: 240px;">
                        <canvas id="monthlyChart" data-chart="{{ json_encode($monthlyChart ?? ['labels' => [], 'datasets' => [], 'year' => '']) }}"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Monitoring Section -->
    <div class="row g-3 mb-3">
        <div class="col-12">
            <div class="card chart-card overflow-hidden">
                <div class="card-header bg-white py-2.5 border-bottom border-slate-50 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span class="fw-bold text-dark" style="font-size: 0.85rem;">Monitoring Terbaru</span>
                    </div>
                    <a href="{{ route('monitoring.index') }}" wire:navigate class="btn btn-sm btn-light" style="font-size: 0.75rem;">Lihat Semua →</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.73rem;">
                            <thead>
                                <tr class="bg-slate-50 border-bottom">
                                    <th class="px-3 py-2 text-dark font-bold text-uppercase tracking-wider" style="font-size: 0.55rem;">Waktu Ditambah</th>
                                    <th class="py-2 text-dark font-bold text-uppercase tracking-wider" style="font-size: 0.55rem;">Jenis Laporan</th>
                                    <th class="py-2 text-dark font-bold text-uppercase tracking-wider" style="font-size: 0.55rem;">Tahun</th>
                                    <th class="py-2 text-dark font-bold text-uppercase tracking-wider" style="font-size: 0.55rem;">Bulan</th>
                                    <th class="py-2 text-dark font-bold text-uppercase tracking-wider" style="font-size: 0.55rem;">Tanggal</th>
                                    <th class="py-2 text-dark font-bold text-uppercase tracking-wider" style="font-size: 0.55rem;">Petugas</th>
                                </tr>
                            </thead>
                            <tbody class="border-0">
                                @forelse ($recentMonitoring as $monitoring)
                                    @php
                                        $categoryColor = match($monitoring->kategori) {
                                            'MF' => 'text-blue-700 bg-blue-100 border-blue-200',
                                            'HF Rutin' => 'text-emerald-700 bg-emerald-100 border-emerald-200',
                                            'HF Nelayan' => 'text-rose-700 bg-rose-100 border-rose-200',
                                            default => 'text-slate-700 bg-slate-100 border-slate-200',
                                        };
                                        $categoryIcon = match($monitoring->kategori) {
                                            'MF' => 'activity',
                                            'HF Rutin' => 'broadcast',
                                            'HF Nelayan' => 'pin',
                                            default => 'info',
                                        };
                                    @endphp
                                    <tr class="border-bottom border-slate-50">
                                        <td class="px-3 py-2">
                                            <div class="fw-bold text-dark" style="font-size: 0.65rem;">{{ $monitoring->created_at->format('d M Y') }}</div>
                                            <div class="text-slate-500 font-medium" style="font-size: 0.62rem;">{{ $monitoring->created_at->format('H:i') }} WIB</div>
                                        </td>
                                        <td>
                                            <div class="d-inline-flex align-items-center gap-1 px-2 py-1 rounded-pill border {{ $categoryColor }}" style="font-size: 0.65rem;">
                                                @if ($monitoring->kategori === 'MF')
                                                    <x-icon icon="medium_frequency" width="14" height="14" stroke="currentColor" />
                                                @elseif ($monitoring->kategori === 'HF Rutin')
                                                    <x-icon icon="rutin" width="14" height="14" stroke="currentColor" />
                                                @elseif ($monitoring->kategori === 'HF Nelayan')
                                                    <x-icon icon="nelayan" width="14" height="14" stroke="currentColor" />
                                                @else
                                                    <x-icon icon="dashboard" width="14" height="14" stroke="currentColor" />
                                                @endif
                                                <span class="fw-bold">{{ $monitoring->kategori }}</span>
                                            </div>
                                        </td>
                                        <td class="text-dark font-medium" style="font-size: 0.65rem;">{{ $monitoring->tahun }}</td>
                                        <td class="text-dark font-medium" style="font-size: 0.65rem;">
                                            @php
                                                $monthNames = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                                                echo $monthNames[$monitoring->bulan - 1] ?? $monitoring->bulan;
                                            @endphp
                                        </td>
                                        <td class="text-dark font-medium" style="font-size: 0.65rem;">{{ str_pad($monitoring->tanggal, 2, '0', STR_PAD_LEFT) }}</td>
                                        <td class="px-3">
                                            <div class="d-flex align-items-center gap-2">
                                                @if($monitoring->user && $monitoring->user->profile_photo)
                                                    <img src="{{ asset('storage/' . $monitoring->user->profile_photo) }}" 
                                                         class="avatar-circle-sm" 
                                                         title="Klik untuk lihat foto"
                                                         onclick="viewFullAvatar(this.src, '{{ addslashes($monitoring->user->name) }}')">
                                                @else
                                                    <div class="avatar-circle-sm d-flex align-items-center justify-content-center bg-slate-100 text-slate-400" 
                                                         style="font-size: 0.6rem; font-weight: 800; cursor: default;">
                                                        {{ strtoupper(substr($monitoring->user->name ?? '?', 0, 1)) }}
                                                    </div>
                                                @endif
                                                <span class="fw-bold {{ ($monitoring->user->role ?? '') === 'super_admin' ? 'text-blue-600' : 'text-slate-700' }}" style="font-size: 0.65rem;">
                                                    {{ $monitoring->user->name ?? 'System' }}
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center opacity-25">
                                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#999" stroke-width="1.5" style="margin-bottom: 0.5rem;"><rect x="2" y="7" width="20" height="13" rx="2" ry="2"/><path d="M16 4H8"/><circle cx="12" cy="18" r="2"/></svg>
                                                <div class="fw-bold" style="font-size: 0.75rem;">Belum ada monitoring data</div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        /**
         * Unified Dashboard Initialization
         * Handles both initial load and Livewire SPA navigation.
         */
        window.initDashboardCharts = function() {
            const barCanvas = document.getElementById('barChart');
            const monthlyCanvas = document.getElementById('monthlyChart');
            
            if (!barCanvas || !monthlyCanvas) return;

            // Cleanup existing instances to prevent memory leaks and "canvas in use" errors
            if (window.balmonBarChart) {
                window.balmonBarChart.destroy();
                window.balmonBarChart = null;
            }
            if (window.balmonMonthlyChart) {
                window.balmonMonthlyChart.destroy();
                window.balmonMonthlyChart = null;
            }

            // Baca data dari attribute data-chart yang diperbarui Livewire
            let barData = { labels: [], datasets: { mf: [], rutin: [], nelayan: [] } };
            let monthlyData = { labels: [], datasets: { mf: [], rutin: [], nelayan: [] }, year: '' };
            try { barData = JSON.parse(barCanvas.getAttribute('data-chart')); } catch(e) {}
            try { monthlyData = JSON.parse(monthlyCanvas.getAttribute('data-chart')); } catch(e) {}
            const corporateColor = '#0f172a';

            const chartDefaults = {
                responsive: true,
                maintainAspectRatio: false,
                animation: false, // Dimatikan agar tidak ada efek "kedip naik turun" saat navigasi SPA
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        labels: {
                            font: { family: "'Plus Jakarta Sans', sans-serif", weight: '700', size: 10 },
                            color: corporateColor,
                            usePointStyle: true,
                            padding: 15
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.95)',
                        titleFont: { family: "'Plus Jakarta Sans', sans-serif", size: 11, weight: '700' },
                        bodyFont: { family: "'Plus Jakarta Sans', sans-serif", size: 10, weight: '600' },
                        padding: 10,
                        cornerRadius: 8,
                        boxPadding: 4,
                        usePointStyle: true
                    }
                }
            };

            const barCtx = barCanvas.getContext('2d');
            const blueGrad = barCtx.createLinearGradient(0, 0, 0, 300);
            blueGrad.addColorStop(0, '#3b82f6'); blueGrad.addColorStop(1, '#1d4ed8');
            const emeraldGrad = barCtx.createLinearGradient(0, 0, 0, 300);
            emeraldGrad.addColorStop(0, '#34d399'); emeraldGrad.addColorStop(1, '#059669');
            const roseGrad = barCtx.createLinearGradient(0, 0, 0, 300);
            roseGrad.addColorStop(0, '#fb7185'); roseGrad.addColorStop(1, '#e11d48');

            window.balmonBarChart = new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: barData.labels,
                    datasets: [
                        { label: 'MF', data: barData.datasets.mf, backgroundColor: blueGrad, borderRadius: 5, barPercentage: 0.8 },
                        { label: 'HF Rutin', data: barData.datasets.rutin, backgroundColor: emeraldGrad, borderRadius: 5, barPercentage: 0.8 },
                        { label: 'HF Nelayan', data: barData.datasets.nelayan, backgroundColor: roseGrad, borderRadius: 5, barPercentage: 0.8 }
                    ]
                },
                options: { 
                    ...chartDefaults, 
                    plugins: { legend: { display: false } },
                    scales: { 
                        y: { 
                            stacked: true, 
                            beginAtZero: true,
                            grid: { color: '#f8fafc' }, 
                            ticks: { font: { size: 9 }, precision: 0, stepSize: 1 } 
                        },
                        x: { 
                            stacked: true, 
                            grid: { display: false }, 
                            ticks: { font: { size: 9 } } 
                        }
                    } 
                }
            });

            const monthlyCtx = monthlyCanvas.getContext('2d');
            window.balmonMonthlyChart = new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: monthlyData.labels,
                    datasets: [
                        { label: 'MF', data: monthlyData.datasets.mf, backgroundColor: blueGrad, borderRadius: 5, barPercentage: 0.8 },
                        { label: 'HF Rutin', data: monthlyData.datasets.rutin, backgroundColor: emeraldGrad, borderRadius: 5, barPercentage: 0.8 },
                        { label: 'HF Nelayan', data: monthlyData.datasets.nelayan, backgroundColor: roseGrad, borderRadius: 5, barPercentage: 0.8 }
                    ]
                },
                options: { 
                    ...chartDefaults, 
                    plugins: { legend: { display: false } },
                    scales: { 
                        y: { 
                            stacked: true, 
                            beginAtZero: true,
                            grid: { color: '#f8fafc' }, 
                            ticks: { font: { size: 9 }, precision: 0, stepSize: 1 } 
                        },
                        x: { 
                            stacked: true, 
                            grid: { display: false }, 
                            ticks: { font: { size: 9 } } 
                        }
                    } 
                }
            });

            if (typeof lucide !== 'undefined') lucide.createIcons();
        };

        // Handle execution based on SPA vs Normal Load
        const spawnCharts = () => {
            if (document.getElementById('barChart')) {
                window.initDashboardCharts();
            } else {
                setTimeout(spawnCharts, 50); // wait until DOM swap finish
            }
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', spawnCharts);
        } else {
            // Give Livewire a tiny window to finish inserting the canvas
            setTimeout(spawnCharts, 100); 
        }

        // Just in case, also hook into navigated
        if (!window.hasBoundDashboardNavigated) {
            document.addEventListener('livewire:navigated', () => {
                if (document.getElementById('barChart')) setTimeout(window.initDashboardCharts, 100);
            });
            window.hasBoundDashboardNavigated = true;
        }
    </script>
@endsection