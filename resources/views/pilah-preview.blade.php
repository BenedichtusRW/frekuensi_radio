@extends('layouts.app')

@section('title', 'Preview Hasil Pilah')
@section('page_title', 'Preview Hasil Pilah')

@section('content')
@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if (session('warning'))
    <div class="alert alert-warning">{{ session('warning') }}</div>
@endif

<div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-3">
    <div>
        <h2 class="h5 mb-1">File Sumber: {{ $sourceFile }}</h2>
        <p class="text-muted mb-0">Preview menampilkan 10 baris pertama untuk tiap pita, dengan format kolom sama seperti LOGBOOK Balmon.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('export.nelayan', ['file' => $sourceFile]) }}" class="btn btn-outline-primary btn-sm">Download Nelayan</a>
        <a href="{{ route('export.rutin', ['file' => $sourceFile]) }}" class="btn btn-outline-primary btn-sm">Download Rutin</a>
        <a href="{{ route('export.mf', ['file' => $sourceFile]) }}" class="btn btn-outline-primary btn-sm">Download MF</a>
    </div>
</div>

@include('partials.hasil-pilah-ringkasan', ['activeType' => 'nelayan', 'counts' => $counts])

<div class="small text-muted mb-2">Tip: geser tabel ke kanan untuk melihat seluruh kolom logbook.</div>

<style>
    .table-logbook thead th {
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
        font-size: 0.78rem;
        border-color: #000;
    }

    .table-logbook tbody td {
        font-size: 0.76rem;
        white-space: nowrap;
        border-color: #000;
    }

    .table-logbook .number-row td {
        background: #fde9d9;
        text-align: center;
        font-size: 0.68rem;
        padding-top: 0.2rem;
        padding-bottom: 0.2rem;
        border-color: #000;
    }

    .table-logbook .wrap-cell {
        white-space: normal;
        min-width: 180px;
    }
</style>

@foreach (['HF Nelayan' => ['type' => 'nelayan', 'data' => $previewNelayan], 'HF Rutin' => ['type' => 'rutin', 'data' => $previewRutin], 'MF' => ['type' => 'mf', 'data' => $previewMf]] as $title => $section)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light">
            <strong>{{ $title }}</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle bg-white table-logbook">
                    <thead class="table-light">
                        <tr>
                            <th colspan="2">Monitoring Center</th>
                            <th colspan="22">Keterangan dari stasiun yang dimonitor</th>
                        </tr>
                        <tr>
                            <th>Kode Negara</th>
                            <th>Stasiun Monitor</th>
                            <th>Frekuensi (KHz)</th>
                            <th>Tanggal</th>
                            <th>Bulan</th>
                            <th>Tahun</th>
                            <th>Mulai</th>
                            <th>Akhir</th>
                            <th>Kuat Medan (dBµV/m)</th>
                            <th>Identifikasi</th>
                            <th>Administrasi Termonitor</th>
                            <th>Kelas Stasiun</th>
                            <th>Lebar Band</th>
                            <th>Kelas Emisi</th>
                            <th>Long (0-180)</th>
                            <th>E atau W</th>
                            <th>Long (0-59)</th>
                            <th>Lat (0-90)</th>
                            <th>N atau S</th>
                            <th>Lat (0-59)</th>
                        </tr>
                        <tr class="number-row">
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
                        @forelse ($section['data'] as $row)
                            <tr>
                                <td>{{ $row->kode_negara ?? '-' }}</td>
                                <td>{{ $row->stasiun_monitor ?? '-' }}</td>
                                <td>{{ $row->frekuensi_khz ?? '-' }}</td>
                                <td>{{ $row->tanggal ?? '-' }}</td>
                                <td>{{ $row->bulan ?? '-' }}</td>
                                <td>{{ $row->tahun ?? '-' }}</td>
                                <td>{{ $row->jam_mulai ?? '-' }}</td>
                                <td>{{ $row->jam_akhir ?? '-' }}</td>
                                <td>{{ $row->kuat_medan_dbuvm ?? '-' }}</td>
                                <td class="wrap-cell">{{ $row->identifikasi ?? '-' }}</td>
                                <td class="wrap-cell">{{ $row->administrasi_termonitor ?? '-' }}</td>
                                <td>{{ $row->kelas_stasiun ?? '-' }}</td>
                                <td>{{ $row->lebar_band ?? '-' }}</td>
                                <td>{{ $row->kelas_emisi ?? '-' }}</td>
                                <td>{{ $row->longitude_derajat ?? '-' }}</td>
                                <td>{{ $row->longitude_arah ?? '-' }}</td>
                                <td>{{ $row->longitude_menit ?? '-' }}</td>
                                <td>{{ $row->latitude_derajat ?? '-' }}</td>
                                <td>{{ $row->latitude_arah ?? '-' }}</td>
                                <td>{{ $row->latitude_menit ?? '-' }}</td>
                                <td>{{ $row->north_bearing ?? '-' }}</td>
                                <td>{{ $row->akurasi ?? '-' }}</td>
                                <td>{{ $row->tidak_sesuai_rr ?? '-' }}</td>
                                <td class="wrap-cell">{{ $row->informasi_tambahan ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="24" class="text-center text-muted">Belum ada data untuk pita ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endforeach
@endsection
