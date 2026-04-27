<div id="tabel-frekuensi" class="card border-slate-200 overflow-hidden"
    style="background: #ffffff; border-radius: 1rem; border: none;">
    <div class="card-header bg-white py-2 px-3 border-bottom border-slate-100 d-flex align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-1">
            <span class="fw-bold text-slate-500" style="font-size: 0.65rem;">ENTRIES</span>
            <select class="form-select form-select-sm border-slate-200" style="width: 55px; font-size: 0.75rem; padding: 0.1rem 0.3rem;" disabled>
                <option selected>10</option>
            </select>

            {{-- Tombol Hapus Semua Data - Resized & Relocated --}}
            @if(auth()->user()->role === 'super_admin')
                <button type="button" id="deleteAllBtn" class="btn btn-danger d-flex align-items-center justify-content-center gap-1 shadow-sm ms-1"
                    style="font-size: 0.65rem; padding: 0.2rem 0.5rem; height: 24px; border-radius: 0.4rem;" 
                    onclick="confirmDeleteAll()">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                    <span>Hapus Semua</span>
                </button>

                <form id="deleteAllMonitoringForm" action="{{ route('monitoring.delete-all', request()->query()) }}" method="POST" style="display:none;">
                    @csrf
                    @method('DELETE')
                </form>
            @endif
        </div>
        <div class="action-buttons-wrap d-flex align-items-center gap-1">
            {{-- Tombol Utama Masuk Mode Hapus --}}
            <button type="button" id="enterBulkModeBtn" class="btn btn-danger d-flex align-items-center justify-content-center gap-1 shadow-sm ms-1"
                style="font-size: 0.65rem; padding: 0.2rem 0.5rem; height: 24px; border-radius: 0.4rem;" 
                onclick="toggleBulkMode(true)">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6"/></svg>
                <span class="text-nowrap">Hapus Beberapa</span>
            </button>
            
            {{-- Tombol Batal Hapus --}}
            <button type="button" id="cancelBulkModeBtn" class="btn btn-light border d-flex align-items-center justify-content-center shadow-sm ms-1" 
                style="display: none; font-size: 0.65rem; padding: 0.2rem 0.5rem; height: 24px; border-radius: 0.4rem;" 
                onclick="toggleBulkMode(false)">
                <span class="text-nowrap">Batal</span>
            </button>

            {{-- Tombol Konfirmasi Hapus (Hanya muncul jika ada yang dipilih) --}}
            <button type="button" id="bulkDeleteBtn" class="btn btn-danger d-none align-items-center justify-content-center gap-1 shadow-sm ms-1" 
                style="font-size: 0.65rem; padding: 0.2rem 0.5rem; height: 24px; border-radius: 0.4rem;" 
                onclick="confirmBulkDelete()">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6"/></svg>
                <span class="text-nowrap">Hapus Terpilih (<span id="selectedCount">0</span>)</span>
            </button>



            <button type="button" id="addLaporanBtn" class="btn-minimal btn-action-responsive d-flex align-items-center justify-content-center gap-1" 
                data-bs-toggle="modal" 
                data-bs-target="#addLaporanModal"
                data-bs-focus="false">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span class="text-nowrap fw-bold">Tambah Data</span>
            </button>
            <a id="exportLaporanBtn" href="{{ route('monitoring.export', request()->query()) }}#daftar-laporan" class="btn-minimal btn-action-responsive d-flex align-items-center justify-content-center gap-1" style="color: #15803d; border-color: #bbf7d0; background-color: #f0fdf4;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="8" y1="13" x2="16" y2="13"></line><line x1="8" y1="17" x2="16" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                <span class="text-nowrap fw-bold">Export XLSX</span>
            </a>
        </div>
    </div>

    <style>
        /* Responsive Table Actions */
        .btn-action-responsive {
            padding: 0 0.75rem;
            min-width: 0;
            font-size: 0.75rem;
            height: 32px;
            justify-content: center;
            text-align: center;
            display: inline-flex;
            width: auto;
        }
        @media (max-width: 768px) {
            .action-buttons-wrap { flex: 1; justify-content: flex-end; }
            .btn-action-responsive { flex: 1; font-size: 0.65rem; padding: 0 0.2rem; }
        }
        
        .btn-action-responsive svg {
            position: static !important;
            opacity: 1 !important;
        }

        .mosfet-table { 
            border-collapse: separate !important; 
            border-spacing: 0 !important; 
            width: 100%; 
            /* CRITICAL: Tell browser NOT to calculate width based on text content */
            table-layout: fixed !important;
            /* GPU pre-rendering hint */
            transform: translateZ(0);
        }
        
        .mosfet-table th, .mosfet-table td {
            border-bottom: 1px solid rgba(15, 23, 42, 0.1) !important;
            border-right: 1px solid rgba(15, 23, 42, 0.1) !important;
        }

        /* Style Baris Data */
        .break-date-row td { background-color: #fff200 !important; }
        .column-label-row td { background-color: #FDE9D9 !important; }

        /* Badge Kategori */
        .badge-kategori {
            padding: 0.2rem 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.65rem;
            font-weight: 600;
            white-space: nowrap;
        }
        .badge-mf { background-color: #e2e8f0; color: #475569; }
        .badge-rutin { background-color: #dcfce7; color: #166534; }
        .badge-nelayan { background-color: #fee2e2; color: #991b1b; }

        /* Action Pills - Isolated for scroll performance */
        .action-pill {
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.375rem;
            border: 1px solid #e2e8f0;
            background: white;
            cursor: pointer;
            /* Force GPU layer to avoid hover-repaint during scroll */
            transform: translateZ(0);
            backface-visibility: hidden;
        }
        .action-pill:hover { background: #f8fafc; border-color: #cbd5e1; }
        .action-pill.view { color: #10b981; }
        .action-pill.edit { color: #3b82f6; }
        .action-pill.delete { color: #ef4444; }

        /* Bulk Delete Column Visibility */
        .bulk-delete-col { display: none; }
        .bulk-mode-active .bulk-delete-col { display: table-cell; }
        .bulk-mode-active col.bulk-delete-col { display: table-column; }
    </style>

    <div class="card-body p-0">
        <div class="mosfet-table-wrap">
            <table class="table table-hover table-sm align-middle mosfet-table">
                <colgroup>
                    <col class="bulk-delete-col" style="width: 40px;"><!-- Checkbox -->
                    <col style="width: 52px;"><!-- No -->
                    <col style="width: 96px;"><!-- Kode Negara -->
                    <col style="width: 156px;"><!-- Stasiun Monitor -->
                    <col style="width: 120px;"><!-- Frekuensi -->
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
                    <col style="width: 160px;"> <!-- Informasi Tambahan -->
                    <col style="width: 160px;"> <!-- Petugas -->
                    <col style="width: 180px;"> <!-- Jenis Laporan -->
                    <col style="width: 130px;"> <!-- Updated At -->
                    <col style="width: 100px;"> <!-- Aksi -->
                </colgroup>
                <thead>
                    <tr class="header-group">
                        <th rowspan="4" class="text-center bulk-delete-col">
                            <input type="checkbox" class="form-check-input" id="selectAllCheckbox" onchange="toggleSelectAll()">
                        </th>
                        <th rowspan="4">No</th>
                        <th colspan="3" class="text-center">Monitoring Center</th>
                        <th colspan="21">Keterangan dari stasiun yang dimonitor</th>
                        <th rowspan="4">Petugas</th>
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
                                <td class="text-center bulk-delete-col">
                                    <input type="checkbox" class="form-check-input row-checkbox" value="{{ $item->id }}" onchange="updateBulkDeleteBtn()">
                                </td>
                                <td>{{ $rowNumber }}</td>
                                <td>{{ (trim((string)$item->kode_negara) === '') ? '-' : $item->kode_negara }}</td>
                                <td>{{ (trim((string)$item->stasiun_monitor) === '') ? '-' : $item->stasiun_monitor }}</td>
                                <td>{{ $item->frekuensi_khz !== null ? number_format($item->frekuensi_khz, 3, '.', '') : '-' }}</td>
                                <td>{{ $item->tanggal ?? '-' }}</td>
                                <td>{{ $item->bulan ?? '-' }}</td>
                                <td>{{ $item->tahun ?? '-' }}</td>
                                <td>{{ $item->jam_mulai ?? '-' }}</td>
                                <td>{{ $item->jam_akhir ?? '-' }}</td>
                                <td>{{ $item->kuat_medan_dbuvm !== null ? (float)$item->kuat_medan_dbuvm : '-' }}</td>
                                <td>{{ (trim((string)$item->identifikasi) === '') ? '-' : $item->identifikasi }}</td>
                                <td>{{ (trim((string)$item->administrasi_termonitor) === '') ? '-' : $item->administrasi_termonitor }}</td>
                                <td>{{ (trim((string)$item->kelas_stasiun) === '') ? '-' : $item->kelas_stasiun }}</td>
                                <td>{{ (trim((string)$item->lebar_band) === '') ? '-' : $item->lebar_band }}</td>
                                <td>{{ (trim((string)$item->kelas_emisi) === '') ? '-' : $item->kelas_emisi }}</td>
                                <td>{{ (trim((string)$item->longitude_derajat) === '') ? '-' : $item->longitude_derajat }}</td>
                                <td>{{ (trim((string)$item->longitude_arah) === '') ? '-' : $item->longitude_arah }}</td>
                                <td>{{ (trim((string)$item->longitude_menit) === '') ? '-' : $item->longitude_menit }}</td>
                                <td>{{ (trim((string)$item->latitude_derajat) === '') ? '-' : $item->latitude_derajat }}</td>
                                <td>{{ (trim((string)$item->latitude_arah) === '') ? '-' : $item->latitude_arah }}</td>
                                <td>{{ (trim((string)$item->latitude_menit) === '') ? '-' : $item->latitude_menit }}</td>
                                <td>{{ (trim((string)$item->north_bearing) === '') ? '-' : $item->north_bearing }}</td>
                                <td>{{ (trim((string)$item->akurasi) === '') ? '-' : $item->akurasi }}</td>
                                <td>{{ (trim((string)$item->tidak_sesuai_rr) === '') ? '-' : $item->tidak_sesuai_rr }}</td>
                                <td class="text-center" style="white-space: normal !important; line-height: 1.2;">{{ (trim((string)$item->informasi_tambahan) === '') ? '-' : $item->informasi_tambahan }}</td>
                                <td style="white-space: normal !important; text-align: center; line-height: 1.2;">
                                    <span class="text-slate-600 fw-medium">{{ $item->user->name ?? 'System' }}</span>
                                </td>
                                <td>
                                    @php
                                        $kategoriLabel = match ($item->kategori) {
                                            'MF' => 'HF Medium Frequency',
                                            'HF Rutin' => 'HF Rutin',
                                            'HF Nelayan' => 'HF Nelayan',
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
                                    {{-- Tombol ringan: hanya data-id, JSON payload ada di JS store di bawah --}}
                                    <div class="d-flex gap-1">
                                        <button type="button" class="action-pill view" title="Lihat Detail"
                                            data-bs-toggle="modal" data-bs-target="#detailLaporanModal"
                                            data-bs-focus="false"
                                            data-row-number="{{ $rowNumber }}"
                                            data-view-payload="{{ json_encode([
                                                'kode_negara'             => $item->kode_negara,
                                                'stasiun_monitor'         => $item->stasiun_monitor,
                                                'frekuensi_khz'           => $item->frekuensi_khz,
                                                'tanggal'                 => $item->tanggal,
                                                'bulan'                   => $item->bulan,
                                                'tahun'                   => $item->tahun,
                                                'jam_mulai'               => $item->jam_mulai,
                                                'jam_akhir'               => $item->jam_akhir,
                                                'kuat_medan_dbuvm'        => (float) $item->kuat_medan_dbuvm,
                                                'identifikasi'            => $item->identifikasi,
                                                'administrasi_termonitor' => $item->administrasi_termonitor,
                                                'kelas_stasiun'           => $item->kelas_stasiun,
                                                'lebar_band'              => $item->lebar_band,
                                                'kelas_emisi'             => $item->kelas_emisi,
                                                'longitude_derajat'       => $item->longitude_derajat,
                                                'longitude_arah'          => $item->longitude_arah,
                                                'longitude_menit'         => $item->longitude_menit,
                                                'latitude_derajat'        => $item->latitude_derajat,
                                                'latitude_arah'           => $item->latitude_arah,
                                                'latitude_menit'          => $item->latitude_menit,
                                                'north_bearing'           => $item->north_bearing,
                                                'akurasi'                 => $item->akurasi,
                                                'tidak_sesuai_rr'         => $item->tidak_sesuai_rr,
                                                'informasi_tambahan'      => $item->informasi_tambahan,
                                            ]) }}"
                                            aria-label="Lihat Detail">
                                            <x-icon icon="lihat" width="16" height="16" />
                                        </button>
                                        <button type="button" class="action-pill edit" title="Edit"
                                            data-bs-toggle="modal" data-bs-target="#editLaporanModal"
                                            data-bs-focus="false"
                                            data-edit-table-number="{{ $rowNumber }}"
                                            data-update-url="{{ route('monitoring.update', $item->id) }}"
                                            data-edit-payload="{{ json_encode([
                                                'id'                               => $item->id,
                                                'kategori'                         => $item->kategori,
                                                'kode_negara'                      => $item->kode_negara,
                                                'stasiun_monitor'                  => $item->stasiun_monitor,
                                                'frekuensi_khz'                    => $item->frekuensi_khz,
                                                'mulai_pengamatan'                 => $item->tahun && $item->bulan && $item->tanggal
                                                    ? sprintf('%04d-%02d-%02dT%s', $item->tahun, $item->bulan, $item->tanggal, str_replace('.', ':', (string) $item->jam_mulai))
                                                    : '',
                                                'selesai_pengamatan_waktu'         => str_replace('.', ':', (string) $item->jam_akhir),
                                                'kuat_medan_dbuvm'                 => $item->kuat_medan_dbuvm !== null ? (float)$item->kuat_medan_dbuvm : '',
                                                'identifikasi'                     => $item->identifikasi,
                                                'administrasi_termonitor'          => $item->administrasi_termonitor,
                                                'kelas_stasiun'                    => $item->kelas_stasiun,
                                                'lebar_band'                       => $item->lebar_band,
                                                'kelas_emisi'                      => $item->kelas_emisi,
                                                'longitude_derajat'                => $item->longitude_derajat,
                                                'longitude_arah'                   => $item->longitude_arah,
                                                'longitude_menit'                  => $item->longitude_menit,
                                                'latitude_derajat'                 => $item->latitude_derajat,
                                                'latitude_arah'                    => $item->latitude_arah,
                                                'latitude_menit'                   => $item->latitude_menit,
                                                'north_bearing'                    => $item->north_bearing,
                                                'akurasi'                          => $item->akurasi,
                                                'tidak_sesuai_rr'                  => $item->tidak_sesuai_rr,
                                                'perkiraan_lokasi_sumber_pancaran' => $item->perkiraan_lokasi_sumber_pancaran,
                                            ]) }}">
                                            <x-icon icon="edit" width="16" height="16" />
                                        </button>
                                        <button type="button" class="action-pill delete" title="Hapus"
                                            data-delete-id="{{ $item->id }}"
                                            data-delete-url="{{ route('monitoring.destroy', $item->id) }}">
                                            <x-icon icon="deletd" width="16" height="16" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="30" class="text-center py-4 text-muted">Belum ada data harian.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    @if ($monitorings->hasPages() || $monitorings->total() > 0)
        <div class="card-footer bg-white border-top border-slate-100 d-flex align-items-center justify-content-between flex-wrap gap-2 px-3 py-2">
            <div style="font-size: 0.72rem; color: #64748b;">
                Menampilkan
                <span class="fw-bold text-slate-800">{{ $monitorings->firstItem() ?? 0 }}</span>
                –
                <span class="fw-bold text-slate-800">{{ $monitorings->lastItem() ?? 0 }}</span>
                dari
                <span class="fw-bold text-slate-800">{{ number_format($monitorings->total()) }}</span>
                data
            </div>
            <div>
                {{ $monitorings->links() }}
            </div>
        </div>
    @endif

    {{-- BULK DELETE FORM --}}
    <form id="bulkDeleteMonitoringForm" action="{{ route('monitoring.bulk-destroy') }}" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
        <div id="bulkDeleteInputs"></div>
    </form>



    {{-- SINGLE DELETE FORM: Satu form hapus untuk semua baris, diisi via JS --}}
    <form id="deleteMonitoringForm" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>
</div>

<script>
(function() {
    /* Store data tidak lagi digunakan, dipindah ke data-payload */

    window.toggleBulkMode = function(active) {
        const table = document.querySelector('.mosfet-table');
        const enterBulkBtn = document.getElementById('enterBulkModeBtn');
        const cancelBulkBtn = document.getElementById('cancelBulkModeBtn');
        const addLaporanBtn = document.getElementById('addLaporanBtn');
        const exportLaporanBtn = document.getElementById('exportLaporanBtn');
        const deleteAllBtn = document.getElementById('deleteAllBtn');
        const bulkBtn = document.getElementById('bulkDeleteBtn');
        const selectAll = document.getElementById('selectAllCheckbox');
        const checkboxes = document.getElementsByClassName('row-checkbox');
        
        if (!table) return;

        if (active) {
            table.classList.add('bulk-mode-active');
            if(enterBulkBtn) enterBulkBtn.classList.add('d-none');
            if(addLaporanBtn) addLaporanBtn.classList.add('d-none');
            if(exportLaporanBtn) exportLaporanBtn.classList.add('d-none');
            if(cancelBulkBtn) cancelBulkBtn.classList.remove('d-none');
            if(deleteAllBtn) deleteAllBtn.classList.add('d-none');
        } else {
            table.classList.remove('bulk-mode-active');
            if(enterBulkBtn) enterBulkBtn.classList.remove('d-none');
            if(addLaporanBtn) addLaporanBtn.classList.remove('d-none');
            if(exportLaporanBtn) exportLaporanBtn.classList.remove('d-none');
            if(cancelBulkBtn) cancelBulkBtn.classList.add('d-none');
            if(bulkBtn) bulkBtn.classList.add('d-none');
            if(deleteAllBtn) deleteAllBtn.classList.remove('d-none');
            
            if (selectAll) selectAll.checked = false;
            if (checkboxes) Array.from(checkboxes).forEach(cb => cb.checked = false);
        }
    };

    window.updateBulkDeleteBtn = function() {
        const checkboxes = document.getElementsByClassName('row-checkbox');
        const bulkBtn = document.getElementById('bulkDeleteBtn');
        const countSpan = document.getElementById('selectedCount');
        const selectAll = document.getElementById('selectAllCheckbox');

        let selected = checkboxes ? Array.from(checkboxes).filter(cb => cb.checked).length : 0;
        if (selected > 0) {
            if(bulkBtn) bulkBtn.classList.remove('d-none');
            if(bulkBtn) bulkBtn.classList.add('d-flex');
            if(countSpan) countSpan.textContent = selected;
        } else {
            if(bulkBtn) bulkBtn.classList.add('d-none');
            if(bulkBtn) bulkBtn.classList.remove('d-flex');
        }
        
        if (selectAll && checkboxes) {
            selectAll.checked = (selected === checkboxes.length && checkboxes.length > 0);
        }
    };

    window.toggleSelectAll = function() {
        const selectAll = document.getElementById('selectAllCheckbox');
        const checkboxes = document.getElementsByClassName('row-checkbox');
        if (!selectAll || !checkboxes) return;
        
        Array.from(checkboxes).forEach(cb => {
            cb.checked = selectAll.checked;
        });
        window.updateBulkDeleteBtn();
    };

    window.confirmBulkDelete = function() {
        const checkboxes = document.getElementsByClassName('row-checkbox');
        let selected = checkboxes ? Array.from(checkboxes).filter(cb => cb.checked) : [];
        if (selected.length === 0) return;
        
        window.confirmSistem('Hapus Terpilih', 'Hapus ' + selected.length + ' data yang dipilih?', function() {
            const form = document.getElementById('bulkDeleteMonitoringForm');
            const inputsDiv = document.getElementById('bulkDeleteInputs');
            if(!form || !inputsDiv) return;

            inputsDiv.innerHTML = '';
            selected.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                inputsDiv.appendChild(input);
            });
            form.submit();
        });
    };

    window.confirmDeleteAll = function() {
        window.confirmSistem('PERINGATAN KRITIS', 'Anda akan menghapus SELURUH data (Total sesuai filter saat ini), bukan hanya yang tercentang di layar. Tindakan ini tidak dapat dibatalkan! Lanjutkan?', function() {
            window.confirmSistem('Konfirmasi Terakhir', 'Hapus SEMUA data di filter ini sekarang?', function() {
                const form = document.getElementById('deleteAllMonitoringForm');
                if(form) form.submit();
            });
        });
    };
})();
</script>