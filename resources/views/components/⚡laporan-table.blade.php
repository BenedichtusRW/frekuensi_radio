<?php

use Livewire\Volt\Component;
use App\Models\Monitoring;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public $readyToLoad = false;
    public $filters = [];

    /**
     * Initialization via wire:init to prevent "freeze" during initial render.
     */
    public function loadData()
    {
        $this->readyToLoad = true;
    }

    public function mount($filters = [])
    {
        $this->filters = $filters;
    }

    public function with()
    {
        if (!$this->readyToLoad) {
            return [
                'monitorings' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10)
            ];
        }

        $query = Monitoring::query()
            ->when($this->filters['kategori'] ?? null, fn($q, $v) => $q->where('kategori', $v))
            ->when($this->filters['bulan'] ?? null, fn($q, $v) => $q->where('bulan', $v))
            ->when($this->filters['tanggal'] ?? null, fn($q, $v) => $q->where('tanggal', $v))
            ->when($this->filters['tahun'] ?? null, fn($q, $v) => $q->where('tahun', $v))
            ->when($this->filters['q'] ?? null, function($q, $v) {
                $searchIn = $this->filters['search_in'] ?? 'identifikasi';
                $q->where($searchIn, 'like', "%{$v}%");
            })
            ->orderBy('tahun', 'DESC')
            ->orderBy('bulan', 'DESC')
            ->orderBy('tanggal', 'DESC')
            ->orderBy('jam_mulai', 'DESC')
            ->orderBy('id', 'DESC');

        return [
            'monitorings' => $query->paginate(10)
        ];
    }
}; ?>

