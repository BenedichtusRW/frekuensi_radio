@extends('layouts.app')

@section('title', 'Dashboard Overview - Balmon Lampung')
@section('page_title', 'Dashboard')

@section('content')
    <!-- Metric Cards Row (Tightened) -->
    <div class="row g-3 mb-3">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-slate-200 shadow-sm h-100 overflow-hidden" style="background: #ffffff; position: relative; border-radius: 1.25rem;">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-slate-50 p-2.5 text-slate-700 border border-slate-100">
                        <i data-lucide="database" size="18"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-slate-500 text-uppercase tracking-wider mb-0.5" style="font-size: 0.65rem;">Total Data</div>
                        <div class="h5 fw-bold mb-0 text-dark">{{ number_format($summary['total_all'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-slate-200 shadow-sm h-100 overflow-hidden" style="background: #ffffff; position: relative; border-radius: 1.25rem;">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-blue-50 p-2.5 text-blue-600 border border-blue-100">
                        <i data-lucide="activity" size="18"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-slate-500 text-uppercase tracking-wider mb-0.5" style="font-size: 0.65rem;">HF Medium Frequency</div>
                        <div class="h5 fw-bold mb-0 text-dark">{{ number_format($summary['mf_all'] ?? 0) }}</div>
                    </div>
                </div>
                <!-- Color Indicator -->
                <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 3px; background: #2563eb;"></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-slate-200 shadow-sm h-100 overflow-hidden" style="background: #ffffff; position: relative; border-radius: 1.25rem;">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-emerald-50 p-2.5 text-emerald-600 border border-emerald-100">
                        <i data-lucide="radio" size="18"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-slate-500 text-uppercase tracking-wider mb-0.5" style="font-size: 0.65rem;">HF Rutin</div>
                        <div class="h5 fw-bold mb-0 text-dark">{{ number_format($summary['rutin_all'] ?? 0) }}</div>
                    </div>
                </div>
                <!-- Color Indicator -->
                <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 3px; background: #10b981;"></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-slate-200 shadow-sm h-100 overflow-hidden" style="background: #ffffff; position: relative; border-radius: 1.25rem;">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-rose-50 p-2.5 text-rose-600 border border-rose-100">
                        <i data-lucide="anchor" size="18"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-slate-500 text-uppercase tracking-wider mb-0.5" style="font-size: 0.65rem;">HF Nelayan</div>
                        <div class="h5 fw-bold mb-0 text-dark">{{ number_format($summary['nelayan_all'] ?? 0) }}</div>
                    </div>
                </div>
                <!-- Color Indicator -->
                <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 3px; background: #f43f5e;"></div>
            </div>
        </div>
    </div>

    <!-- Main Charts Row (Side-by-Side Stacked) -->
    <div class="row g-3 mb-3">
        <div class="col-12 col-lg-6">
            <div class="card border-slate-200 shadow-sm overflow-hidden h-100" style="background: #ffffff; border-radius: 1.25rem;">
                <div class="card-header bg-white py-2.5 border-bottom border-slate-50 d-flex align-items-center gap-2">
                    <i data-lucide="bar-chart-3" class="text-dark" size="16"></i>
                    <span class="fw-bold text-dark" style="font-size: 0.85rem;">Aktivitas 7 Hari Terakhir</span>
                </div>
                <div class="card-body py-2">
                    <div style="height: 240px;">
                        <canvas id="barChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card border-slate-200 shadow-sm overflow-hidden h-100" style="background: #ffffff; border-radius: 1.25rem;">
                <div class="card-header bg-white py-2.5 border-bottom border-slate-50 d-flex align-items-center gap-2">
                    <i data-lucide="trending-up" class="text-dark" size="16"></i>
                    <span class="fw-bold text-dark" style="font-size: 0.85rem;">Aktivitas Monitoring {{ $monthlyChart['year'] ?? date('Y') }}</span>
                </div>
                <div class="card-body py-2">
                    <div style="height: 240px;">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Log Row (Tightened) -->
    <div class="card border-0 shadow-sm mb-4" style="background: #ffffff; border-radius: 1.25rem;">
        <div class="card-header bg-white py-2.5 border-0 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i data-lucide="history" class="text-dark" size="16"></i>
                <span class="fw-bold text-dark" style="font-size: 0.85rem;">Log Aktivitas Sistem</span>
            </div>
            <span class="text-slate-400 fw-medium" style="font-size: 0.7rem;">Menampilkan 20 rekaman terbaru</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.78rem;">
                    <thead>
                        <tr class="bg-slate-50 border-bottom">
                            <th class="px-3 py-2 text-dark font-bold text-uppercase tracking-wider" style="font-size: 0.6rem;">Waktu</th>
                            <th class="py-2 text-dark font-bold text-uppercase tracking-wider" style="font-size: 0.6rem;">Aksi</th>
                            <th class="py-2 text-dark font-bold text-uppercase tracking-wider" style="font-size: 0.6rem;">Deskripsi</th>
                            <th class="py-2 text-dark font-bold text-uppercase tracking-wider" style="font-size: 0.6rem;">Platform</th>
                            <th class="py-2 text-dark font-bold text-uppercase tracking-wider text-end px-3" style="font-size: 0.6rem;">Alamat IP</th>
                        </tr>
                    </thead>
                    <tbody class="border-0">
                        @forelse ($activityLogs as $log)
                            @php
                                $badgeClass = match ($log->action) {
                                    'add_data' => 'text-emerald-700 bg-emerald-100 border-emerald-200',
                                    'edit_data' => 'text-amber-700 bg-amber-100 border-amber-200',
                                    'export' => 'text-purple-700 bg-purple-100 border-purple-200',
                                    'visit_dashboard' => 'text-blue-700 bg-blue-100 border-blue-200',
                                    'visit_laporan' => 'text-slate-700 bg-slate-100 border-slate-200',
                                    default => 'text-slate-700 bg-slate-50 border-slate-200',
                                };
                                $iconName = match ($log->action) {
                                    'add_data' => 'plus-circle',
                                    'edit_data' => 'edit',
                                    'export' => 'download',
                                    'visit_dashboard' => 'home',
                                    'visit_laporan' => 'file-text',
                                    'visit_input' => 'clipboard',
                                    default => 'info',
                                };
                                $labelText = match ($log->action) {
                                    'add_data' => 'Tambah Data',
                                    'edit_data' => 'Edit Data',
                                    'export' => 'Export',
                                    'visit_dashboard' => 'Dashboard',
                                    'visit_laporan' => 'Laporan',
                                    'visit_input' => 'Input',
                                    default => Str::headline($log->action),
                                };
                            @endphp
                            <tr class="border-bottom border-slate-50">
                                <td class="px-3 py-2">
                                    <div class="fw-bold text-dark">{{ $log->created_at->format('d M') }}</div>
                                    <div class="text-slate-500 font-medium" style="font-size: 0.7rem;">{{ $log->created_at->format('H:i') }} WIB</div>
                                </td>
                                <td>
                                    <div class="d-inline-flex align-items-center gap-1.5 px-2 py-1 rounded-pill border {{ $badgeClass }}" style="font-size: 0.7rem;">
                                        <i data-lucide="{{ $iconName }}" size="10"></i>
                                        <span class="fw-bold">{{ $labelText }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-dark font-medium line-clamp-1" style="max-width: 280px;">{{ $log->description }}</div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1.5">
                                        <div class="p-1 rounded-3 bg-slate-50 text-dark border">
                                            @php
                                                $osStr = strtolower($log->platform);
                                                $osIcon = str_contains($osStr, 'windows') ? 'monitor' : (str_contains($osStr, 'android') ? 'smartphone' : (str_contains($osStr, 'ios') ? 'smartphone' : 'cpu'));
                                            @endphp
                                            <i data-lucide="{{ $osIcon }}" size="12"></i>
                                        </div>
                                        <div>
                                            <div class="text-dark font-bold" style="font-size: 0.7rem;">{{ $log->platform }}</div>
                                            <div class="text-slate-500" style="font-size: 0.65rem;">{{ $log->browser }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-2 text-end font-monospace text-dark" style="font-size: 0.7rem;">
                                    {{ $log->ip_address }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i data-lucide="inbox" class="text-slate-300 mb-2" size="32"></i>
                                    <div class="text-slate-400" style="font-size: 0.75rem;">Belum ada aktivitas tercatat.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const barData = @json($barChart ?? ['labels' => [], 'values' => []]);
            const monthlyData = @json($monthlyChart ?? ['labels' => [], 'values' => [], 'year' => '']);

            // Standard Corporate Navy Identity
            const corporateColor = '#0f172a';

            // Shared Config (Tightened & Interactive)
            const chartDefaults = {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        labels: {
                            font: { family: "'Plus Jakarta Sans', sans-serif", weight: '700', size: 10 },
                            color: '#0f172a',
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

            // 1. Aktivitas 7 Hari (Premium Stacked Capsules)
            const barCtx = document.getElementById('barChart').getContext('2d');
            
            // Create Gradients
            const blueGrad = barCtx.createLinearGradient(0, 0, 0, 300);
            blueGrad.addColorStop(0, '#3b82f6'); blueGrad.addColorStop(1, '#1d4ed8');
            
            const emeraldGrad = barCtx.createLinearGradient(0, 0, 0, 300);
            emeraldGrad.addColorStop(0, '#34d399'); emeraldGrad.addColorStop(1, '#059669');
            
            const roseGrad = barCtx.createLinearGradient(0, 0, 0, 300);
            roseGrad.addColorStop(0, '#fb7185'); roseGrad.addColorStop(1, '#e11d48');

            new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: barData.labels,
                    datasets: [
                        {
                            label: 'HF Medium Frequency',
                            data: barData.datasets.mf,
                            backgroundColor: blueGrad,
                            borderRadius: 5,
                            borderSkipped: false,
                            barPercentage: 0.8,
                            categoryPercentage: 0.8
                        },
                        {
                            label: 'HF Rutin',
                            data: barData.datasets.rutin,
                            backgroundColor: emeraldGrad,
                            borderRadius: 5,
                            borderSkipped: false,
                            barPercentage: 0.8,
                            categoryPercentage: 0.8
                        },
                        {
                            label: 'HF Nelayan',
                            data: barData.datasets.nelayan,
                            backgroundColor: roseGrad,
                            borderRadius: 5,
                            borderSkipped: false,
                            barPercentage: 0.8,
                            categoryPercentage: 0.8
                        }
                    ]
                },
                options: {
                    ...chartDefaults,
                    plugins: { 
                        ...chartDefaults.plugins, 
                        legend: { display: false } 
                    },
                    scales: {
                        y: { 
                            stacked: true,
                            beginAtZero: true, 
                            grid: { color: '#f8fafc', drawTicks: false }, 
                            border: { display: false }, 
                            ticks: { color: '#94a3b8', font: { weight: '600', size: 9 }, precision: 0, padding: 10 } 
                        },
                        x: { 
                            stacked: true,
                            grid: { display: false }, 
                            border: { display: false }, 
                            ticks: { color: '#94a3b8', font: { weight: '600', size: 9 }, padding: 10 } 
                        }
                    }
                }
            });

            // 2. Aktivitas Monitoring Bulanan (Premium Stacked Capsules)
            const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
            new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: monthlyData.labels,
                    datasets: [
                        {
                            label: 'HF Medium Frequency',
                            data: monthlyData.datasets.mf,
                            backgroundColor: blueGrad,
                            borderRadius: 5,
                            borderSkipped: false,
                            barPercentage: 0.8,
                            categoryPercentage: 0.8
                        },
                        {
                            label: 'HF Rutin',
                            data: monthlyData.datasets.rutin,
                            backgroundColor: emeraldGrad,
                            borderRadius: 5,
                            borderSkipped: false,
                            barPercentage: 0.8,
                            categoryPercentage: 0.8
                        },
                        {
                            label: 'HF Nelayan',
                            data: monthlyData.datasets.nelayan,
                            backgroundColor: roseGrad,
                            borderRadius: 5,
                            borderSkipped: false,
                            barPercentage: 0.8,
                            categoryPercentage: 0.8
                        }
                    ]
                },
                options: {
                    ...chartDefaults,
                    plugins: { 
                        ...chartDefaults.plugins, 
                        legend: { display: false } 
                    },
                    scales: {
                        y: { 
                            stacked: true,
                            beginAtZero: true, 
                            grid: { color: '#f8fafc', drawTicks: false }, 
                            border: { display: false }, 
                            ticks: { color: '#94a3b8', font: { weight: '600', size: 9 }, precision: 0, padding: 10 } 
                        },
                        x: { 
                            stacked: true,
                            grid: { display: false }, 
                            border: { display: false }, 
                            ticks: { color: '#94a3b8', font: { weight: '600', size: 9 }, padding: 10 } 
                        }
                    }
                }
            });
            
            // Re-init Lucide for inside cards
            lucide.createIcons();
        });
    </script>
@endsection