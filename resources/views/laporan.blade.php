@extends('layouts.app')

@section('title', $pageTitle ?? 'Daftar Laporan Harian')
@section('page_title', $pageTitle ?? 'Daftar Laporan')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <style>
        .laporan-title {
            font-size: 0.92rem;
            font-weight: 700;
            letter-spacing: 0.03em;
        }

        .laporan-filter-label {
            font-size: 0.76rem;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
        }

        .toolbar-row {
            padding: 0.55rem 0.75rem;
            border-bottom: 1px solid #e5e7eb;
            background: #fafafa;
        }

        .toolbar-meta {
            font-size: 0.72rem;
            color: #6b7280;
        }

        .toolbar-meta .form-select {
            font-size: 0.72rem;
            padding-top: 0.18rem;
            padding-bottom: 0.18rem;
        }

        .toolbar-actions .btn {
            font-size: 0.7rem;
            padding: 0.26rem 0.52rem;
            border-radius: 0.25rem;
        }

        .date-filter-group .form-control {
            border: 1px solid #94a3b8;
            background-color: #f8fafc;
            box-shadow: inset 0 1px 1px rgba(15, 23, 42, 0.04);
        }

        .date-filter-group .form-control:focus {
            border-color: #64748b;
            box-shadow: 0 0 0 0.12rem rgba(100, 116, 139, 0.16);
        }

        .search-scope-toggle {
            min-width: 120px;
        }

        .mosfet-table-wrap {
            overflow-x: auto;
            max-height: 68vh;
        }

        .mosfet-table {
            min-width: 2450px;
            font-size: 0.72rem;
            margin-bottom: 0;
            border-collapse: collapse;
        }

        .mosfet-table thead th {
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
            border: 1.5px solid #000000 !important;
            background: #ffffff;
            font-weight: 700;
            position: sticky;
            z-index: 5;
            line-height: 1.2;
            padding: 0.45rem 0.5rem;
            background-clip: padding-box;
        }

        .mosfet-table thead tr.header-group th {
            background: #ffffff;
            font-size: 0.75rem;
            top: 0;
            z-index: 8;
            height: 42px;
        }

        .mosfet-table thead tr.header-field th {
            top: 42px;
            z-index: 7;
            font-size: 0.7rem;
            height: 42px;
        }

        .mosfet-table thead tr.header-group th[rowspan] {
            z-index: 9;
        }

        .mosfet-table thead tr.header-subfield th {
            top: 84px;
            z-index: 6;
            font-size: 0.7rem;
            height: 42px;
        }

        .mosfet-table tbody td {
            border: 1px solid #000000;
            white-space: nowrap;
            vertical-align: middle;
            text-align: center;
        }

        .mosfet-table .column-label-row td {
            background: #fde9d9;
            border: 1px solid #000000;
            font-weight: 500;
            text-align: center !important;
        }

        .mosfet-table .column-label-row td:first-child {
            background: #fde9d9;
        }

        .mosfet-table tbody td:nth-child(3),
        .mosfet-table tbody td:nth-child(11) {
            text-align: left;
        }

        .mosfet-table .break-date-row td {
            background: #fff200;
            font-weight: 600;
        }

        .action-pill {
            width: 22px;
            height: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.2rem;
            border: 1px solid #d1d5db;
            text-decoration: none;
            background: #f8fafc;
            font-size: 0.68rem;
            line-height: 1;
        }

        .action-pill:hover {
            filter: brightness(0.96);
        }

        .action-pill.edit {
            color: #2563eb;
            border-color: #93c5fd;
            background: #eff6ff;
        }

        .action-pill.view {
            color: #0f766e;
            border-color: #99f6e4;
            background: #ecfeff;
        }

        .action-pill.delete {
            color: #dc2626;
            border-color: #fca5a5;
            background: #fef2f2;
        }

        .badge-kategori {
            display: inline-block;
            padding: 0.2rem 0.45rem;
            border-radius: 0.35rem;
            font-size: 0.72rem;
            font-weight: 700;
            line-height: 1.1;
            border: 1px solid transparent;
        }

        .badge-mf {
            background: #dbeafe;
            color: #1d4ed8;
            border-color: #93c5fd;
        }

        .badge-rutin {
            background: #dcfce7;
            color: #166534;
            border-color: #86efac;
        }

        .badge-nelayan {
            background: #ffedd5;
            color: #9a3412;
            border-color: #fdba74;
        }

        #addLaporanModal .modal-content {
            height: calc(100vh - 2rem);
        }

        #addLaporanModal .modal-body {
            max-height: calc(100vh - 190px);
            overflow-y: auto !important;
            padding-bottom: 1.5rem;
        }

        #addLaporanModal .modal-footer {
            position: sticky;
            bottom: 0;
            background: #fff;
            z-index: 2;
        }

        #addLaporanModal textarea[name="informasi_tambahan"] {
            min-height: 110px;
            resize: vertical;
        }

        .detail-section-title {
            background: #dde2e6;
            border: 1px solid #adb5bd;
            padding: 0.38rem 0.55rem;
            font-weight: 700;
            margin-bottom: 0;
        }

        .detail-subtable {
            border: 1px solid #adb5bd;
            margin-bottom: 1rem;
        }

        .detail-subtable thead th {
            background: #e9ecef;
            border: 1px solid #adb5bd;
            white-space: nowrap;
        }

        .detail-subtable tbody td {
            border: 1px solid #ced4da;
        }
        .btn-link-reset:hover {
            color: #ef4444 !important; /* Red-500 */
        }
        .btn-link-reset:hover i {
            color: #ef4444 !important;
        }
    </style>

    <div class="card border-0 shadow-sm mb-3" style="background: #ffffff; border-radius: 1.25rem; box-shadow: 0 10px 30px -15px rgba(0,0,0,0.08) !important;">
        <div class="card-body py-3 px-3">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i data-lucide="filter" class="text-blue-500" size="12"></i>
                <span class="fw-bold text-slate-800" style="font-size: 0.65rem; letter-spacing: 0.05em;">FILTER PENCARIAN</span>
            </div>
            <form id="laporanFilterForm" method="GET" action="{{ route('monitoring.index') }}"
                class="row g-2 align-items-end">
                <div class="col-12 col-md-3 col-lg-2">
                    <label class="form-label text-xs fw-medium text-slate-500 text-uppercase mb-0.5" style="font-size: 0.75rem;">Kategori</label>
                    <select name="kategori" class="form-select form-select-sm border-slate-200 rounded-3" style="font-size: 0.85rem; height: 32px;">
                        <option value="">Semua kategori</option>
                        <option value="HF Nelayan" {{ ($filters['kategori'] ?? '') === 'HF Nelayan' ? 'selected' : '' }}>HF Nelayan</option>
                        <option value="HF Rutin" {{ ($filters['kategori'] ?? '') === 'HF Rutin' ? 'selected' : '' }}>HF Rutin</option>
                        <option value="MF" {{ ($filters['kategori'] ?? '') === 'MF' ? 'selected' : '' }}>HF Medium Frequency</option>
                    </select>
                </div>
                <div class="col-6 col-md-2 col-lg-1">
                    <label class="form-label text-xs fw-medium text-slate-500 text-uppercase mb-0.5" style="font-size: 0.75rem;">Hari</label>
                    <input type="number" name="tanggal" class="form-control form-control-sm border-slate-200 rounded-3" style="font-size: 0.85rem; height: 32px;" min="1" max="31" placeholder="1-31" value="{{ $filters['tanggal'] ?? '' }}">
                </div>
                <div class="col-6 col-md-2 col-lg-1">
                    <label class="form-label text-xs fw-medium text-slate-500 text-uppercase mb-0.5" style="font-size: 0.75rem;">Bulan</label>
                    <input type="number" name="bulan" class="form-control form-control-sm border-slate-200 rounded-3" style="font-size: 0.85rem; height: 32px;" min="1" max="12" placeholder="1-12" value="{{ $filters['bulan'] ?? '' }}">
                </div>
                <div class="col-12 col-md-2 col-lg-1">
                    <label class="form-label text-xs fw-medium text-slate-500 text-uppercase mb-0.5" style="font-size: 0.75rem;">Tahun</label>
                    <input type="number" name="tahun" class="form-control form-control-sm border-slate-200 rounded-3" style="font-size: 0.85rem; height: 32px;" min="2000" max="2100" placeholder="YYYY" value="{{ $filters['tahun'] ?? '' }}">
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <label class="form-label text-xs fw-medium text-slate-500 text-uppercase mb-0.5" style="font-size: 0.75rem;">Kata Kunci</label>
                    <div class="input-group input-group-sm">
                        @php
                            $searchIn = $filters['search_in'] ?? 'identifikasi';
                            $searchInLabel = match ($searchIn) {
                                'identifikasi' => 'Identifikasi',
                                'frekuensi_khz' => 'Frekuensi',
                                'stasiun_monitor' => 'Stasiun',
                                'administrasi_termonitor' => 'Administrasi',
                                default => 'Identifikasi',
                            };
                        @endphp
                        <input type="hidden" name="search_in" id="searchInHidden" value="{{ $searchIn }}">
                        <input id="filterSearchInput" type="text" name="q" class="form-control border-slate-200"
                            style="font-size: 0.85rem; height: 32px;"
                            placeholder="Cari data..." value="{{ $filters['q'] ?? '' }}">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false" id="searchInToggle" style="font-size: 0.8rem; height: 32px;">
                            {{ $searchInLabel }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-slate-200 rounded-3" style="font-size: 0.8rem;">
                            <li><button class="dropdown-item search-scope-option" type="button" data-value="identifikasi" data-label="Identifikasi">Identifikasi</button></li>
                            <li><button class="dropdown-item search-scope-option" type="button" data-value="frekuensi_khz" data-label="Frekuensi">Frekuensi</button></li>
                            <li><button class="dropdown-item search-scope-option" type="button" data-value="stasiun_monitor" data-label="Stasiun">Stasiun</button></li>
                            <li><button class="dropdown-item search-scope-option" type="button" data-value="administrasi_termonitor" data-label="Administrasi">Administrasi</button></li>
                        </ul>
                    </div>
                </div>
                <div class="col-12 col-md-4 col-lg-3 d-flex gap-2 align-items-center justify-content-start ps-3">
                    <button type="submit" class="btn btn-blue-500 text-white btn-sm rounded-3 d-flex align-items-center gap-1.5 px-3 fw-medium shadow-sm" style="font-size: 0.8rem; height: 32px; background-color: #3b82f6; border: none;">
                        <i data-lucide="search" size="14"></i> Filter
                    </button>
                    <a href="{{ route('monitoring.index') }}" class="btn btn-outline-slate-200 text-slate-400 btn-sm rounded-3 d-flex align-items-center gap-1.5 px-3 fw-medium btn-link-reset transition-all" style="font-size: 0.8rem; height: 32px; border: 1px solid #e2e8f0; text-decoration: none;">
                        <i data-lucide="refresh-cw" size="14"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>


    <div id="tabel-frekuensi" class="card border-0 shadow-sm overflow-hidden" style="background: #ffffff; border-radius: 1.25rem; box-shadow: 0 10px 30px -15px rgba(0,0,0,0.08) !important;">
        <div class="card-header bg-white py-2.5 px-3 border-bottom border-slate-50 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <span class="text-xs fw-bold text-slate-500 text-uppercase tracking-wider" style="font-size: 0.65rem;">Entries</span>
                <select class="form-select form-select-sm border-slate-200" style="width: 60px; font-size: 0.75rem;" disabled>
                    <option selected>10</option>
                </select>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <button type="button" class="btn btn-emerald btn-sm rounded-3 d-flex align-items-center gap-2 px-3 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#addLaporanModal">
                    <i data-lucide="plus" size="16"></i> 
                    <span>Add New</span>
                </button>
                <a href="{{ route('monitoring.export', request()->query()) }}#daftar-laporan" class="btn btn-royal btn-sm rounded-3 d-flex align-items-center gap-2 px-3 fw-semibold shadow-sm text-decoration-none">
                    <i data-lucide="file-spreadsheet" size="16"></i> 
                    <span>Export XLSX</span>
                </a>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="mosfet-table-wrap">
                <table class="table table-hover table-sm align-middle mosfet-table">
                    <colgroup>
                        <col style="width: 52px;">
                        <col style="width: 96px;">
                        <col style="width: 156px;">
                        <col style="width: 120px;">
                        <col style="width: 88px;">
                        <col style="width: 74px;">
                        <col style="width: 74px;">
                        <col style="width: 88px;">
                        <col style="width: 88px;">
                        <col style="width: 136px;">
                        <col style="width: 250px;">
                        <col style="width: 160px;">
                        <col style="width: 112px;">
                        <col style="width: 100px;">
                        <col style="width: 106px;">
                        <col style="width: 98px;">
                        <col style="width: 72px;">
                        <col style="width: 98px;">
                        <col style="width: 98px;">
                        <col style="width: 72px;">
                        <col style="width: 98px;">
                        <col style="width: 190px;">
                        <col style="width: 128px;">
                        <col style="width: 112px;">
                        <col style="width: 92px;">
                        <col style="width: 116px;">
                        <col style="width: 210px;">
                        <col style="width: 74px;">
                    </colgroup>
                    <thead>
                        <tr class="header-group">
                            <th rowspan="4">No</th>
                            <th colspan="2">Monitoring Center</th>
                            <th colspan="22">Keterangan dari stasiun yang dimonitor</th>
                            <th rowspan="4">Jenis Laporan</th>
                            <th rowspan="4">Updated At</th>
                            <th rowspan="4">Aksi</th>
                        </tr>
                        <tr class="header-field">
                            <th rowspan="2">Kode Negara</th>
                            <th rowspan="2">Stasiun Monitor</th>
                            <th rowspan="2">Frekuensi (KHz)</th>
                            <th colspan="3">Waktu Pengamatan</th>
                            <th colspan="2">Jam Pengamatan</th>
                            <th rowspan="2">Kuat Medan (dBuV/m)</th>
                            <th rowspan="2">Identifikasi</th>
                            <th rowspan="2">Administrasi Termonitor</th>
                            <th rowspan="2">Kelas Stasiun</th>
                            <th rowspan="2">Lebar Band</th>
                            <th rowspan="2">Kelas Emisi</th>
                            <th colspan="6">Perkiraan Lokasi Sumber Pancaran</th>
                            <th rowspan="2">North Bearing</th>
                            <th rowspan="2">Akurasi</th>
                            <th rowspan="2">Tidak sesuai RR</th>
                            <th rowspan="2">Informasi Tambahan</th>
                        </tr>
                        <tr class="header-subfield">
                            <th>Tanggal</th>
                            <th>Bulan</th>
                            <th>Tahun</th>
                            <th>Jam Mulai</th>
                            <th>Jam Akhir</th>
                            <th>Long (0-180)</th>
                            <th>E/W</th>
                            <th>Long (0-59)</th>
                            <th>Lat (0-90)</th>
                            <th>N/S</th>
                            <th>Lat (0-59)</th>
                        </tr>
                        <tr class="column-label-row">
                            <td>1</td>
                            <td>2</td>
                            <td>3</td>
                            <td>4</td>
                            <td>5</td>
                            <td>6</td>
                            <td>7</td>
                            <td>8</td>
                            <td>9</td>
                            <td>10</td>
                            <td>11</td>
                            <td>12</td>
                            <td>13</td>
                            <td>14</td>
                            <td>15</td>
                            <td>16</td>
                            <td>17</td>
                            <td>18</td>
                            <td>19</td>
                            <td>20</td>
                            <td>21</td>
                            <td>22</td>
                            <td>23</td>
                            <td>24</td>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($monitorings->count() > 0)
                            @php
                                $pageRows = $monitorings->getCollection()->values();
                            @endphp
                            @foreach ($pageRows as $item)
                                            @php
                                                $rowNumber = ($monitorings->firstItem() ?? 1) + $loop->index;
                                                $dateKey = sprintf('%04d-%02d-%02d', (int) ($item->tahun ?? 0), (int) ($item->bulan ?? 0), (int) ($item->tanggal ?? 0));
                                                $nextItem = !$loop->last ? ($pageRows[$loop->index + 1] ?? null) : null;
                                                $nextDateKey = $nextItem
                                                    ? sprintf('%04d-%02d-%02d', (int) ($nextItem->tahun ?? 0), (int) ($nextItem->bulan ?? 0), (int) ($nextItem->tanggal ?? 0))
                                                    : null;
                                                $isBreakDateRow = $nextDateKey !== null && $dateKey !== $nextDateKey;
                                            @endphp
                                            <tr class="{{ $isBreakDateRow ? 'break-date-row' : '' }}">
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
                                                    @php
                                                        $kategoriLabel = match ($item->kategori) {
                                                            'MF' => 'Monitoring HF Medium Frequency',
                                                            'HF Rutin' => 'Monitoring HF Rutin',
                                                            'HF Nelayan' => 'Monitoring HF Nelayan',
                                                            default => $item->kategori,
                                                        };
                                                        $badgeClass = match ($item->kategori) {
                                                            'MF' => 'badge-mf',
                                                            'HF Rutin' => 'badge-rutin',
                                                            'HF Nelayan' => 'badge-nelayan',
                                                            default => 'badge-mf',
                                                        };
                                                    @endphp
                                                    <span class="badge-kategori {{ $badgeClass }}">{{ $kategoriLabel }}</span>
                                                </td>
                                                <td>{{ optional($item->updated_at)->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <div class="d-flex gap-1">
                                                        <button type="button" class="action-pill view" title="Lihat Detail"
                                                            data-bs-toggle="modal" data-bs-target="#detailLaporanModal{{ $item->id }}"
                                                            aria-label="Lihat Detail">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor"
                                                                viewBox="0 0 16 16" aria-hidden="true">
                                                                <path
                                                                    d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.12 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z" />
                                                                <path
                                                                    d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z" />
                                                            </svg>
                                                        </button>
                                                        <button type="button" class="action-pill edit" title="Edit" data-bs-toggle="modal"
                                                            data-bs-target="#editLaporanModal" data-edit-table-number="{{ $rowNumber }}"
                                                            data-update-url="{{ route('monitoring.update', $item->id) }}" data-monitoring="{{ json_encode([
                                    "id" => $item->id,
                                    "kategori" => $item->kategori,
                                    "kode_negara" => $item->kode_negara,
                                    "stasiun_monitor" => $item->stasiun_monitor,
                                    "frekuensi_khz" => $item->frekuensi_khz,
                                    "mulai_pengamatan" => $item->tahun && $item->bulan && $item->tanggal
                                        ? sprintf("%04d-%02d-%02dT%s", $item->tahun, $item->bulan, $item->tanggal, str_replace('.', ':', (string) $item->jam_mulai))
                                        : '',
                                    "selesai_pengamatan_waktu" => str_replace('.', ':', (string) $item->jam_akhir),
                                    "kuat_medan_dbuvm" => $item->kuat_medan_dbuvm,
                                    "identifikasi" => $item->identifikasi,
                                    "administrasi_termonitor" => $item->administrasi_termonitor,
                                    "kelas_stasiun" => $item->kelas_stasiun,
                                    "lebar_band" => $item->lebar_band,
                                    "kelas_emisi" => $item->kelas_emisi,
                                    "longitude_derajat" => $item->longitude_derajat,
                                    "longitude_arah" => $item->longitude_arah,
                                    "longitude_menit" => $item->longitude_menit,
                                    "latitude_derajat" => $item->latitude_derajat,
                                    "latitude_arah" => $item->latitude_arah,
                                    "latitude_menit" => $item->latitude_menit,
                                    "north_bearing" => $item->north_bearing,
                                    "akurasi" => $item->akurasi,
                                    "tidak_sesuai_rr" => $item->tidak_sesuai_rr,
                                    "perkiraan_lokasi_sumber_pancaran" => $item->perkiraan_lokasi_sumber_pancaran,
                                ]) }}">
                                                            &#9998;
                                                        </button>
                                                        <form action="{{ route('monitoring.destroy', $item->id) }}" method="POST"
                                                            onsubmit="return confirm('Hapus data ini?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="action-pill delete" type="submit" title="Hapus">&#128465;</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="28" class="text-center py-4 text-muted">Belum ada data harian.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        @if ($monitorings->hasPages())
            <div class="card-footer">
                {{ $monitorings->links() }}
            </div>
        @endif
    </div>

    @if ($monitorings->count() > 0)
        @foreach ($monitorings as $item)
            @php
                $rowNumber = ($monitorings->firstItem() ?? 1) + $loop->index;
            @endphp
            <div class="modal fade" id="detailLaporanModal{{ $item->id }}" tabindex="-1"
                aria-labelledby="detailLaporanModalLabel{{ $item->id }}" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="detailLaporanModalLabel{{ $item->id }}">Detail Tabel {{ $rowNumber }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="overflow-x-auto w-full">
                                <table class="w-full min-w-[1900px] border-collapse text-[10px] leading-tight text-center"
                                    style="border: 1px solid black !important;">
                                    <colgroup>
                                        <col style="width: 88px;">
                                        <col style="width: 74px;">
                                        <col style="width: 74px;">
                                        <col style="width: 88px;">
                                        <col style="width: 88px;">
                                        <col style="width: 136px;">
                                        <col style="width: 250px;">
                                        <col style="width: 160px;">
                                        <col style="width: 112px;">
                                        <col style="width: 100px;">
                                        <col style="width: 106px;">
                                        <col style="width: 98px;">
                                        <col style="width: 72px;">
                                        <col style="width: 98px;">
                                        <col style="width: 98px;">
                                        <col style="width: 72px;">
                                        <col style="width: 98px;">
                                        <col style="width: 190px;">
                                        <col style="width: 128px;">
                                        <col style="width: 112px;">
                                        <col style="width: 92px;">
                                        <col style="width: 116px;">
                                        <col style="width: 210px;">
                                    </colgroup>
                                    <thead class="bg-white text-slate-800">
                                        <tr style="height: 28px;">
                                            <th class="border border-black px-1 py-1 font-bold" colspan="2"
                                                style="border: 1px solid black !important;">Monitoring Center</th>
                                            <th class="border border-black px-1 py-1 font-bold" colspan="21"
                                                style="border: 1px solid black !important;">Keterangan dari stasiun yang dimonitor
                                            </th>
                                        </tr>
                                        <tr style="height: 34px;">
                                            <th class="border border-black px-1 py-1 font-bold" rowspan="2"
                                                style="border: 1px solid black !important;">Kode Negara</th>
                                            <th class="border border-black px-1 py-1 font-bold" rowspan="2"
                                                style="border: 1px solid black !important;">Stasiun Monitor</th>
                                            <th class="border border-black px-1 py-1 font-bold" rowspan="2"
                                                style="border: 1px solid black !important;">Frekuensi (KHz)</th>
                                            <th class="border border-black px-1 py-1 font-bold" colspan="2"
                                                style="border: 1px solid black !important;">Waktu</th>
                                            <th class="border border-black px-1 py-1 font-bold" colspan="2"
                                                style="border: 1px solid black !important;">Jam</th>
                                            <th class="border border-black px-1 py-1 font-bold" rowspan="2"
                                                style="border: 1px solid black !important;">Kuat Medan (dBµV/m)</th>
                                            <th class="border border-black px-1 py-1 font-bold" rowspan="2"
                                                style="border: 1px solid black !important;">Identifikasi</th>
                                            <th class="border border-black px-1 py-1 font-bold" rowspan="2"
                                                style="border: 1px solid black !important;">Administrasi Termonitor</th>
                                            <th class="border border-black px-1 py-1 font-bold" rowspan="2"
                                                style="border: 1px solid black !important;">Kelas Stasiun</th>
                                            <th class="border border-black px-1 py-1 font-bold" rowspan="2"
                                                style="border: 1px solid black !important;">Lebar Band</th>
                                            <th class="border border-black px-1 py-1 font-bold" rowspan="2"
                                                style="border: 1px solid black !important;">Kelas Emisi</th>
                                            <th class="border border-black px-1 py-1 font-bold" colspan="6"
                                                style="border: 1px solid black !important;">Perkiraan Lokasi Sumber Pancaran</th>
                                            <th class="border border-black px-1 py-1 font-bold" rowspan="2"
                                                style="border: 1px solid black !important;">North Bearing</th>
                                            <th class="border border-black px-1 py-1 font-bold" rowspan="2"
                                                style="border: 1px solid black !important;">Akurasi</th>
                                            <th class="border border-black px-1 py-1 font-bold" rowspan="2"
                                                style="border: 1px solid black !important;">Tidak sesuai RR</th>
                                            <th class="border border-black px-1 py-1 font-bold" rowspan="2"
                                                style="border: 1px solid black !important;">Informasi Tambahan</th>
                                        </tr>
                                        <tr style="height: 30px;">
                                            <th class="border border-black px-1 py-1 font-bold"
                                                style="border: 1px solid black !important;">Tanggal</th>
                                            <th class="border border-black px-1 py-1 font-bold"
                                                style="border: 1px solid black !important;">Bulan</th>
                                            <th class="border border-black px-1 py-1 font-bold"
                                                style="border: 1px solid black !important;">Mulai</th>
                                            <th class="border border-black px-1 py-1 font-bold"
                                                style="border: 1px solid black !important;">Akhir</th>
                                            <th class="border border-black px-1 py-1 font-bold"
                                                style="border: 1px solid black !important;">Long (0-180)</th>
                                            <th class="border border-black px-1 py-1 font-bold"
                                                style="border: 1px solid black !important;">E atau W</th>
                                            <th class="border border-black px-1 py-1 font-bold"
                                                style="border: 1px solid black !important;">Long (0-59)</th>
                                            <th class="border border-black px-1 py-1 font-bold"
                                                style="border: 1px solid black !important;">Lat (0-90)</th>
                                            <th class="border border-black px-1 py-1 font-bold"
                                                style="border: 1px solid black !important;">N atau S</th>
                                            <th class="border border-black px-1 py-1 font-bold"
                                                style="border: 1px solid black !important;">Lat (0-59)</th>
                                        </tr>
                                        <tr style="background-color: #f2e1d1; height: 24px;" class="text-slate-800 font-normal">
                                            <th class="border border-black px-1 py-1" style="border: 1px solid black !important;">1
                                            </th>
                                            <th class="border border-black px-1 py-1" style="border: 1px solid black !important;">2
                                            </th>
                                            <th class="border border-black px-1 py-1" style="border: 1px solid black !important;">3
                                            </th>
                                            <th class="border border-black px-1 py-1" style="border: 1px solid black !important;">4
                                            </th>
                                            <th class="border border-black px-1 py-1" style="border: 1px solid black !important;">5
                                            </th>
                                            <th class="border border-black px-1 py-1" style="border: 1px solid black !important;">6
                                            </th>
                                            <th class="border border-black px-1 py-1" style="border: 1px solid black !important;">7
                                            </th>
                                            <th class="border border-black px-1 py-1" style="border: 1px solid black !important;">8
                                            </th>
                                            <th class="border border-black px-1 py-1" style="border: 1px solid black !important;">9
                                            </th>
                                            <th class="border border-black px-1 py-1" style="border: 1px solid black !important;">10
                                            </th>
                                            <th class="border border-black px-1 py-1" style="border: 1px solid black !important;">11
                                            </th>
                                            <th class="border border-black px-1 py-1" style="border: 1px solid black !important;">12
                                            </th>
                                            <th class="border border-black px-1 py-1" style="border: 1px solid black !important;">13
                                            </th>
                                            <th class="border border-black px-1 py-1" style="border: 1px solid black !important;">14
                                            </th>
                                            <th class="border border-black px-1 py-1" style="border: 1px solid black !important;">15
                                            </th>
                                            <th class="border border-black px-1 py-1" style="border: 1px solid black !important;">16
                                            </th>
                                            <th class="border border-black px-1 py-1" style="border: 1px solid black !important;">17
                                            </th>
                                            <th class="border border-black px-1 py-1" style="border: 1px solid black !important;">18
                                            </th>
                                            <th class="border border-black px-1 py-1" style="border: 1px solid black !important;">19
                                            </th>
                                            <th class="border border-black px-1 py-1" style="border: 1px solid black !important;">20
                                            </th>
                                            <th class="border border-black px-1 py-1" style="border: 1px solid black !important;">21
                                            </th>
                                            <th class="border border-black px-1 py-1" style="border: 1px solid black !important;">22
                                            </th>
                                            <th class="border border-black px-1 py-1" style="border: 1px solid black !important;">23
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="bg-white" style="height: 34px;">
                                            <td class="border border-black px-1 py-1" style="border: 1px solid black !important;">
                                                {{ $item->kode_negara ?: '-' }}</td>
                                            <td class="border border-black px-1 py-1 text-start"
                                                style="border: 1px solid black !important;">{{ $item->stasiun_monitor ?: '-' }}</td>
                                            <td class="border border-black px-1 py-1" style="border: 1px solid black !important;">
                                                {{ $item->frekuensi_khz ?: '-' }}</td>
                                            <td class="border border-black px-1 py-1" style="border: 1px solid black !important;">
                                                {{ $item->tanggal ?: '-' }}</td>
                                            <td class="border border-black px-1 py-1" style="border: 1px solid black !important;">
                                                {{ $item->bulan ?: '-' }}</td>
                                            <td class="border border-black px-1 py-1" style="border: 1px solid black !important;">
                                                {{ $item->jam_mulai ?: '-' }}</td>
                                            <td class="border border-black px-1 py-1" style="border: 1px solid black !important;">
                                                {{ $item->jam_akhir ?: '-' }}</td>
                                            <td class="border border-black px-1 py-1" style="border: 1px solid black !important;">
                                                {{ $item->kuat_medan_dbuvm ?: '-' }}</td>
                                            <td class="border border-black px-1 py-1 text-start"
                                                style="border: 1px solid black !important;">{{ $item->identifikasi ?: '-' }}</td>
                                            <td class="border border-black px-1 py-1" style="border: 1px solid black !important;">
                                                {{ $item->administrasi_termonitor ?: '-' }}</td>
                                            <td class="border border-black px-1 py-1" style="border: 1px solid black !important;">
                                                {{ $item->kelas_stasiun ?: '-' }}</td>
                                            <td class="border border-black px-1 py-1" style="border: 1px solid black !important;">
                                                {{ $item->lebar_band ?: '-' }}</td>
                                            <td class="border border-black px-1 py-1" style="border: 1px solid black !important;">
                                                {{ $item->kelas_emisi ?: '-' }}</td>
                                            <td class="border border-black px-1 py-1" style="border: 1px solid black !important;">
                                                {{ $item->longitude_derajat ?: '-' }}</td>
                                            <td class="border border-black px-1 py-1" style="border: 1px solid black !important;">
                                                {{ $item->longitude_arah ?: '-' }}</td>
                                            <td class="border border-black px-1 py-1" style="border: 1px solid black !important;">
                                                {{ $item->longitude_menit ?: '-' }}</td>
                                            <td class="border border-black px-1 py-1" style="border: 1px solid black !important;">
                                                {{ $item->latitude_derajat ?: '-' }}</td>
                                            <td class="border border-black px-1 py-1" style="border: 1px solid black !important;">
                                                {{ $item->latitude_arah ?: '-' }}</td>
                                            <td class="border border-black px-1 py-1" style="border: 1px solid black !important;">
                                                {{ $item->latitude_menit ?: '-' }}</td>
                                            <td class="border border-black px-1 py-1" style="border: 1px solid black !important;">
                                                {{ $item->north_bearing ?: '-' }}</td>
                                            <td class="border border-black px-1 py-1" style="border: 1px solid black !important;">
                                                {{ $item->akurasi ?: '-' }}</td>
                                            <td class="border border-black px-1 py-1" style="border: 1px solid black !important;">
                                                {{ $item->tidak_sesuai_rr ?: '-' }}</td>
                                            <td class="border border-black px-1 py-1" style="border: 1px solid black !important;">
                                                {{ $item->informasi_tambahan ?: '-' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    @php
        $monthNames = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
    @endphp

    <div class="modal fade" id="addLaporanModal" tabindex="-1" aria-labelledby="addLaporanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addLaporanModalLabel">Add New Laporan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('monitoring.store') }}">
                    @csrf
                    <input type="hidden" name="form_source" value="add_laporan">
                    <div class="modal-body flex-grow-1 overflow-y-auto pe-2" style="max-height: calc(100vh - 10.5rem);">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Jenis Laporan <span class="text-danger">*</span></label>
                                <select name="kategori" class="form-select" required>
                                    <option value="" disabled hidden></option>
                                    <option value="HF Nelayan">HF Nelayan</option>
                                    <option value="HF Rutin">HF Rutin</option>
                                    <option value="MF">HF Medium Frequency</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Negara <span class="text-danger">*</span></label>
                                <select name="kode_negara" class="form-select" required>
                                    <option value="INS" selected>INDONESIA (INS)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Stasiun Monitor <span class="text-danger">*</span></label>
                                <select name="stasiun_monitor" class="form-select" required>
                                    <option value="" disabled hidden></option>
                                    @foreach (($dropdownOptions['stasiun_monitor'] ?? ['MSHF LAMPUNG']) as $stasiunMonitor)
                                        <option value="{{ $stasiunMonitor }}">{{ $stasiunMonitor }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Frekuensi (kHz) <span class="text-danger">*</span></label>
                                <input type="number" step="0.001" name="frekuensi_khz" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Mulai Pengamatan <span class="text-danger">*</span></label>
                                <input id="mulaiPengamatanInput" type="datetime-local" name="mulai_pengamatan"
                                    class="form-control" value="{{ old('mulai_pengamatan') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Selesai Pengamatan <span class="text-danger">*</span></label>
                                @php
                                    $oldSelesaiWaktu = old('selesai_pengamatan_waktu');
                                    if ($oldSelesaiWaktu === null) {
                                        $oldSelesaiPengamatan = old('selesai_pengamatan', '');
                                        $oldSelesaiWaktu = str_contains($oldSelesaiPengamatan, 'T')
                                            ? substr($oldSelesaiPengamatan, 11, 5)
                                            : '';
                                    }
                                @endphp
                                <input id="selesaiPengamatanWaktuInput" type="time" name="selesai_pengamatan_waktu"
                                    class="form-control" value="{{ $oldSelesaiWaktu }}" step="60" required>
                                <input id="selesaiPengamatanHidden" type="hidden" name="selesai_pengamatan"
                                    value="{{ old('selesai_pengamatan') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Kuat Medan</label>
                                <input type="number" step="0.01" name="kuat_medan_dbuvm" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Identifikasi <span class="text-danger">*</span></label>
                                <input type="text" name="identifikasi" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Administrasi Termonitor</label>
                                <select name="administrasi_termonitor" class="form-select">
                                    <option value="" disabled hidden></option>
                                    @foreach (($dropdownOptions['administrasi_termonitor'] ?? ['INS']) as $administrasiTermonitor)
                                        <option value="{{ $administrasiTermonitor }}">{{ $administrasiTermonitor }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Kelas Stasiun <span class="text-danger">*</span></label>
                                @php
                                    $selectedKelasStasiun = old('kelas_stasiun');
                                @endphp
                                <select name="kelas_stasiun" class="form-select max-h-40" required>
                                    <option value="" disabled hidden></option>
                                    @foreach (($dropdownOptions['kelas_stasiun'] ?? ['AL', 'AM', 'AT', 'BC', 'BT', 'FA', 'FB', 'FC', 'FD', 'FG', 'FL', 'FP', 'FX', 'LR', 'MA', 'ML', 'MO', 'MR', 'MS', 'NL', 'NR', 'OD', 'OE', 'PL', 'RM', 'RN', 'SA', 'SM', 'SS', 'TC', 'UV', 'UW']) as $kelasStasiun)
                                        <option value="{{ $kelasStasiun }}" {{ $selectedKelasStasiun === $kelasStasiun ? 'selected' : '' }}>{{ $kelasStasiun }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Lebar Band <span class="text-danger">*</span></label>
                                <input type="text" name="lebar_band" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Kelas Emisi <span class="text-danger">*</span></label>
                                <input type="text" name="kelas_emisi" class="form-control" required>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Long (0-180)</label>
                                <input type="text" name="longitude_derajat" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">E/W</label>
                                <input type="text" name="longitude_arah" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Long (0-59)</label>
                                <input type="text" name="longitude_menit" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Lat (0-90)</label>
                                <input type="text" name="latitude_derajat" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">N/S</label>
                                <input type="text" name="latitude_arah" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Lat (0-59)</label>
                                <input type="text" name="latitude_menit" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">North Bearing</label>
                                <input type="text" name="north_bearing" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Akurasi</label>
                                <input type="text" name="akurasi" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tidak Sesuai RR</label>
                                <input type="text" name="tidak_sesuai_rr" class="form-control">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Informasi Tambahan</label>
                                <textarea name="informasi_tambahan" rows="5" class="form-control"
                                    placeholder="Isi informasi tambahan di sini...">{{ old('informasi_tambahan') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary transition hover:opacity-90">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editLaporanModal" tabindex="-1" aria-labelledby="editLaporanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: min(95vw, 1200px);">
            <div class="modal-content d-flex flex-column" style="max-height: calc(100vh - 1.5rem);">
                <div class="modal-header">
                    <h5 class="modal-title" id="editLaporanModalLabel">
                        {{ isset($editTableNumber) && $editTableNumber ? 'Edit Table ' . $editTableNumber : 'Edit Table' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                @php
                    $editMonitoringData = $editMonitoring ?? null;
                    $editJamMulai = old('mulai_pengamatan');
                    if (!$editJamMulai) {
                        $editJamMulai = isset($editMonitoringData) && $editMonitoringData->tahun && $editMonitoringData->bulan && $editMonitoringData->tanggal
                            ? sprintf(
                                '%04d-%02d-%02dT%s',
                                (int) $editMonitoringData->tahun,
                                (int) $editMonitoringData->bulan,
                                (int) $editMonitoringData->tanggal,
                                str_replace('.', ':', (string) $editMonitoringData->jam_mulai)
                            )
                            : '';
                    }

                    $editJamAkhir = old('selesai_pengamatan_waktu');
                    if (!$editJamAkhir) {
                        $editJamAkhir = isset($editMonitoringData) ? str_replace('.', ':', (string) $editMonitoringData->jam_akhir) : '';
                    }

                    $editSelectedKategori = old('kategori', $editMonitoringData->kategori ?? '');
                    $editSelectedAdministrasi = old('administrasi_termonitor', $editMonitoringData->administrasi_termonitor ?? '');
                    $editSelectedKelasStasiun = old('kelas_stasiun', $editMonitoringData->kelas_stasiun ?? '');
                @endphp

                <form method="POST" action="{{ route('monitoring.update', old('edit_id', $editMonitoringData->id ?? 0)) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="form_source" value="edit_laporan">
                    <input type="hidden" name="edit_id" id="editMonitoringId"
                        value="{{ old('edit_id', $editMonitoringData->id ?? '') }}">
                    <input type="hidden" name="edit_table_no" id="editTableNoInput"
                        value="{{ old('edit_table_no', $editTableNumber ?? '') }}">

                    <div class="modal-body flex-grow-1 overflow-y-auto pe-2" style="max-height: calc(100vh - 10.5rem);">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Jenis Laporan <span class="text-danger">*</span></label>
                                <select id="editKategoriInput" name="kategori" class="form-select" required>
                                    <option value="" disabled hidden {{ $editSelectedKategori === '' ? 'selected' : '' }}>
                                    </option>
                                    <option value="HF Nelayan" {{ $editSelectedKategori === 'HF Nelayan' ? 'selected' : '' }}>
                                        HF Nelayan</option>
                                    <option value="HF Rutin" {{ $editSelectedKategori === 'HF Rutin' ? 'selected' : '' }}>HF
                                        Rutin</option>
                                    <option value="MF" {{ $editSelectedKategori === 'MF' ? 'selected' : '' }}>HF Medium
                                        Frequency</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Negara <span class="text-danger">*</span></label>
                                <select id="editKodeNegaraInput" name="kode_negara" class="form-select" required>
                                    <option value="INS" {{ old('kode_negara', $editMonitoringData->kode_negara ?? 'INS') === 'INS' ? 'selected' : '' }}>INDONESIA (INS)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Stasiun Monitor <span class="text-danger">*</span></label>
                                <select id="editStasiunMonitorInput" name="stasiun_monitor" class="form-select" required>
                                    <option value="" disabled hidden {{ old('stasiun_monitor', $editMonitoringData->stasiun_monitor ?? '') === '' ? 'selected' : '' }}></option>
                                    @foreach (($dropdownOptions['stasiun_monitor'] ?? ['MSHF LAMPUNG']) as $stasiunMonitor)
                                        <option value="{{ $stasiunMonitor }}" {{ old('stasiun_monitor', $editMonitoringData->stasiun_monitor ?? '') === $stasiunMonitor ? 'selected' : '' }}>
                                            {{ $stasiunMonitor }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Frekuensi (kHz) <span class="text-danger">*</span></label>
                                <input id="editFrekuensiInput" type="number" step="0.001" name="frekuensi_khz"
                                    class="form-control"
                                    value="{{ old('frekuensi_khz', $editMonitoringData->frekuensi_khz ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mulai Pengamatan <span class="text-danger">*</span></label>
                                <input id="editMulaiPengamatanInput" type="datetime-local" name="mulai_pengamatan"
                                    class="form-control" value="{{ old('mulai_pengamatan', $editJamMulai) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Selesai Pengamatan <span class="text-danger">*</span></label>
                                <input id="editSelesaiPengamatanWaktuInput" type="time" name="selesai_pengamatan_waktu"
                                    class="form-control" value="{{ old('selesai_pengamatan_waktu', $editJamAkhir) }}"
                                    step="60" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Kuat Medan</label>
                                <input id="editKuatMedanInput" type="number" step="0.01" name="kuat_medan_dbuvm"
                                    class="form-control"
                                    value="{{ old('kuat_medan_dbuvm', $editMonitoringData->kuat_medan_dbuvm ?? '') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Identifikasi <span class="text-danger">*</span></label>
                                <input id="editIdentifikasiInput" type="text" name="identifikasi" class="form-control"
                                    value="{{ old('identifikasi', $editMonitoringData->identifikasi ?? '') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Administrasi Termonitor</label>
                                <select id="editAdministrasiInput" name="administrasi_termonitor" class="form-select">
                                    <option value="" disabled hidden {{ $editSelectedAdministrasi === '' ? 'selected' : '' }}>
                                    </option>
                                    @foreach (($dropdownOptions['administrasi_termonitor'] ?? ['INS']) as $administrasiTermonitor)
                                        <option value="{{ $administrasiTermonitor }}" {{ $editSelectedAdministrasi === $administrasiTermonitor ? 'selected' : '' }}>
                                            {{ $administrasiTermonitor }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Kelas Stasiun <span class="text-danger">*</span></label>
                                <select id="editKelasStasiunInput" name="kelas_stasiun" class="form-select max-h-40"
                                    required>
                                    <option value="" disabled hidden {{ $editSelectedKelasStasiun === '' ? 'selected' : '' }}>
                                    </option>
                                    @foreach (($dropdownOptions['kelas_stasiun'] ?? ['AL', 'AM', 'AT', 'BC', 'BT', 'FA', 'FB', 'FC', 'FD', 'FG', 'FL', 'FP', 'FX', 'LR', 'MA', 'ML', 'MO', 'MR', 'MS', 'NL', 'NR', 'OD', 'OE', 'PL', 'RM', 'RN', 'SA', 'SM', 'SS', 'TC', 'UV', 'UW']) as $kelasStasiun)
                                        <option value="{{ $kelasStasiun }}" {{ $editSelectedKelasStasiun === $kelasStasiun ? 'selected' : '' }}>{{ $kelasStasiun }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Lebar Band <span class="text-danger">*</span></label>
                                <input id="editLebarBandInput" type="text" name="lebar_band" class="form-control"
                                    value="{{ old('lebar_band', $editMonitoringData->lebar_band ?? '') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Kelas Emisi <span class="text-danger">*</span></label>
                                <input id="editKelasEmisiInput" type="text" name="kelas_emisi" class="form-control"
                                    value="{{ old('kelas_emisi', $editMonitoringData->kelas_emisi ?? '') }}" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Long (0-180)</label>
                                <input id="editLongitudeDerajatInput" type="text" name="longitude_derajat"
                                    class="form-control"
                                    value="{{ old('longitude_derajat', $editMonitoringData->longitude_derajat ?? '') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">E/W</label>
                                <input id="editLongitudeArahInput" type="text" name="longitude_arah" class="form-control"
                                    value="{{ old('longitude_arah', $editMonitoringData->longitude_arah ?? '') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Long (0-59)</label>
                                <input id="editLongitudeMenitInput" type="text" name="longitude_menit" class="form-control"
                                    value="{{ old('longitude_menit', $editMonitoringData->longitude_menit ?? '') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Lat (0-90)</label>
                                <input id="editLatitudeDerajatInput" type="text" name="latitude_derajat"
                                    class="form-control"
                                    value="{{ old('latitude_derajat', $editMonitoringData->latitude_derajat ?? '') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">N/S</label>
                                <input id="editLatitudeArahInput" type="text" name="latitude_arah" class="form-control"
                                    value="{{ old('latitude_arah', $editMonitoringData->latitude_arah ?? '') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Lat (0-59)</label>
                                <input id="editLatitudeMenitInput" type="text" name="latitude_menit" class="form-control"
                                    value="{{ old('latitude_menit', $editMonitoringData->latitude_menit ?? '') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">North Bearing</label>
                                <input id="editNorthBearingInput" type="text" name="north_bearing" class="form-control"
                                    value="{{ old('north_bearing', $editMonitoringData->north_bearing ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Akurasi</label>
                                <input id="editAkurasiInput" type="text" name="akurasi" class="form-control"
                                    value="{{ old('akurasi', $editMonitoringData->akurasi ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tidak Sesuai RR</label>
                                <input id="editTidakSesuaiRRInput" type="text" name="tidak_sesuai_rr" class="form-control"
                                    value="{{ old('tidak_sesuai_rr', $editMonitoringData->tidak_sesuai_rr ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Perkiraan Lokasi Sumber Pancaran</label>
                                <input id="editPerkiraanLokasiInput" type="text" name="perkiraan_lokasi_sumber_pancaran"
                                    class="form-control"
                                    value="{{ old('perkiraan_lokasi_sumber_pancaran', $editMonitoringData->perkiraan_lokasi_sumber_pancaran ?? '') }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Informasi Tambahan</label>
                                <textarea id="editInformasiTambahanInput" name="informasi_tambahan" rows="4"
                                    class="form-control"
                                    placeholder="Isi informasi tambahan di sini...">{{ old('informasi_tambahan', $editMonitoringData->informasi_tambahan ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary transition hover:opacity-90">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('laporanFilterForm');
            if (!form) {
                return;
            }

            const searchInHidden = document.getElementById('searchInHidden');
            const searchInToggle = document.getElementById('searchInToggle');
            const searchScopeOptions = document.querySelectorAll('.search-scope-option');
            const searchInput = document.getElementById('filterSearchInput');
            const autoSubmitFields = form.querySelectorAll('select[name="kategori"], input[name="tanggal"], input[name="bulan"], input[name="tahun"]');

            autoSubmitFields.forEach(function (field) {
                field.addEventListener('change', function () {
                    form.requestSubmit();
                });
            });

            searchScopeOptions.forEach(function (option) {
                option.addEventListener('click', function () {
                    const value = option.getAttribute('data-value') || 'identifikasi';
                    const label = option.getAttribute('data-label') || 'Identifikasi';

                    if (searchInHidden) {
                        searchInHidden.value = value;
                    }
                    if (searchInToggle) {
                        searchInToggle.textContent = label;
                    }

                    // Apply the selected search scope immediately.
                    form.requestSubmit();
                });
            });

            if (searchInput) {
                searchInput.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        form.requestSubmit();
                    }
                });
            }

            const hasErrors = {{ $errors->any() ? 'true' : 'false' }};
            const formSource = @json(old('form_source'));

            if (hasErrors && formSource === 'add_laporan') {
                const addModalEl = document.getElementById('addLaporanModal');
                if (addModalEl && window.bootstrap && bootstrap.Modal) {
                    bootstrap.Modal.getOrCreateInstance(addModalEl).show();
                }
            }

            if (hasErrors && formSource === 'edit_laporan') {
                const editModalEl = document.getElementById('editLaporanModal');
                if (editModalEl && window.bootstrap && bootstrap.Modal) {
                    bootstrap.Modal.getOrCreateInstance(editModalEl).show();
                }
            }

            const editModalEl = document.getElementById('editLaporanModal');
            const editModalTitle = document.getElementById('editLaporanModalLabel');
            const editForm = editModalEl ? editModalEl.querySelector('form') : null;

            const editFieldMap = {
                editMonitoringId: 'id',
                editKategoriInput: 'kategori',
                editKodeNegaraInput: 'kode_negara',
                editStasiunMonitorInput: 'stasiun_monitor',
                editFrekuensiInput: 'frekuensi_khz',
                editMulaiPengamatanInput: 'mulai_pengamatan',
                editSelesaiPengamatanWaktuInput: 'selesai_pengamatan_waktu',
                editKuatMedanInput: 'kuat_medan_dbuvm',
                editIdentifikasiInput: 'identifikasi',
                editAdministrasiInput: 'administrasi_termonitor',
                editKelasStasiunInput: 'kelas_stasiun',
                editLebarBandInput: 'lebar_band',
                editKelasEmisiInput: 'kelas_emisi',
                editLongitudeDerajatInput: 'longitude_derajat',
                editLongitudeArahInput: 'longitude_arah',
                editLongitudeMenitInput: 'longitude_menit',
                editLatitudeDerajatInput: 'latitude_derajat',
                editLatitudeArahInput: 'latitude_arah',
                editLatitudeMenitInput: 'latitude_menit',
                editNorthBearingInput: 'north_bearing',
                editAkurasiInput: 'akurasi',
                editTidakSesuaiRRInput: 'tidak_sesuai_rr',
                editPerkiraanLokasiInput: 'perkiraan_lokasi_sumber_pancaran',
                editInformasiTambahanInput: 'informasi_tambahan',
                editTableNoInput: 'edit_table_no',
            };

            const setFieldValue = function (fieldId, value) {
                const field = document.getElementById(fieldId);
                if (!field) {
                    return;
                }

                field.value = value ?? '';
            };

            const syncEditModalFromData = function (monitoring, tableNumber, updateUrl) {
                if (!monitoring) {
                    return;
                }

                if (editForm && updateUrl) {
                    editForm.setAttribute('action', updateUrl);
                }

                Object.keys(editFieldMap).forEach(function (fieldId) {
                    const monitoringKey = editFieldMap[fieldId];

                    if (fieldId === 'editTableNoInput') {
                        setFieldValue(fieldId, tableNumber || '');
                        return;
                    }

                    if (fieldId === 'editMonitoringId') {
                        setFieldValue(fieldId, monitoring.id || '');
                        return;
                    }

                    setFieldValue(fieldId, monitoring[monitoringKey] || '');
                });

                if (editModalTitle) {
                    editModalTitle.textContent = tableNumber ? `Edit Table ${tableNumber}` : 'Edit Table';
                }
            };

            if (editModalEl) {
                editModalEl.addEventListener('show.bs.modal', function (event) {
                    const trigger = event.relatedTarget;
                    if (!trigger) {
                        return;
                    }

                    const monitoringRaw = trigger.getAttribute('data-monitoring');
                    const tableNumber = trigger.getAttribute('data-edit-table-number') || '';
                    const updateUrl = trigger.getAttribute('data-update-url') || '';

                    if (!monitoringRaw) {
                        return;
                    }

                    try {
                        const monitoring = JSON.parse(monitoringRaw);
                        syncEditModalFromData(monitoring, tableNumber, updateUrl);
                    } catch (error) {
                        console.error('Gagal memuat data edit modal:', error);
                    }
                });
            }

            const editModalNeedsOpen = hasErrors && formSource === 'edit_laporan';
            const editMonitoringLoaded = @json(isset($editMonitoring) && $editMonitoring);

            if (editModalEl && window.bootstrap && bootstrap.Modal && (editModalNeedsOpen || editMonitoringLoaded)) {
                bootstrap.Modal.getOrCreateInstance(editModalEl).show();
            }

            const addForm = document.querySelector('#addLaporanModal form');
            const mulaiPengamatanInput = document.getElementById('mulaiPengamatanInput');
            const selesaiPengamatanWaktuInput = document.getElementById('selesaiPengamatanWaktuInput');
            const selesaiPengamatanHidden = document.getElementById('selesaiPengamatanHidden');

            const toMinutes = function (timeValue) {
                if (!timeValue || !timeValue.includes(':')) {
                    return null;
                }

                const [hourStr, minuteStr] = timeValue.split(':');
                const hour = Number(hourStr);
                const minute = Number(minuteStr);

                if (!Number.isFinite(hour) || !Number.isFinite(minute)) {
                    return null;
                }

                return (hour * 60) + minute;
            };

            const syncSelesaiPengamatan = function () {
                if (!selesaiPengamatanHidden) {
                    return;
                }

                const mulaiValue = mulaiPengamatanInput ? (mulaiPengamatanInput.value || '') : '';
                const selesaiWaktuValue = selesaiPengamatanWaktuInput ? (selesaiPengamatanWaktuInput.value || '') : '';

                if (selesaiPengamatanWaktuInput) {
                    selesaiPengamatanWaktuInput.setCustomValidity('');
                    selesaiPengamatanWaktuInput.removeAttribute('min');

                    if (mulaiValue && mulaiValue.includes('T')) {
                        const mulaiJam = mulaiValue.split('T')[1].slice(0, 5);
                        selesaiPengamatanWaktuInput.setAttribute('min', mulaiJam);

                        const mulaiMenit = toMinutes(mulaiJam);
                        const selesaiMenit = toMinutes(selesaiWaktuValue);

                        if (mulaiMenit !== null && selesaiMenit !== null && selesaiMenit < mulaiMenit) {
                            selesaiPengamatanWaktuInput.setCustomValidity('Selesai Pengamatan harus sama atau lebih besar dari Mulai Pengamatan.');
                        }
                    }
                }

                if (!mulaiValue || !selesaiWaktuValue || !mulaiValue.includes('T')) {
                    selesaiPengamatanHidden.value = '';
                    return;
                }

                const tanggalMulai = mulaiValue.split('T')[0];
                selesaiPengamatanHidden.value = `${tanggalMulai}T${selesaiWaktuValue}`;
            };

            if (mulaiPengamatanInput) {
                mulaiPengamatanInput.addEventListener('change', syncSelesaiPengamatan);
                mulaiPengamatanInput.addEventListener('input', syncSelesaiPengamatan);
            }

            if (selesaiPengamatanWaktuInput) {
                selesaiPengamatanWaktuInput.addEventListener('change', syncSelesaiPengamatan);
                selesaiPengamatanWaktuInput.addEventListener('input', syncSelesaiPengamatan);
            }

            if (addForm) {
                addForm.addEventListener('submit', syncSelesaiPengamatan);
            }

            syncSelesaiPengamatan();

            // --- PENYAMBUNGAN KABEL DATA EDIT (Vanilla JS Version) ---
            const editLaporanModal = document.getElementById('editLaporanModal');
            if (editLaporanModal) {
                editLaporanModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const rawData = button.getAttribute('data-monitoring');
                    if (!rawData) return;

                    const data = JSON.parse(rawData);
                    const updateUrl = button.getAttribute('data-update-url');
                    const index = button.getAttribute('data-edit-table-number') || button.getAttribute('data-index') || '1';

                    // 3. Debugging & Force Update
                    console.log("Script Edit Aktif - Index: " + index);

                    // 1. Judul Modal Berdasarkan Nomor Urut
                    const title = editLaporanModal.querySelector('.modal-title');
                    if (title) {
                        title.textContent = 'Edit Table ' + index;
                    }

                    const form = editLaporanModal.querySelector('form');
                    if (form && updateUrl) {
                        form.action = updateUrl;
                    }

                    // Hidden Inputs
                    const setVal = function (id, val) {
                        const el = document.getElementById(id);
                        if (el) el.value = val;
                    };

                    setVal('editMonitoringId', data.id || '');
                    setVal('editTableNoInput', index);

                    // 2. Data Pre-fill (Audit ID Selector & Select Input)
                    setVal('editKategoriInput', data.kategori || '');
                    setVal('editKodeNegaraInput', data.kode_negara || 'INS');
                    setVal('editStasiunMonitorInput', data.stasiun_monitor || '');
                    setVal('editFrekuensiInput', data.frekuensi_khz || '');

                    setVal('editMulaiPengamatanInput', data.mulai_pengamatan || '');
                    setVal('editSelesaiPengamatanWaktuInput', data.selesai_pengamatan_waktu || '');
                    setVal('editKuatMedanInput', data.kuat_medan_dbuvm || '');
                    setVal('editIdentifikasiInput', data.identifikasi || '');
                    setVal('editAdministrasiInput', data.administrasi_termonitor || '');
                    setVal('editKelasStasiunInput', data.kelas_stasiun || '');

                    setVal('editLebarBandInput', data.lebar_band || '');
                    setVal('editKelasEmisiInput', data.kelas_emisi || '');

                    // Loop the rest safely if their IDs conform to form names
                    Object.keys(data).forEach(function (key) {
                        if (form) {
                            const input = form.querySelector('[name="' + key + '"]');
                            if (input && input.type !== 'checkbox' && input.type !== 'radio') {
                                if (!input.value || input.value === '') {
                                    input.value = data[key] !== null ? data[key] : '';
                                }
                            }
                        }
                    });
                });

                editLaporanModal.addEventListener('hidden.bs.modal', function () {
                    const form = editLaporanModal.querySelector('form');
                    if (form) form.reset();
                    const title = editLaporanModal.querySelector('.modal-title');
                    if (title) title.textContent = 'Edit Table';
                });
            }

        });
    </script>
@endsection