<div wire:init="loadData" class="laporan-table-container" style="min-height: 400px;">
    @if($readyToLoad)
        <div id="tabel-frekuensi" class="card border-slate-200 overflow-hidden"
            style="background: #ffffff; border-radius: 1rem; border: none; contain: content;">
            <div class="card-header bg-white py-2 px-3 border-bottom border-slate-100 d-flex align-items-center justify-content-between gap-2">
                <div class="d-flex align-items-center gap-1">
                    <span class="fw-bold text-slate-500" style="font-size: 0.65rem;">ENTRIES</span>
                    <select class="form-select form-select-sm border-slate-200" style="width: 55px; font-size: 0.75rem; padding: 0.1rem 0.3rem;" disabled>
                        <option selected>10</option>
                    </select>
                </div>
                <div class="action-buttons-wrap d-flex align-items-center gap-1">
                    <button type="button" class="btn-minimal btn-action-responsive" data-bs-toggle="modal" data-bs-target="#addLaporanModal">
                        <span class="text-nowrap">Tambah Baru</span>
                    </button>
                    <a href="{{ route('monitoring.export', request()->query()) }}#daftar-laporan" class="btn-minimal btn-action-responsive">
                        <span class="text-nowrap">Export XLSX</span>
                    </a>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="mosfet-table-wrap">
                    <table class="table table-hover table-sm align-middle mosfet-table">
                        <colgroup>
                            <col style="width: 52px;"><col style="width: 96px;"><col style="width: 156px;"><col style="width: 120px;">
                            <col style="width: 88px;"><col style="width: 74px;"><col style="width: 74px;"><col style="width: 88px;">
                            <col style="width: 88px;"><col style="width: 136px;"><col style="width: 250px;"><col style="width: 160px;">
                            <col style="width: 112px;"><col style="width: 100px;"><col style="width: 106px;"><col style="width: 98px;">
                            <col style="width: 72px;"><col style="width: 98px;"><col style="width: 98px;"><col style="width: 72px;">
                            <col style="width: 98px;"><col style="width: 190px;"><col style="width: 128px;"><col style="width: 112px;">
                            <col style="width: 160px;"><col style="width: 180px;"><col style="width: 130px;"><col style="width: 100px;">
                        </colgroup>
                        <thead>
                            <tr class="header-group">
                                <th rowspan="4">No</th><th colspan="2">Monitoring Center</th><th colspan="22">Keterangan dari stasiun yang dimonitor</th>
                                <th rowspan="4">Jenis Laporan</th><th rowspan="4">Updated At</th><th rowspan="4">Aksi</th>
                            </tr>
                            <tr class="header-field">
                                <th rowspan="2">Kode Negara</th><th rowspan="2">Stasiun Monitor</th><th rowspan="2">Frekuensi (KHz)</th>
                                <th colspan="3">Waktu Pengamatan</th><th colspan="2">Jam Pengamatan</th>
                                <th rowspan="2">Kuat Medan (dBuV/m)</th><th rowspan="2">Identifikasi</th><th rowspan="2">Administrasi Termonitor</th>
                                <th rowspan="2">Kelas Stasiun</th><th rowspan="2">Lebar Band</th><th rowspan="2">Kelas Emisi</th>
                                <th colspan="6">Perkiraan Lokasi Sumber Pancaran</th><th rowspan="2">North Bearing</th>
                                <th rowspan="2">Akurasi</th><th rowspan="2">Tidak sesuai RR</th><th rowspan="2">Informasi Tambahan</th>
                            </tr>
                            <tr class="header-subfield">
                                <th>Tanggal</th><th>Bulan</th><th>Tahun</th><th>Jam Mulai</th><th>Jam Akhir</th>
                                <th>Long (0-180)</th><th>E/W</th><th>Long (0-59)</th><th>Lat (0-90)</th><th>N/S</th><th>Lat (0-59)</th>
                            </tr>
                            <tr class="column-label-row">
                                @for($i=1;$i<=24;$i++) <td>{{$i}}</td> @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($monitorings as $index => $item)
                                @php $rowNumber = $monitorings->firstItem() + $index; @endphp
                                <tr>
                                    <td>{{ $rowNumber }}</td>
                                    <td>{{ $item->kode_negara }}</td>
                                    <td>{{ $item->stasiun_monitor }}</td>
                                    <td>{{ $item->frekuensi_khz }}</td>
                                    <td>{{ $item->tanggal }}</td>
                                    <td>{{ $item->bulan }}</td>
                                    <td>{{ $item->tahun }}</td>
                                    <td>{{ $item->jam_mulai }}</td>
                                    <td>{{ $item->jam_akhir }}</td>
                                    <td>{{ $item->kuat_medan_dbuvm }}</td>
                                    <td>{{ $item->identifikasi }}</td>
                                    <td>{{ $item->administrasi_termonitor }}</td>
                                    <td>{{ $item->kelas_stasiun }}</td>
                                    <td>{{ $item->lebar_band }}</td>
                                    <td>{{ $item->kelas_emisi }}</td>
                                    <td>{{ $item->longitude_derajat }}</td>
                                    <td>{{ $item->longitude_arah }}</td>
                                    <td>{{ $item->longitude_menit }}</td>
                                    <td>{{ $item->latitude_derajat }}</td>
                                    <td>{{ $item->latitude_arah }}</td>
                                    <td>{{ $item->latitude_menit }}</td>
                                    <td>{{ $item->north_bearing }}</td>
                                    <td>{{ $item->akurasi }}</td>
                                    <td>{{ $item->tidak_sesuai_rr }}</td>
                                    <td>{{ $item->informasi_tambahan }}</td>
                                    <td>
                                        <span class="badge rounded-pill bg-light text-dark border">{{ $item->kategori }}</span>
                                    </td>
                                    <td>{{ $item->updated_at?->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button type="button" class="action-pill view" data-bs-toggle="modal" data-bs-target="#detailLaporanModal{{ $item->id }}">
                                                <x-icon icon="lihat" width="16" height="16" />
                                            </button>
                                            <a href="{{ route('monitoring.edit', ['id' => $item->id, 'no' => $rowNumber]) }}" class="action-pill edit">
                                                <x-icon icon="edit" width="16" height="16" />
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="28" class="text-center py-4 text-muted">Belum ada data harian.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($monitorings->hasPages())
                <div class="card-footer bg-white border-top border-slate-100">
                    {{ $monitorings->links() }}
                </div>
            @endif
        </div>

        {{-- Modals for each row --}}
        @foreach ($monitorings as $item)
            @php $rowNumber = $monitorings->firstItem() + $loop->index; @endphp
            <div class="modal fade" id="detailLaporanModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Detail Tabel {{ $rowNumber }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <tbody style="font-size: 0.85rem;">
                                        <tr><th class="bg-light" style="width: 25%;">Kode Negara</th><td>{{ $item->kode_negara ?: '-' }}</td></tr>
                                        <tr><th class="bg-light">Stasiun Monitor</th><td>{{ $item->stasiun_monitor ?: '-' }}</td></tr>
                                        <tr><th class="bg-light">Frekuensi (KHz)</th><td>{{ $item->frekuensi_khz ?: '-' }}</td></tr>
                                        <tr><th class="bg-light">Waktu</th><td>{{ $item->tanggal }}-{{ $item->bulan }}-{{ $item->tahun }}</td></tr>
                                        <tr><th class="bg-light">Jam</th><td>{{ $item->jam_mulai }} - {{ $item->jam_akhir }}</td></tr>
                                        <tr><th class="bg-light">Kuat Medan</th><td>{{ $item->kuat_medan_dbuvm ?: '-' }}</td></tr>
                                        <tr><th class="bg-light">Identifikasi</th><td>{{ $item->identifikasi ?: '-' }}</td></tr>
                                        <tr><th class="bg-light">Administrasi</th><td>{{ $item->administrasi_termonitor ?: '-' }}</td></tr>
                                        <tr><th class="bg-light">Kelas Stasiun</th><td>{{ $item->kelas_stasiun ?: '-' }}</td></tr>
                                        <tr><th class="bg-light">Lebar Band</th><td>{{ $item->lebar_band ?: '-' }}</td></tr>
                                        <tr><th class="bg-light">Kelas Emisi</th><td>{{ $item->kelas_emisi ?: '-' }}</td></tr>
                                        <tr><th class="bg-light">Koordinat</th><td>{{ $item->longitude_derajat }}°{{ $item->longitude_arah }}' {{ $item->longitude_menit }}" / {{ $item->latitude_derajat }}°{{ $item->latitude_arah }}' {{ $item->latitude_menit }}"</td></tr>
                                        <tr><th class="bg-light">Informasi Tambahan</th><td>{{ $item->informasi_tambahan ?: '-' }}</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white" style="height: 450px; display: flex; align-items: center; justify-content: center;">
            <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status"></div>
            <h5 class="text-slate-600 fw-bold">Menyiapkan Laporan...</h5>
            <p class="text-slate-400 small">Optimalisasi render sedang berlangsung</p>
        </div>
    @endif
</div>
