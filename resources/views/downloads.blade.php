@extends('layouts.app')

@section('title', 'Download Laporan')
@section('page_title', 'Download Laporan')

@section('content')
@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-header">Arsip Hasil Export XLSX</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;" class="text-center">No</th>
                        <th>Nama File</th>
                        <th style="width: 300px;">Metadata Export</th>
                        <th style="width: 140px;" class="text-center">Ukuran</th>
                        <th style="width: 190px;" class="text-center">Terakhir Dibuat</th>
                        <th style="width: 220px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($archives as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item['name'] }}</td>
                            <td>
                                <div><strong>Filter:</strong> {{ $item['filter_label'] ?? '-' }}</div>
                                <div><strong>Jumlah Data:</strong> {{ number_format((int) ($item['row_count'] ?? 0), 0, ',', '.') }} baris</div>
                            </td>
                            <td class="text-center">{{ $item['size_label'] }}</td>
                            <td class="text-center">{{ $item['modified_at']->format('d/m/Y H:i:s') }}</td>
                            <td class="text-center">
                                <div class="d-inline-flex gap-2">
                                    <a href="{{ route('monitoring.downloads.file', ['file' => $item['name']]) }}" class="btn btn-sm btn-primary">Download Ulang</a>
                                    <form method="POST" action="{{ route('monitoring.downloads.delete', ['file' => $item['name']]) }}" onsubmit="return confirm('Hapus file arsip ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada arsip export XLSX.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
