@extends('layouts.app')

@php
    $editNo = isset($editTableNumber) && $editTableNumber !== null ? (int) $editTableNumber : null;
    $editLabel = $editNo !== null ? 'Edit Tabel ' . $editNo : 'Edit Laporan Harian';
@endphp

@section('title', isset($monitoring) ? $editLabel : 'Input Laporan Harian')
@section('page_title', isset($monitoring) ? $editLabel : 'Input Laporan Harian')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header">
            {{ isset($monitoring) ? 'Perbaiki data logbook harian' : 'Input data logbook harian' }}
        </div>
        <div class="card-body">
            <form method="POST"
                action="{{ isset($monitoring) ? route('monitoring.update', $monitoring->id) : route('monitoring.store') }}">
                @csrf
                @if (isset($monitoring))
                    @method('PUT')
                    <input type="hidden" name="edit_table_no" value="{{ old('edit_table_no', $editNo) }}">
                @endif

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Jenis Laporan (Kategori) <span class="text-danger">*</span></label>
                        @php
                            $selectedKategori = old('kategori', $monitoring->kategori ?? '');
                        @endphp
                        <select name="kategori" class="form-select" required>
                            <option value="" disabled selected {{ $selectedKategori === '' ? 'selected' : '' }}>Pilih Jenis Laporan...</option>
                            @foreach (($dropdownOptions['kategori'] ?? ['MF', 'HF Rutin', 'HF Nelayan']) as $cat)
                                <option value="{{ $cat }}" {{ $selectedKategori === $cat ? 'selected' : '' }}>
                                    {{ $cat }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Negara <span class="text-danger">*</span></label>
                        @php
                            $selectedNegara = old('kode_negara', $monitoring->kode_negara ?? '');
                        @endphp
                        <select name="kode_negara" class="form-select max-h-40" required>
                            <option value="" disabled hidden {{ $selectedNegara === '' ? 'selected' : '' }}></option>
                            @foreach (($dropdownOptions['kode_negara'] ?? ['INDONESIA (INS)']) as $negara)
                                <option value="{{ $negara }}" {{ $selectedNegara === $negara ? 'selected' : '' }}>{{ $negara }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Stasiun Monitor <span class="text-danger">*</span></label>
                        @php
                            $selectedStasiun = old('stasiun_monitor', $monitoring->stasiun_monitor ?? '');
                        @endphp
                        <select name="stasiun_monitor" class="form-select max-h-40" required>
                            <option value="" disabled hidden {{ $selectedStasiun === '' ? 'selected' : '' }}></option>
                            @foreach (($dropdownOptions['stasiun_monitor'] ?? ['MSHF LAMPUNG']) as $stasiun)
                                <option value="{{ $stasiun }}" {{ $selectedStasiun === $stasiun ? 'selected' : '' }}>
                                    {{ $stasiun }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Frekuensi (kHz) <span class="text-danger">*</span></label>
                        <input type="number" step="0.001" name="frekuensi_khz" class="form-control"
                            value="{{ old('frekuensi_khz', $monitoring->frekuensi_khz ?? '') }}">
                    </div>

                    @if (isset($monitoring))
                        @php
                            $jamMulaiStored = str_replace('.', ':', (string) ($monitoring->jam_mulai ?? '00:00'));
                            $jamMulaiStored = preg_match('/^\d{2}:\d{2}$/', $jamMulaiStored) ? $jamMulaiStored : '00:00';
                            $mulaiPengamatanDefault = sprintf(
                                '%04d-%02d-%02dT%s',
                                (int) ($monitoring->tahun ?? now()->year),
                                (int) ($monitoring->bulan ?? now()->month),
                                (int) ($monitoring->tanggal ?? now()->day),
                                $jamMulaiStored
                            );

                            $jamAkhirStored = str_replace('.', ':', (string) ($monitoring->jam_akhir ?? ''));
                            $jamAkhirStored = preg_match('/^\d{2}:\d{2}$/', $jamAkhirStored) ? $jamAkhirStored : '';
                        @endphp

                        <div class="col-md-6">
                            <label class="form-label">Mulai Pengamatan <span class="text-danger">*</span></label>
                            <input type="datetime-local" id="editMulaiPengamatanInput" name="mulai_pengamatan"
                                class="form-control" value="{{ old('mulai_pengamatan', $mulaiPengamatanDefault) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Selesai Pengamatan <span class="text-danger">*</span></label>
                            <input type="time" id="editSelesaiPengamatanInput" name="selesai_pengamatan_waktu"
                                class="form-control" step="60" value="{{ old('selesai_pengamatan_waktu', $jamAkhirStored) }}">
                        </div>
                    @else
                        <div class="col-md-4">
                            <label class="form-label">Tanggal (1-31)</label>
                            <select name="tanggal" class="form-select">
                                <option value="" disabled hidden {{ old('tanggal', optional($monitoring)->tanggal ?? '') === '' ? 'selected' : '' }}></option>
                                @for ($day = 1; $day <= 31; $day++)
                                    <option value="{{ $day }}" {{ (string) old('tanggal', optional($monitoring)->tanggal ?? '') === (string) $day ? 'selected' : '' }}>{{ str_pad((string) $day, 2, '0', STR_PAD_LEFT) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Bulan (1-12)</label>
                            <select name="bulan" class="form-select">
                                <option value="" disabled hidden {{ old('bulan', optional($monitoring)->bulan ?? '') === '' ? 'selected' : '' }}></option>
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
                                @foreach ($monthNames as $monthNumber => $monthLabel)
                                    <option value="{{ $monthNumber }}" {{ (string) old('bulan', optional($monitoring)->bulan ?? '') === (string) $monthNumber ? 'selected' : '' }}>{{ str_pad((string) $monthNumber, 2, '0', STR_PAD_LEFT) }}
                                        - {{ $monthLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tahun</label>
                            <select name="tahun" class="form-select">
                                <option value="" disabled hidden {{ old('tahun', optional($monitoring)->tahun ?? '') === '' ? 'selected' : '' }}></option>
                                @for ($year = 2024; $year <= 2026; $year++)
                                    <option value="{{ $year }}" {{ (string) old('tahun', optional($monitoring)->tahun ?? '') === (string) $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Mulai Pengamatan <span class="text-danger">*</span></label>
                            @php
                                $jamMulaiRaw = (string) old('jam_mulai', optional($monitoring)->jam_mulai ?? '');
                                $jamMulaiValue = str_contains($jamMulaiRaw, '.') ? str_replace('.', ':', $jamMulaiRaw) : $jamMulaiRaw;
                            @endphp
                            <input type="time" name="jam_mulai" class="form-control" step="60" value="{{ $jamMulaiValue }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Selesai Pengamatan <span class="text-danger">*</span></label>
                            @php
                                $jamAkhirRaw = (string) old('jam_akhir', optional($monitoring)->jam_akhir ?? '');
                                $jamAkhirValue = str_contains($jamAkhirRaw, '.') ? str_replace('.', ':', $jamAkhirRaw) : $jamAkhirRaw;
                            @endphp
                            <input type="time" name="jam_akhir" class="form-control" step="60" value="{{ $jamAkhirValue }}">
                        </div>
                    @endif

                    <div class="col-md-4">
                        <label class="form-label">Kuat Medan (dBuV/m)</label>
                        <input type="number" step="0.01" name="kuat_medan_dbuvm" class="form-control"
                            value="{{ old('kuat_medan_dbuvm', optional($monitoring)->kuat_medan_dbuvm ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Identifikasi <span class="text-danger">*</span></label>
                        <input type="text" name="identifikasi" class="form-control"
                            value="{{ old('identifikasi', $monitoring->identifikasi ?? '') }}"
                            placeholder="Isi UNKNOWN jika tidak teridentifikasi">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Administrasi Termonitor</label>
                        @php
                            $selectedAdministrasi = old('administrasi_termonitor', $monitoring->administrasi_termonitor ?? '');
                        @endphp
                        <select name="administrasi_termonitor" class="form-select">
                            <option value="" disabled {{ $selectedAdministrasi === '' ? 'selected' : '' }}></option>
                            @foreach (($dropdownOptions['administrasi_termonitor'] ?? ['INS']) as $administrasiTermonitor)
                                <option value="{{ $administrasiTermonitor }}" {{ $selectedAdministrasi === $administrasiTermonitor ? 'selected' : '' }}>{{ $administrasiTermonitor }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Kelas Stasiun <span class="text-danger">*</span></label>
                        @php
                            $selectedKelasStasiun = old('kelas_stasiun', $monitoring->kelas_stasiun ?? '');
                        @endphp
                        <select name="kelas_stasiun" class="form-select max-h-40">
                            <option value="" disabled {{ $selectedKelasStasiun === '' ? 'selected' : '' }}></option>
                            @foreach (($dropdownOptions['kelas_stasiun'] ?? ['AL', 'AM', 'AT', 'BC', 'BT', 'FA', 'FB', 'FC', 'FD', 'FG', 'FL', 'FP', 'FX', 'LR', 'MA', 'ML', 'MO', 'MR', 'MS', 'NL', 'NR', 'OD', 'OE', 'PL', 'RM', 'RN', 'SA', 'SM', 'SS', 'TC', 'UV', 'UW']) as $kelasStasiun)
                                <option value="{{ $kelasStasiun }}" {{ $selectedKelasStasiun === $kelasStasiun ? 'selected' : '' }}>{{ $kelasStasiun }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Lebar Band <span class="text-danger">*</span></label>
                        <input type="text" name="lebar_band" class="form-control"
                            value="{{ old('lebar_band', $monitoring->lebar_band ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kelas Emisi <span class="text-danger">*</span></label>
                        @php
                            $currentEmisi = old('kelas_emisi', $monitoring->kelas_emisi ?? '');
                        @endphp
                        @if($dropdownOptions['config']['kelas_emisi_manual'] ?? false)
                            {{-- Mode Manual: Input Text Biasa --}}
                            <input type="text" name="kelas_emisi" class="form-control"
                                value="{{ $currentEmisi }}" required>
                        @else
                            {{-- Mode Master Data: Dropdown --}}
                            <select name="kelas_emisi" class="form-select" required>
                                <option value="" disabled selected>Pilih Kelas Emisi...</option>
                                @foreach (($dropdownOptions['kelas_emisi'] ?? []) as $emisi)
                                    <option value="{{ $emisi }}" {{ $currentEmisi === $emisi ? 'selected' : '' }}>
                                        {{ $emisi }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Long (0-180)</label>
                        <input type="text" name="longitude_derajat" class="form-control"
                            value="{{ old('longitude_derajat', $monitoring->longitude_derajat ?? '') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">E atau W</label>
                        <input type="text" name="longitude_arah" class="form-control"
                            value="{{ old('longitude_arah', $monitoring->longitude_arah ?? '') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Long (0-59)</label>
                        <input type="text" name="longitude_menit" class="form-control"
                            value="{{ old('longitude_menit', $monitoring->longitude_menit ?? '') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Lat (0-90)</label>
                        <input type="text" name="latitude_derajat" class="form-control"
                            value="{{ old('latitude_derajat', $monitoring->latitude_derajat ?? '') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">N atau S</label>
                        <input type="text" name="latitude_arah" class="form-control"
                            value="{{ old('latitude_arah', $monitoring->latitude_arah ?? '') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Lat (0-59)</label>
                        <input type="text" name="latitude_menit" class="form-control"
                            value="{{ old('latitude_menit', $monitoring->latitude_menit ?? '') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">North Bearing</label>
                        <input type="text" name="north_bearing" class="form-control"
                            value="{{ old('north_bearing', $monitoring->north_bearing ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Akurasi</label>
                        <input type="text" name="akurasi" class="form-control"
                            value="{{ old('akurasi', $monitoring->akurasi ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tidak Sesuai RR</label>
                        <input type="text" name="tidak_sesuai_rr" class="form-control"
                            value="{{ old('tidak_sesuai_rr', $monitoring->tidak_sesuai_rr ?? '') }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Perkiraan Lokasi Sumber Pancaran</label>
                        <input type="text" name="perkiraan_lokasi_sumber_pancaran" class="form-control"
                            value="{{ old('perkiraan_lokasi_sumber_pancaran', $monitoring->perkiraan_lokasi_sumber_pancaran ?? '') }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Informasi Tambahan</label>
                        <textarea name="informasi_tambahan" class="form-control"
                            rows="3">{{ old('informasi_tambahan', $monitoring->informasi_tambahan ?? '') }}</textarea>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 mt-4 justify-content-end">
                    <a href="{{ route('monitoring.index') }}" wire:navigate class="btn btn-outline-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary">{{ isset($monitoring) ? 'Update' : 'Simpan' }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const jamMulaiInput = document.querySelector('input[name="jam_mulai"]');
            const jamAkhirInput = document.querySelector('input[name="jam_akhir"]');
            const mulaiPengamatanInput = document.getElementById('editMulaiPengamatanInput');
            const selesaiPengamatanInput = document.getElementById('editSelesaiPengamatanInput');
            const form = document.querySelector('form[action*="/input"]');

            if (mulaiPengamatanInput && selesaiPengamatanInput) {
                const toMinutes = function (timeValue) {
                    if (!timeValue || !timeValue.includes(':')) {
                        return null;
                    }

                    const parts = timeValue.split(':');
                    const hour = Number(parts[0]);
                    const minute = Number(parts[1]);

                    if (!Number.isFinite(hour) || !Number.isFinite(minute)) {
                        return null;
                    }

                    return (hour * 60) + minute;
                };

                const validateDatetimeTime = function () {
                    const mulaiValue = mulaiPengamatanInput.value || '';
                    const selesaiValue = selesaiPengamatanInput.value || '';

                    selesaiPengamatanInput.setCustomValidity('');
                    selesaiPengamatanInput.removeAttribute('min');

                    if (!mulaiValue || !mulaiValue.includes('T')) {
                        return true;
                    }

                    const mulaiTime = mulaiValue.split('T')[1].slice(0, 5);
                    selesaiPengamatanInput.setAttribute('min', mulaiTime);

                    const mulaiMinutes = toMinutes(mulaiTime);
                    const selesaiMinutes = toMinutes(selesaiValue);
                    if (mulaiMinutes !== null && selesaiMinutes !== null && selesaiMinutes < mulaiMinutes) {
                        selesaiPengamatanInput.setCustomValidity('Selesai Pengamatan harus sama atau lebih besar dari Mulai Pengamatan.');
                        return false;
                    }

                    return true;
                };

                mulaiPengamatanInput.addEventListener('input', validateDatetimeTime);
                mulaiPengamatanInput.addEventListener('change', validateDatetimeTime);
                selesaiPengamatanInput.addEventListener('input', validateDatetimeTime);
                selesaiPengamatanInput.addEventListener('change', validateDatetimeTime);

                if (form) {
                    form.addEventListener('submit', function (event) {
                        if (!validateDatetimeTime()) {
                            event.preventDefault();
                            selesaiPengamatanInput.reportValidity();
                        }
                    });
                }

                validateDatetimeTime();
                return;
            }

            if (!jamMulaiInput || !jamAkhirInput) {
                return;
            }

            const validateJam = function () {
                const jamMulai = jamMulaiInput.value || '';
                const jamAkhir = jamAkhirInput.value || '';

                jamAkhirInput.setCustomValidity('');
                jamAkhirInput.removeAttribute('min');

                if (!jamMulai) {
                    return true;
                }

                jamAkhirInput.setAttribute('min', jamMulai);

                if (jamAkhir && jamAkhir < jamMulai) {
                    jamAkhirInput.setCustomValidity('Selesai Pengamatan harus sama atau lebih besar dari Mulai Pengamatan.');
                    return false;
                }

                return true;
            };

            jamMulaiInput.addEventListener('input', validateJam);
            jamMulaiInput.addEventListener('change', validateJam);
            jamAkhirInput.addEventListener('input', validateJam);
            jamAkhirInput.addEventListener('change', validateJam);

            if (form) {
                form.addEventListener('submit', function (event) {
                    if (!validateJam()) {
                        event.preventDefault();
                        jamAkhirInput.reportValidity();
                    }
                });
            }

            validateJam();

            // --- PENCEGAHAN DOUBLE SUBMIT FRONTEND ---
            const allForms = document.querySelectorAll('form');
            allForms.forEach(function (f) {
                f.addEventListener('submit', function () {
                    setTimeout(function () {
                        const btn = f.querySelector('button[type="submit"]');
                        if (btn && !f.querySelector(':invalid')) {
                            btn.disabled = true;
                            btn.textContent = 'Menyimpan...';
                        }
                    }, 10);
                });
            });
        });
    </script>
@endsection