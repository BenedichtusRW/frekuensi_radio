@extends('layouts.app')

@section('title', 'Data Nelayan')
@section('page_title', 'Nelayan')

@section('content')
<div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-3">
    <h2 class="h5 m-0">Data Nelayan</h2>
    <div class="d-flex gap-2">
        <a href="{{ route('export.nelayan', ['file' => $sourceFile]) }}" class="btn btn-success">Export Nelayan</a>
    </div>
</div>

@if ($sourceFile)
    <div class="alert alert-info py-2">Menampilkan hasil pilah dari file: <strong>{{ $sourceFile }}</strong></div>
@endif

@include('partials.hasil-pilah-ringkasan', ['activeType' => 'nelayan'])

<div class="small text-muted mb-2">Tip: geser tabel ke kanan untuk melihat seluruh kolom logbook.</div>

<div class="table-responsive">
    <table class="table table-striped table-bordered align-middle bg-white table-logbook">
        <thead class="table-light">
            <tr>
                <th>Kode Negara</th>
                <th>Stasiun Monitor</th>
                <th>Frekuensi (kHz)</th>
                <th>Tanggal</th>
                <th>Bulan</th>
                <th>Mulai</th>
                <th>Akhir</th>
                <th>Kuat Medan (dBµV/m)</th>
                <th>Identifikasi</th>
                <th>Administrasi</th>
                <th>Kelas St.</th>
                <th>Lebar Band</th>
                <th>Kelas Emisi</th>
                <th>Lokasi Sumber</th>
                <th>Long (0-180)</th>
                <th>E/W</th>
                <th>Long (0-59)</th>
                <th>Lat (0-90)</th>
                <th>N/S</th>
                <th>Lat (0-59)</th>
                <th>North Bearing</th>
                <th>Akurasi</th>
                <th>Tidak Sesuai RR</th>
                <th>Informasi Tambahan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($logs as $log)
                <tr>
                    <td>{{ $log->kode_negara ?? '-' }}</td>
                    <td>{{ $log->stasiun_monitor ?? '-' }}</td>
                    <td>{{ $log->frekuensi_khz ?? '-' }}</td>
                    <td>{{ $log->tanggal ?? '-' }}</td>
                    <td>{{ $log->bulan ?? '-' }}</td>
                    <td>{{ $log->jam_mulai ?? '-' }}</td>
                    <td>{{ $log->jam_akhir ?? '-' }}</td>
                    <td>{{ $log->kuat_medan_dbuvm ?? '-' }}</td>
                    <td class="wrap-cell">{{ $log->identifikasi ?? '-' }}</td>
                    <td class="wrap-cell">{{ $log->administrasi_termonitor ?? '-' }}</td>
                    <td>{{ $log->kelas_stasiun ?? '-' }}</td>
                    <td>{{ $log->lebar_band ?? '-' }}</td>
                    <td>{{ $log->kelas_emisi ?? '-' }}</td>
                    <td class="wrap-cell">{{ $log->perkiraan_lokasi_sumber_pancaran ?? '-' }}</td>
                    <td>{{ $log->longitude_derajat ?? '-' }}</td>
                    <td>{{ $log->longitude_arah ?? '-' }}</td>
                    <td>{{ $log->longitude_menit ?? '-' }}</td>
                    <td>{{ $log->latitude_derajat ?? '-' }}</td>
                    <td>{{ $log->latitude_arah ?? '-' }}</td>
                    <td>{{ $log->latitude_menit ?? '-' }}</td>
                    <td>{{ $log->north_bearing ?? '-' }}</td>
                    <td>{{ $log->akurasi ?? '-' }}</td>
                    <td>{{ $log->tidak_sesuai_rr ?? '-' }}</td>
                    <td class="wrap-cell">{{ $log->informasi_tambahan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="24" class="text-center text-muted">Belum ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $logs->links() }}
@endsection
