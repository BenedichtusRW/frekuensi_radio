@extends('layouts.app')

@section('title', 'History Hasil Pilah')
@section('page_title', 'History Hasil Pilah')

@section('content')
@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if (session('warning'))
    <div class="alert alert-warning">{{ session('warning') }}</div>
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

<div class="row g-4">
    <div class="col-12 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h3 class="h6 mb-0">Preview per File (Aktif)</h3>
            </div>
            <div class="card-body">
                @if ($recentFiles->isNotEmpty())
                    <div class="d-flex flex-column gap-2">
                        @foreach ($recentFiles as $file)
                            <div class="border rounded p-2 bg-light-subtle">
                                <a href="{{ route('pilah.preview', ['file' => $file]) }}" class="btn btn-outline-primary btn-sm text-start w-100 mb-2">
                                    {{ $file }}
                                </a>
                                <div class="d-flex gap-2">
                                    <form action="{{ route('file.archive') }}" method="POST" class="w-50">
                                        @csrf
                                        <input type="hidden" name="source_file" value="{{ $file }}">
                                        <button type="submit" class="btn btn-outline-secondary btn-sm w-100">Arsip</button>
                                    </form>
                                    <form action="{{ route('file.delete') }}" method="POST" class="w-50" onsubmit="return confirm('Yakin hapus permanen data file ini?')">
                                        @csrf
                                        <input type="hidden" name="source_file" value="{{ $file }}">
                                        <button type="submit" class="btn btn-outline-danger btn-sm w-100">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted mb-0">Tidak ada file aktif.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-info text-white">
                <h3 class="h6 mb-0">File Arsip</h3>
            </div>
            <div class="card-body">
                @if ($archivedFiles->isNotEmpty())
                    <div class="d-flex flex-column gap-2">
                        @foreach ($archivedFiles as $file)
                            <div class="border rounded p-2 bg-light">
                                <div class="small fw-semibold text-muted mb-2">{{ $file }}</div>
                                <div class="d-flex gap-2">
                                    <form action="{{ route('file.restore') }}" method="POST" class="w-50">
                                        @csrf
                                        <input type="hidden" name="source_file" value="{{ $file }}">
                                        <button type="submit" class="btn btn-outline-primary btn-sm w-100">Pulihkan</button>
                                    </form>
                                    <form action="{{ route('file.delete') }}" method="POST" class="w-50" onsubmit="return confirm('Yakin hapus permanen file arsip ini?')">
                                        @csrf
                                        <input type="hidden" name="source_file" value="{{ $file }}">
                                        <button type="submit" class="btn btn-outline-danger btn-sm w-100">Hapus Permanen</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted mb-0">Belum ada file diarsipkan.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
