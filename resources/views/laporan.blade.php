@extends('layouts.app')
{{-- FORCE_RECOMPILE_V2 --}}

@section('title', $pageTitle ?? 'Daftar Laporan Harian')
@section('page_title', $pageTitle ?? 'Daftar Laporan')

@section('content')
<div wire:key="laporan-daftar-{{ request()->query('page', 1) }}">
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

        .btn-minimal {
            background-color: #fcfcfc;
            color: #64748b;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease-in-out;
            font-size: 0.72rem;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            border-radius: 0.5rem;
            padding: 0 2.5rem; /* Increased padding to accommodate absolute icon */
            text-decoration: none;
            font-weight: 600;
            position: relative;
            min-width: 140px;
        }

        .btn-minimal i, .btn-minimal svg {
            position: absolute;
            left: 1rem;
            opacity: 0.8;
            color: #94a3b8;
            width: 14px;
            height: 14px;
        }

        .btn-minimal:hover {
            background-color: #f8fafc;
            border-color: #cbd5e1;
            color: #334155;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.04);
        }

        .btn-minimal:active {
            transform: translateY(0);
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
            overflow-y: visible;
            max-height: none;
            border: none;
            /* Force GPU to keep the entire table in memory */
            transform: translate3d(0, 0, 0);
            will-change: transform, scroll-position;
            backface-visibility: hidden;
            perspective: 1000;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f8fafc;
            position: relative;
            background: #ffffff;
        }

        /* Mobile: Visible scrollbar so user knows to swipe */
        @media (max-width: 991.98px) {
            .mosfet-table-wrap::-webkit-scrollbar {
                height: 6px;
            }
            .mosfet-table-wrap::-webkit-scrollbar-track {
                background: #f1f5f9;
            }
            .mosfet-table-wrap::-webkit-scrollbar-thumb {
                background: #94a3b8;
                border-radius: 3px;
            }
        }

        .mosfet-table {
            min-width: 2450px;
            font-size: 0.68rem;
            margin-bottom: 0;
            border-collapse: separate !important;
            border-spacing: 0 !important;
            border-left: 1px solid rgba(15, 23, 42, 0.15) !important;
            border-top: 1px solid rgba(15, 23, 42, 0.15) !important;
            /* table-layout: fixed mencegah browser recalc lebar kolom setiap frame */
            table-layout: fixed;
        }

        .mosfet-table thead th,
        .mosfet-table thead td,
        .mosfet-table tbody td {
            border: none !important;
            border-right: 1px solid rgba(15, 23, 42, 0.15) !important;
            border-bottom: 1px solid rgba(15, 23, 42, 0.15) !important;
            padding: 0.3rem 0.4rem;
            box-sizing: border-box !important;
            /* NOTE: box-shadow NOT suppressed here — sticky first-child uses it for separator */
        }

        /* Standard box-shadow suppression for all table cells */
        .mosfet-table thead th,
        .mosfet-table thead td,
        .mosfet-table tbody td {
            box-shadow: none !important;
        }

        .mosfet-table thead th {
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
            background: #ffffff;
            font-weight: 700;
            line-height: 1;
            background-clip: padding-box !important;
            /* Vertical sticky (top) handled per row-group below */
            position: sticky;
            z-index: 5;
        }

        .mosfet-table thead tr.header-group th {
            top: 0;
            z-index: 8;
            height: 32px;
        }

        .mosfet-table thead tr.header-field th {
            top: 32px;
            z-index: 7;
            height: 32px;
        }

        .mosfet-table thead tr.header-subfield th {
            top: 64px;
            z-index: 6;
            height: 32px;
        }

        /* Rowspan header in header-group: highest priority for corner cell (No) */
        .mosfet-table thead tr.header-group th[rowspan] {
            z-index: 9;
        }


        .mosfet-table tbody td {
            white-space: nowrap;
            vertical-align: middle;
            text-align: center;
        }

        .mosfet-table .column-label-row td {
            background: #FDE9D9 !important; /* Adjusted to match export style */
            color: #000000 !important;
            font-weight: 700;
            text-align: center !important;
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
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.5rem;
            border: 1px solid #d1d5db;
            text-decoration: none;
            background: #f8fafc;
            font-size: 0.85rem;
            line-height: 1;
            /* Tidak ada transition — mencegah repaint saat scroll melintas tombol */
        }

        .action-pill:hover {
            filter: brightness(0.96);
        }

        /*
         * SCROLL SHIELD: Saat body.is-scrolling, matikan pointer-events & transition
         * pada semua tombol aksi. Ini mencegah browser menghitung hover-state
         * dan memulai animasi filter/background untuk setiap tombol yang
         * melintas di bawah kursor saat scroll — penyebab lag di kolom Aksi.
         */
        body.is-scrolling .action-pill {
            pointer-events: none !important;
            transition: none !important;
            filter: none !important;
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
            background: #f8fafc;
            border: 1px solid rgba(15, 23, 42, 0.1);
            padding: 0.5rem 0.75rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0;
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
        }

        .detail-subtable {
            border: 1px solid rgba(15, 23, 42, 0.1) !important;
            margin-bottom: 1rem;
        }

        .detail-subtable thead th {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid rgba(15, 23, 42, 0.1) !important;
            white-space: nowrap;
            font-size: 0.7rem;
            padding: 0.4rem;
        }

        .detail-subtable tbody td {
            border: 1px solid rgba(15, 23, 42, 0.1) !important;
            padding: 0.4rem;
            color: #1e293b;
        }

        .btn-link-reset:hover {
            color: #ef4444 !important;
            /* Red-500 */
        }

        .btn-link-reset:hover i {
            color: #ef4444 !important;
        }

        /* Detail Modal Table Styling */
        .detail-table {
            width: 100%;
            border-collapse: collapse !important;
            border: 1px solid rgba(15, 23, 42, 0.15) !important;
            font-size: 10px;
            text-align: center;
            line-height: normal;
        }

        .detail-table th,
        .detail-table td {
            border: 1px solid rgba(15, 23, 42, 0.15) !important;
            padding: 4px 6px !important;
            vertical-align: middle;
        }

        .detail-table thead .header-group-row {
            background-color: #0f172a !important; /* Deep Navy/Black as requested */
            color: #ffffff !important;
            height: 32px;
        }

        .detail-table thead .header-group-row th:first-child {
            border-right: 1px solid rgba(255, 255, 255, 0.3) !important; /* Visual separator */
        }

        .detail-table thead .header-field-row {
            background-color: #f8fafc !important;
            color: #1e293b !important;
            height: 34px;
            font-weight: 700;
        }

        .detail-table thead .header-number-row {
            background-color: #FDE9D9 !important; /* Adjusted to match export style */
            color: #000000 !important;
            height: 26px;
            font-weight: 700;
        }

        .detail-table tbody tr {
            background-color: #ffffff !important;
            height: 34px;
        }

        .detail-table tbody td.text-start {
            text-align: left !important;
        }

        .modal-xl {
            max-width: 95vw !important;
        }
    </style>

    <div class="card border-slate-200 shadow-sm mb-3" style="background: #ffffff; border-radius: 1rem;">
        <div class="card-body py-2 px-3">
            <div class="d-flex align-items-center gap-2 mb-2">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="7" x2="20" y2="7"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="17" x2="20" y2="17"/></svg>
                <span class="fw-bold text-slate-800" style="font-size: 0.65rem; letter-spacing: 0.05em;">FILTER
                    PENCARIAN</span>
            </div>
            <form id="laporanFilterForm" method="GET" action="{{ route('monitoring.index') }}"
                class="row g-3 align-items-end">
                <div class="col-auto">
                    <label class="form-label text-xs fw-medium text-slate-500 text-uppercase mb-1"
                        style="font-size: 0.65rem;">Kategori</label>
                    <select name="kategori" class="form-select form-select-sm border-slate-200 rounded-3"
                        style="font-size: 0.85rem; height: 36px; background-color: #f8fafc; min-width: 140px;">
                        <option value="">Semua kategori</option>
                        <option value="HF Nelayan" {{ ($filters['kategori'] ?? '') === 'HF Nelayan' ? 'selected' : '' }}>HF Nelayan</option>
                        <option value="HF Rutin" {{ ($filters['kategori'] ?? '') === 'HF Rutin' ? 'selected' : '' }}>HF Rutin</option>
                        <option value="MF" {{ ($filters['kategori'] ?? '') === 'MF' ? 'selected' : '' }}>HF Medium Frequency</option>
                    </select>
                </div>

                @if(auth()->user()->role === 'super_admin')
                <div class="col-auto">
                    <label class="form-label text-xs fw-medium text-slate-500 text-uppercase mb-1"
                        style="font-size: 0.65rem;">Petugas Admin</label>
                        <select name="user_id" class="form-select form-select-sm border-slate-200 rounded-3"
                            style="font-size: 0.85rem; height: 36px; background-color: #f8fafc; min-width: 140px;"
                            onchange="this.form.dispatchEvent(new Event('submit'))">
                            <option value="">Semua Admin</option>
                            @foreach($users->where('role', '!=', 'super_admin') as $user)
                                <option value="{{ $user->id }}" {{ (string)($filters['user_id'] ?? '') === (string)$user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                </div>
                @endif
                
                <div class="col-auto">
                    <div class="row g-1" style="width: 220px;">
                        <div class="col-4">
                            <label class="form-label text-xs fw-medium text-slate-500 text-uppercase mb-1" style="font-size: 0.65rem;">TANGGAL</label>
                            <input type="number" name="tanggal" class="form-control form-control-sm border-slate-200 rounded-3"
                                style="font-size: 0.85rem; height: 36px; background-color: #f8fafc;" min="1" max="31" placeholder="1-31"
                                value="{{ $filters['tanggal'] ?? '' }}">
                        </div>
                        <div class="col-4">
                            <label class="form-label text-xs fw-medium text-slate-500 text-uppercase mb-1" style="font-size: 0.65rem;">Bulan</label>
                            <input type="number" name="bulan" class="form-control form-control-sm border-slate-200 rounded-3"
                                style="font-size: 0.85rem; height: 36px; background-color: #f8fafc;" min="1" max="12" placeholder="1-12"
                                value="{{ $filters['bulan'] ?? '' }}">
                        </div>
                        <div class="col-4">
                            <label class="form-label text-xs fw-medium text-slate-500 text-uppercase mb-1" style="font-size: 0.65rem;">Tahun</label>
                            <input type="number" name="tahun" class="form-control form-control-sm border-slate-200 rounded-3"
                                style="font-size: 0.85rem; height: 36px; background-color: #f8fafc;" min="2000" max="2100" placeholder="YYYY"
                                value="{{ $filters['tahun'] ?? '' }}">
                        </div>
                    </div>
                </div>

                <div class="col">
                    <label class="form-label text-xs fw-medium text-slate-500 text-uppercase mb-1"
                        style="font-size: 0.65rem;">Kata Kunci</label>
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
                            style="font-size: 0.85rem; height: 36px;" placeholder="Cari data..."
                            value="{{ $filters['q'] ?? '' }}">
                        <button class="btn btn-light border-slate-200 dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false" id="searchInToggle" style="font-size: 0.75rem; height: 36px; background-color: #f8fafc;">
                            {{ $searchInLabel }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-slate-200 rounded-3" style="font-size: 0.8rem;">
                            <li><button class="dropdown-item search-scope-option py-2" type="button" data-value="identifikasi" data-label="Identifikasi">Identifikasi</button></li>
                            <li><button class="dropdown-item search-scope-option py-2" type="button" data-value="frekuensi_khz" data-label="Frekuensi">Frekuensi</button></li>
                            <li><button class="dropdown-item search-scope-option py-2" type="button" data-value="stasiun_monitor" data-label="Stasiun">Stasiun</button></li>
                            <li><button class="dropdown-item search-scope-option py-2" type="button" data-value="administrasi_termonitor" data-label="Administrasi">Administrasi</button></li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-auto d-flex gap-1" style="margin-bottom: 6px;">
                    <button type="submit" data-no-disable="true" class="btn btn-primary d-flex align-items-center justify-content-center gap-1 shadow-sm"
                        style="font-size: 0.65rem; padding: 0.2rem 0.6rem; height: 24px; border-radius: 0.4rem; background-color: #2563eb; border: none;">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        Filter
                    </button>
                    <a href="{{ route('monitoring.index') }}" wire:navigate class="btn btn-light border d-flex align-items-center justify-content-center gap-1 shadow-sm"
                        style="font-size: 0.65rem; padding: 0.2rem 0.6rem; height: 24px; border-radius: 0.4rem;">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M2.5 2v6h6M21.5 22v-6h-6"/><path d="M22 11.5A10 10 0 0 0 3.2 7.2M2 12.5a10 10 0 0 0 18.8 4.3"/></svg>
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- TABLE: Full 28 columns, horizontal scroll isolated to table only --}}
    <div id="laporan-table-container" style="overflow: hidden; border-radius: 1rem;">
        @include('partials.laporan-table')
    </div>

    {{-- SINGLE DYNAMIC DETAIL MODAL: Only one modal for all rows to save memory --}}
    <div class="modal fade" id="detailLaporanModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header py-2 px-3 border-bottom border-slate-100">
                    <h5 class="modal-title fw-bold text-slate-800" id="detailModalTitle">Detail Laporan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="table-responsive p-3" style="max-width: 100vw; overflow-x: auto;">
                        <table class="table table-bordered table-sm align-middle mb-0" style="min-width: 2200px; font-size: 0.75rem; border-collapse: collapse !important;">
                            <thead class="align-middle" style="color: #000; font-weight: bold;">
                                <tr style="background-color: #d1d5db !important;">
                                    <th colspan="2" class="text-center py-2" style="border: 1px solid #000 !important; background-color: #d1d5db !important;">Monitoring Center</th>
                                    <th colspan="22" class="text-center py-2" style="border: 1px solid #000 !important; background-color: #d1d5db !important;">Keterangan dari stasiun yang dimonitor</th>
                                </tr>
                                <tr style="background-color: #d1d5db !important; text-transform: uppercase;">
                                    <th rowspan="2" class="text-center align-middle" style="border: 1px solid #000 !important; background-color: #d1d5db !important;">Kode Negara</th>
                                    <th rowspan="2" class="text-center align-middle" style="border: 1px solid #000 !important; background-color: #d1d5db !important;">Stasiun Monitor</th>
                                    <th rowspan="2" class="text-center align-middle" style="border: 1px solid #000 !important; background-color: #d1d5db !important;">Frekuensi (KHz)</th>
                                    <th colspan="3" class="text-center align-middle" style="border: 1px solid #000 !important; background-color: #d1d5db !important;">Waktu</th>
                                    <th colspan="2" class="text-center align-middle" style="border: 1px solid #000 !important; background-color: #d1d5db !important;">Jam</th>
                                    <th rowspan="2" class="text-center align-middle" style="border: 1px solid #000 !important; background-color: #d1d5db !important;">Kuat Medan</th>
                                    <th rowspan="2" class="text-center align-middle" style="border: 1px solid #000 !important; background-color: #d1d5db !important;">Identifikasi</th>
                                    <th rowspan="2" class="text-center align-middle" style="border: 1px solid #000 !important; background-color: #d1d5db !important;">Adm. Termonitor</th>
                                    <th rowspan="2" class="text-center align-middle" style="border: 1px solid #000 !important; background-color: #d1d5db !important;">Kelas Stasiun</th>
                                    <th rowspan="2" class="text-center align-middle" style="border: 1px solid #000 !important; background-color: #d1d5db !important;">Lebar Band</th>
                                    <th rowspan="2" class="text-center align-middle" style="border: 1px solid #000 !important; background-color: #d1d5db !important;">Kelas Emisi</th>
                                    <th colspan="6" class="text-center align-middle" style="border: 1px solid #000 !important; background-color: #d1d5db !important;">Lokasi Sumber Pancaran</th>
                                    <th rowspan="2" class="text-center align-middle" style="border: 1px solid #000 !important; background-color: #d1d5db !important;">N. Bearing</th>
                                    <th rowspan="2" class="text-center align-middle" style="border: 1px solid #000 !important; background-color: #d1d5db !important;">Akurasi</th>
                                    <th rowspan="2" class="text-center align-middle" style="border: 1px solid #000 !important; background-color: #d1d5db !important;">Tidak sesuai RR</th>
                                    <th rowspan="2" class="text-center align-middle" style="border: 1px solid #000 !important; background-color: #d1d5db !important;">Info Tambahan</th>
                                </tr>
                                <tr style="background-color: #d1d5db !important;">
                                    <th class="text-center" style="border: 1px solid #000 !important; background-color: #d1d5db !important;">Tgl</th>
                                    <th class="text-center" style="border: 1px solid #000 !important; background-color: #d1d5db !important;">Bln</th>
                                    <th class="text-center" style="border: 1px solid #000 !important; background-color: #d1d5db !important;">Thn</th>
                                    <th class="text-center" style="border: 1px solid #000 !important; background-color: #d1d5db !important;">Mulai</th>
                                    <th class="text-center" style="border: 1px solid #000 !important; background-color: #d1d5db !important;">Akhir</th>
                                    <th class="text-center" style="border: 1px solid #000 !important; background-color: #d1d5db !important;">Long (0-180)</th>
                                    <th class="text-center" style="border: 1px solid #000 !important; background-color: #d1d5db !important;">E/W</th>
                                    <th class="text-center" style="border: 1px solid #000 !important; background-color: #d1d5db !important;">Long (0-59)</th>
                                    <th class="text-center" style="border: 1px solid #000 !important; background-color: #d1d5db !important;">Lat (0-90)</th>
                                    <th class="text-center" style="border: 1px solid #000 !important; background-color: #d1d5db !important;">N/S</th>
                                    <th class="text-center" style="border: 1px solid #000 !important; background-color: #d1d5db !important;">Lat (0-59)</th>
                                </tr>
                            </thead>
                            <tbody id="detailModalContent" style="border: 1px solid #000; color: #000;">
                                <!-- Filled by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            // GLOBAL DELEGATION: Memastikan event jalan meskipun DOM berubah (AJAX/Livewire)
            document.addEventListener('show.bs.modal', function(event) {
                const modalId = event.target.id;
                const button = event.relatedTarget;
                if (!button) return;

                if (modalId === 'detailLaporanModal') {
                    const rowNum = button.getAttribute('data-row-number') || '';
                    const payloadRaw = button.getAttribute('data-view-payload');
                    
                    if (!payloadRaw) return;
                    
                    let data = null;
                    try {
                        data = JSON.parse(payloadRaw);
                    } catch (e) {
                        console.error('Failed to parse view payload', e);
                        return;
                    }
                    
                    document.getElementById('detailModalTitle').textContent = 'Detail Tabel No ' + rowNum;
                    
                    const tbody = document.getElementById('detailModalContent');
                    tbody.innerHTML = `
                        <tr style="background-color: #fff;">
                            <td class="text-center" style="border: 1px solid #000;">${data.kode_negara || '-'}</td>
                            <td class="text-start fw-bold" style="border: 1px solid #000;">${data.stasiun_monitor || '-'}</td>
                            <td class="text-center" style="border: 1px solid #000;">${data.frekuensi_khz || '-'}</td>
                            <td class="text-center" style="border: 1px solid #000;">${data.tanggal || '-'}</td>
                            <td class="text-center" style="border: 1px solid #000;">${data.bulan || '-'}</td>
                            <td class="text-center" style="border: 1px solid #000;">${data.tahun || '-'}</td>
                            <td class="text-center" style="border: 1px solid #000;">${data.jam_mulai || '-'}</td>
                            <td class="text-center" style="border: 1px solid #000;">${data.jam_akhir || '-'}</td>
                            <td class="text-center" style="border: 1px solid #000;">${data.kuat_medan_dbuvm || '-'}</td>
                            <td class="text-start" style="border: 1px solid #000;">${data.identifikasi || '-'}</td>
                            <td class="text-center" style="border: 1px solid #000;">${data.administrasi_termonitor || '-'}</td>
                            <td class="text-center" style="border: 1px solid #000;">${data.kelas_stasiun || '-'}</td>
                            <td class="text-center" style="border: 1px solid #000;">${data.lebar_band || '-'}</td>
                            <td class="text-center" style="border: 1px solid #000;">${data.kelas_emisi || '-'}</td>
                            <td class="text-center" style="border: 1px solid #000;">${data.longitude_derajat || '-'}</td>
                            <td class="text-center" style="border: 1px solid #000;">${data.longitude_arah || '-'}</td>
                            <td class="text-center" style="border: 1px solid #000;">${data.longitude_menit || '-'}</td>
                            <td class="text-center" style="border: 1px solid #000;">${data.latitude_derajat || '-'}</td>
                            <td class="text-center" style="border: 1px solid #000;">${data.latitude_arah || '-'}</td>
                            <td class="text-center" style="border: 1px solid #000;">${data.latitude_menit || '-'}</td>
                            <td class="text-center" style="border: 1px solid #000;">${data.north_bearing || '-'}</td>
                            <td class="text-center" style="border: 1px solid #000;">${data.akurasi || '-'}</td>
                            <td class="text-center" style="border: 1px solid #000;">${data.tidak_sesuai_rr || '-'}</td>
                            <td class="text-start" style="border: 1px solid #000;">${data.informasi_tambahan || '-'}</td>
                        </tr>
                    `;
                }
            });
        })();
    </script>

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
                <form id="addLaporanForm" method="POST" action="{{ route('monitoring.store') }}">
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
                                    <option value="" disabled hidden></option>
                                    @foreach (($dropdownOptions['kode_negara'] ?? ['INDONESIA (INS)']) as $negara)
                                        <option value="{{ $negara }}" {{ old('kode_negara') === $negara ? 'selected' : '' }}>{{ $negara }}</option>
                                    @endforeach
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

                            {{-- Dropdown Pilih Petugas Khusus Super Admin --}}
                            @if(auth()->user()->role === 'super_admin')
                            <div class="col-md-4">
                                <label class="form-label">Input Atas Nama (Petugas) <span class="text-danger">*</span></label>
                                <select name="user_id" class="form-select" required>
                                    <option value="" disabled selected>Pilih Admin...</option>
                                    @foreach($users->where('role', '!=', 'super_admin') as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted" style="font-size: 0.7rem;">Sebagai Super Admin, Anda bisa mengisikan data untuk admin lain.</small>
                            </div>
                            @endif

                            <div class="col-md-4">
                                <label class="form-label">Frekuensi (kHz) <span class="text-danger">*</span></label>
                                <input type="text" name="frekuensi_khz" class="form-control freq-formatter" 
                                    placeholder="Contoh: 1432.000" required>
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
                                <input type="text" name="kuat_medan_dbuvm" class="form-control field-strength-formatter" 
                                    placeholder="Contoh: 9.2">
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
                    /** @var \App\Models\Monitoring|null $editMonitoringData */
                    $editMonitoringData = $editMonitoring ?? null;
                    $editJamMulai = old('mulai_pengamatan');
                    if (!$editJamMulai) {
                        $editJamMulai = isset($editMonitoringData) && $editMonitoringData?->tahun && $editMonitoringData?->bulan && $editMonitoringData?->tanggal
                            ? sprintf(
                                '%04d-%02d-%02dT%s',
                                (int) $editMonitoringData?->tahun,
                                (int) $editMonitoringData?->bulan,
                                (int) $editMonitoringData?->tanggal,
                                str_replace('.', ':', (string) $editMonitoringData?->jam_mulai)
                            )
                            : '';
                    }

                    $editJamAkhir = old('selesai_pengamatan_waktu');
                    if (!$editJamAkhir) {
                        $editJamAkhir = isset($editMonitoringData) ? str_replace('.', ':', (string) $editMonitoringData?->jam_akhir) : '';
                    }

                    $editSelectedKategori = old('kategori', $editMonitoringData?->kategori ?? '');
                    $editSelectedAdministrasi = old('administrasi_termonitor', $editMonitoringData?->administrasi_termonitor ?? '');
                    $editSelectedKelasStasiun = old('kelas_stasiun', $editMonitoringData?->kelas_stasiun ?? '');
                @endphp

                <form id="editLaporanForm" method="POST" action="{{ route('monitoring.update', old('edit_id', $editMonitoringData?->id ?? 0)) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="form_source" value="edit_laporan">
                    <input type="hidden" name="edit_id" id="editMonitoringId"
                        value="{{ old('edit_id', $editMonitoringData?->id ?? '') }}">
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
                                    <option value="" disabled hidden></option>
                                    @foreach (($dropdownOptions['kode_negara'] ?? ['INDONESIA (INS)']) as $negara)
                                        <option value="{{ $negara }}" {{ old('kode_negara', $editMonitoringData?->kode_negara ?? '') === $negara ? 'selected' : '' }}>{{ $negara }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Stasiun Monitor <span class="text-danger">*</span></label>
                                <select id="editStasiunMonitorInput" name="stasiun_monitor" class="form-select" required>
                                    <option value="" disabled hidden {{ old('stasiun_monitor', $editMonitoringData?->stasiun_monitor ?? '') === '' ? 'selected' : '' }}></option>
                                    @foreach (($dropdownOptions['stasiun_monitor'] ?? ['MSHF LAMPUNG']) as $stasiunMonitor)
                                        <option value="{{ $stasiunMonitor }}" {{ old('stasiun_monitor', $editMonitoringData?->stasiun_monitor ?? '') === $stasiunMonitor ? 'selected' : '' }}>
                                            {{ $stasiunMonitor }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Frekuensi (kHz) <span class="text-danger">*</span></label>
                                <input id="editFrekuensiInput" type="text" name="frekuensi_khz"
                                    class="form-control freq-formatter"
                                    value="{{ old('frekuensi_khz', isset($editMonitoringData) ? number_format($editMonitoringData->frekuensi_khz, 3, '.', '') : '') }}" required>
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
                                <input id="editKuatMedanInput" type="text" name="kuat_medan_dbuvm"
                                    class="form-control field-strength-formatter"
                                    value="{{ old('kuat_medan_dbuvm', $editMonitoringData?->kuat_medan_dbuvm ?? '') }}"
                                    placeholder="Contoh: 9.2">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Identifikasi <span class="text-danger">*</span></label>
                                <input id="editIdentifikasiInput" type="text" name="identifikasi" class="form-control"
                                    value="{{ old('identifikasi', $editMonitoringData?->identifikasi ?? '') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Administrasi Termonitor</label>
                                <select id="editAdministrasiInput" name="administrasi_termonitor" class="form-select">
                                    <option value="" disabled hidden {{ $editSelectedAdministrasi === '' ? 'selected' : '' }}>
                                    </option>
                                    @foreach (($dropdownOptions['administrasi_termonitor'] ?? ['INS']) as $administrasiTermonitor)
                                        <option value="{{ $administrasiTermonitor }}" {{ $editSelectedAdministrasi === $administrasiTermonitor ? 'selected' : '' }}>
                                            {{ $administrasiTermonitor }}
                                        </option>
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
                                    value="{{ old('lebar_band', $editMonitoringData?->lebar_band ?? '') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Kelas Emisi <span class="text-danger">*</span></label>
                                <input id="editKelasEmisiInput" type="text" name="kelas_emisi" class="form-control"
                                    value="{{ old('kelas_emisi', $editMonitoringData?->kelas_emisi ?? '') }}" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Long (0-180)</label>
                                <input id="editLongitudeDerajatInput" type="text" name="longitude_derajat"
                                    class="form-control"
                                    value="{{ old('longitude_derajat', $editMonitoringData?->longitude_derajat ?? '') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">E/W</label>
                                <input id="editLongitudeArahInput" type="text" name="longitude_arah" class="form-control"
                                    value="{{ old('longitude_arah', $editMonitoringData?->longitude_arah ?? '') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Long (0-59)</label>
                                <input id="editLongitudeMenitInput" type="text" name="longitude_menit" class="form-control"
                                    value="{{ old('longitude_menit', $editMonitoringData?->longitude_menit ?? '') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Lat (0-90)</label>
                                <input id="editLatitudeDerajatInput" type="text" name="latitude_derajat"
                                    class="form-control"
                                    value="{{ old('latitude_derajat', $editMonitoringData?->latitude_derajat ?? '') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">N/S</label>
                                <input id="editLatitudeArahInput" type="text" name="latitude_arah" class="form-control"
                                    value="{{ old('latitude_arah', $editMonitoringData?->latitude_arah ?? '') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Lat (0-59)</label>
                                <input id="editLatitudeMenitInput" type="text" name="latitude_menit" class="form-control"
                                    value="{{ old('latitude_menit', $editMonitoringData?->latitude_menit ?? '') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">North Bearing</label>
                                <input id="editNorthBearingInput" type="text" name="north_bearing" class="form-control"
                                    value="{{ old('north_bearing', $editMonitoringData?->north_bearing ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Akurasi</label>
                                <input id="editAkurasiInput" type="text" name="akurasi" class="form-control"
                                    value="{{ old('akurasi', $editMonitoringData?->akurasi ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tidak Sesuai RR</label>
                                <input id="editTidakSesuaiRRInput" type="text" name="tidak_sesuai_rr" class="form-control"
                                    value="{{ old('tidak_sesuai_rr', $editMonitoringData?->tidak_sesuai_rr ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Perkiraan Lokasi Sumber Pancaran</label>
                                <input id="editPerkiraanLokasiInput" type="text" name="perkiraan_lokasi_sumber_pancaran"
                                    class="form-control"
                                    value="{{ old('perkiraan_lokasi_sumber_pancaran', $editMonitoringData?->perkiraan_lokasi_sumber_pancaran ?? '') }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Informasi Tambahan</label>
                                <textarea id="editInformasiTambahanInput" name="informasi_tambahan" rows="4"
                                    class="form-control"
                                    placeholder="Isi informasi tambahan di sini...">{{ old('informasi_tambahan', $editMonitoringData?->informasi_tambahan ?? '') }}</textarea>
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
</div>
@endsection

@section('scripts')
    <script>
        (function() {
            function initLaporanPage() {
                const form = document.getElementById('laporanFilterForm');
                if (!form || form.dataset.filtersInitialized) {
                    // Even if form is missing, we might need to handle modals if they are present
                    // but usually they are on the same page.
                }

                if (form && !form.dataset.filtersInitialized) {
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
                    form.dataset.filtersInitialized = "true";
                }

                // --- MODAL ERROR HANDLING ---
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

                // --- EDIT MODAL SYNCING ---
                const editModalEl = document.getElementById('editLaporanModal');
                if (editModalEl && !editModalEl.dataset.editInitialized) {
                    const editModalTitle = document.getElementById('editLaporanModalLabel');
                    const editForm = editModalEl.querySelector('form');

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
                        if (field) field.value = value ?? '';
                    };

                    const syncEditModalFromData = function (monitoring, tableNumber, updateUrl) {
                        if (!monitoring) return;
                        if (editForm && updateUrl) editForm.setAttribute('action', updateUrl);

                        Object.keys(editFieldMap).forEach(function (fieldId) {
                            const monitoringKey = editFieldMap[fieldId];
                            if (fieldId === 'editTableNoInput') {
                                setFieldValue(fieldId, tableNumber || '');
                            } else if (fieldId === 'editMonitoringId') {
                                setFieldValue(fieldId, monitoring.id || '');
                            } else {
                                setFieldValue(fieldId, monitoring[monitoringKey] || '');
                            }
                        });

                        if (editModalTitle) {
                            editModalTitle.textContent = tableNumber ? `Edit Table ${tableNumber}` : 'Edit Table';
                        }
                    };

                    document.addEventListener('show.bs.modal', function (event) {
                        if (event.target.id !== 'editLaporanModal') return;
                        
                        const trigger = event.relatedTarget;
                        if (!trigger) return;

                        const tableNumber = trigger.getAttribute('data-edit-table-number') || trigger.getAttribute('data-index') || '';
                        const updateUrl = trigger.getAttribute('data-update-url') || '';
                        const payloadRaw = trigger.getAttribute('data-edit-payload');
                        
                        if (!payloadRaw) return;

                        let monitoring = null;
                        try {
                            monitoring = JSON.parse(payloadRaw);
                        } catch (e) {
                            console.error('Failed to parse edit payload', e);
                            return;
                        }

                        syncEditModalFromData(monitoring, tableNumber, updateUrl);
                    });

                    editModalEl.addEventListener('hidden.bs.modal', function () {
                        if (editForm) editForm.reset();
                        if (editModalTitle) editModalTitle.textContent = 'Edit Table';
                    });

                    const editMonitoringLoaded = @json(isset($editMonitoring) && $editMonitoring);
                    if (window.bootstrap && bootstrap.Modal && editMonitoringLoaded) {
                        bootstrap.Modal.getOrCreateInstance(editModalEl).show();
                    }
                    
                    editModalEl.dataset.editInitialized = "true";
                }

                // --- ADD MODAL DATE SYNCING ---
                const addForm = document.querySelector('#addLaporanModal form');
                const mulaiPengamatanInput = document.getElementById('mulaiPengamatanInput');
                const selesaiPengamatanWaktuInput = document.getElementById('selesaiPengamatanWaktuInput');
                const selesaiPengamatanHidden = document.getElementById('selesaiPengamatanHidden');

                if (mulaiPengamatanInput && !mulaiPengamatanInput.dataset.syncInitialized) {
                    const toMinutes = function (timeValue) {
                        if (!timeValue || !timeValue.includes(':')) return null;
                        const [hourStr, minuteStr] = timeValue.split(':');
                        return (Number(hourStr) * 60) + Number(minuteStr);
                    };

                    const syncSelesaiPengamatan = function () {
                        if (!selesaiPengamatanHidden) return;
                        const mulaiValue = mulaiPengamatanInput.value || '';
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

                    mulaiPengamatanInput.addEventListener('change', syncSelesaiPengamatan);
                    mulaiPengamatanInput.addEventListener('input', syncSelesaiPengamatan);
                    if (selesaiPengamatanWaktuInput) {
                        selesaiPengamatanWaktuInput.addEventListener('change', syncSelesaiPengamatan);
                        selesaiPengamatanWaktuInput.addEventListener('input', syncSelesaiPengamatan);
                    }
                    if (addForm) {
                        addForm.addEventListener('submit', syncSelesaiPengamatan);
                    }
                    
                    syncSelesaiPengamatan();
                    mulaiPengamatanInput.dataset.syncInitialized = "true";
                }
            }

            // Initial run
            initLaporanPage();

            // Run after Livewire navigation
            document.addEventListener('livewire:navigated', initLaporanPage);
        })();
    </script>

    <script>
        /*
         * SCROLL SHIELD ENGINE
         * Tambahkan class 'is-scrolling' ke body saat scroll berlangsung,
         * hapus 100ms setelah scroll berhenti.
         * Ini menonaktifkan pointer-events & transitions pada .action-pill
         * via CSS di atas, mencegah hover-repaint storm selama scroll.
         */
        (function() {
            var scrollTimer = null;
            var body = document.body;

            function onScroll() {
                if (!body.classList.contains('is-scrolling')) {
                    body.classList.add('is-scrolling');
                }
                if (scrollTimer) clearTimeout(scrollTimer);
                scrollTimer = setTimeout(function() {
                    body.classList.remove('is-scrolling');
                }, 100);
            }

            // Capture fase agar menangkap scroll dari semua elemen termasuk mosfet-table-wrap
            window.addEventListener('scroll', onScroll, { passive: true, capture: true });

            // Auto-hide alerts logic
            (function() {
                function initAlertAutoHide() {
                    setTimeout(function() {
                        const alerts = document.querySelectorAll('.alert');
                        alerts.forEach(function(alert) {
                            // Use Bootstrap's own method if available, or manual fade
                            alert.style.transition = 'opacity 0.6s ease, transform 0.6s ease, margin 0.6s ease';
                            alert.style.opacity = '0';
                            alert.style.transform = 'translateY(-20px)';
                            setTimeout(function() {
                                alert.remove();
                            }, 600);
                        });
                    }, 8000); // 8 seconds
                }

                document.addEventListener('DOMContentLoaded', initAlertAutoHide);
                document.addEventListener('livewire:navigated', initAlertAutoHide);
            })();

            // Tangani juga scroll di dalam table wrapper (horizontal scroll)
            document.addEventListener('DOMContentLoaded', function() {
                var wrap = document.querySelector('.mosfet-table-wrap');
                if (wrap) wrap.addEventListener('scroll', onScroll, { passive: true });
            });
            document.addEventListener('livewire:navigated', function() {
                var wrap = document.querySelector('.mosfet-table-wrap');
                if (wrap && !wrap._scrollShieldBound) {
                    wrap.addEventListener('scroll', onScroll, { passive: true });
                    wrap._scrollShieldBound = true;
                }
            });
        })();
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterForm = document.getElementById('laporanFilterForm');
            const tableContainer = document.getElementById('laporan-table-container');

            // Function to load table via AJAX
            async function loadLaporanTable(url) {
                // Prevent multi-clicks during load
                tableContainer.style.pointerEvents = 'none';

                try {
                    const response = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) throw new Error('Network response was not ok');

                    const html = await response.text();
                    tableContainer.innerHTML = html;
                    
                    // Cleanup URL to just /laporan
                    const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                    window.history.replaceState({path: cleanUrl}, '', cleanUrl);

                } catch (error) {
                    console.error('Error fetching table:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Memuat Data',
                        text: 'Terjadi kesalahan saat memuat data. Silakan coba lagi.',
                        background: '#ffffff',
                        color: '#0f172a',
                        iconColor: '#ef4444'
                    });
                } finally {
                    tableContainer.style.pointerEvents = 'auto';
                }
            }

            // Global refresh function
            window.refreshLaporanTable = function() {
                const formData = new FormData(filterForm);
                const params = new URLSearchParams(formData).toString();
                const url = filterForm.getAttribute('action') + '?' + params;
                loadLaporanTable(url);
            };

            // Intercept form submission
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                window.refreshLaporanTable();
            });

            /* Tombol Hapus — Gunakan AJAX untuk menghindari perputaran/refresh */
            document.addEventListener('click', async function(e) {
                var btn = e.target.closest('[data-delete-id]');
                if (!btn) return;
                
                window.confirmSistem('Hapus Laporan', 'Apakah Anda yakin ingin menghapus data laporan ini?', async function() {
                    const deleteUrl = btn.getAttribute('data-delete-url');
                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    
                    // Animasi loading pada baris yang dihapus
                    const row = btn.closest('tr');
                    row.style.opacity = '0.3';
                    row.style.pointerEvents = 'none';

                    try {
                        const response = await fetch(deleteUrl, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        if (response.ok) {
                        // Refresh tabel menggunakan fungsi global yang ada di laporan.blade.php
                        if (window.refreshLaporanTable) {
                            window.refreshLaporanTable();
                        } else {
                            window.location.reload();
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menghapus',
                            text: 'Data laporan gagal dihapus.',
                            background: '#ffffff',
                            color: '#0f172a',
                            iconColor: '#ef4444'
                        });
                        row.style.opacity = '1';
                        row.style.pointerEvents = 'auto';
                    }
                } catch (error) {
                    console.error('Delete error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan Koneksi',
                        text: 'Terjadi kesalahan koneksi saat menghapus data.',
                        background: '#ffffff',
                        color: '#0f172a',
                        iconColor: '#ef4444'
                    });
                    row.style.opacity = '1';
                    row.style.pointerEvents = 'auto';
                    }
                });
            });

            // Handle Add Laporan AJAX
            const addLaporanForm = document.getElementById('addLaporanForm');
            addLaporanForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalBtnHtml = submitBtn.innerHTML;
                
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

                try {
                    const response = await fetch(this.action, {
                        method: 'POST',
                        body: new FormData(this),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        // Success! Hide modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('addLaporanModal'));
                        modal.hide();
                        
                        // Reset form
                        this.reset();
                        
                        // Refresh table
                        window.refreshLaporanTable();
                        
                        // Show success message (optional, you could use a toast)
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data laporan berhasil disimpan!',
                            timer: 2000,
                            showConfirmButton: false,
                            background: '#ffffff',
                            color: '#0f172a',
                            iconColor: '#10b981'
                        });
                    } else {
                        const errorData = await response.json();
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menyimpan',
                            text: errorData.message || 'Cek kembali inputan Anda.',
                            background: '#ffffff',
                            color: '#0f172a',
                            iconColor: '#ef4444'
                        });
                    }
                } catch (error) {
                    console.error('Submit error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan Koneksi',
                        text: 'Terjadi kesalahan koneksi ke server.',
                        background: '#ffffff',
                        color: '#0f172a',
                        iconColor: '#ef4444'
                    });
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                }
            });

            // Handle Edit Laporan AJAX
            const editLaporanForm = document.getElementById('editLaporanForm');
            editLaporanForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalBtnHtml = submitBtn.innerHTML;
                
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

                try {
                    const response = await fetch(this.action, {
                        method: 'POST',
                        body: new FormData(this),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        // Success! Hide modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editLaporanModal'));
                        modal.hide();
                        
                        // Refresh table
                        window.refreshLaporanTable();
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Diperbarui!',
                            text: 'Data laporan berhasil diperbarui!',
                            timer: 2000,
                            showConfirmButton: false,
                            background: '#ffffff',
                            color: '#0f172a',
                            iconColor: '#10b981'
                        });
                    } else {
                        const errorData = await response.json();
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Memperbarui',
                            text: errorData.message || 'Cek kembali inputan Anda.',
                            background: '#ffffff',
                            color: '#0f172a',
                            iconColor: '#ef4444'
                        });
                    }
                } catch (error) {
                    console.error('Update error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan Koneksi',
                        text: 'Terjadi kesalahan koneksi ke server.',
                        background: '#ffffff',
                        color: '#0f172a',
                        iconColor: '#ef4444'
                    });
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                }
            });

            // Handle Pagination Clicks
            document.addEventListener('click', function(e) {
                const paginationLink = e.target.closest('.pagination a');
                if (paginationLink) {
                    e.preventDefault();
                    const url = paginationLink.dataset.ajaxUrl || paginationLink.href;
                    if (url && url !== 'javascript:void(0)') {
                        loadLaporanTable(url);
                    }
                }
            });

            // Smart Formatter for Frequency (Automatic dot at 3rd decimal place)
            const freqInputs = document.querySelectorAll('.freq-formatter');
            freqInputs.forEach(input => {
                input.addEventListener('input', function(e) {
                    let val = this.value.replace(/\D/g, "");
                    if (val.length > 3) {
                        this.value = val.slice(0, -3) + "." + val.slice(-3);
                    } else {
                        this.value = val;
                    }
                });

                input.addEventListener('blur', function() {
                    if (this.value === "") return;
                    let val = parseFloat(this.value);
                    if (!isNaN(val)) {
                        this.value = val.toFixed(3);
                    }
                });
            });

            // Smart Formatter for Kuat Medan (Automatic dot at 1st decimal place)
            const fieldStrengthInputs = document.querySelectorAll('.field-strength-formatter');
            fieldStrengthInputs.forEach(input => {
                input.addEventListener('input', function(e) {
                    // Hanya izinkan angka
                    let val = this.value.replace(/\D/g, "");
                    
                    if (val.length > 1) {
                        // Sisipkan titik 1 angka dari belakang (Contoh: 92 -> 9.2)
                        this.value = val.slice(0, -1) + "." + val.slice(-1);
                    } else {
                        this.value = val;
                    }
                });

                input.addEventListener('blur', function() {
                    if (this.value === "") return;
                    let val = parseFloat(this.value);
                    if (!isNaN(val)) {
                        // Keep the value as a clean float string
                        this.value = val.toString();
                    }
                });
            });

            // Handle Search Scope Dropdown
            const searchScopeOptions = document.querySelectorAll('.search-scope-option');
            const searchInHidden = document.getElementById('searchInHidden');
            const searchInToggle = document.getElementById('searchInToggle');

            searchScopeOptions.forEach(option => {
                option.addEventListener('click', function() {
                    const value = this.getAttribute('data-value');
                    const label = this.getAttribute('data-label');
                    searchInHidden.value = value;
                    searchInToggle.textContent = label;
                });
            });
            
            // Auto-submit on category change
            const kategoriSelect = filterForm.querySelector('select[name="kategori"]');
            if (kategoriSelect) {
                kategoriSelect.addEventListener('change', () => filterForm.dispatchEvent(new Event('submit')));
            }
        });
    </script>
@endsection