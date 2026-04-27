<?php

namespace App\Services;

use App\Models\Monitoring;
use App\Models\MonitoringLog;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MonitoringLogImportService
{
    private array $rangeGroups = [
        'mf' => [
            [300.0, 3000.0],
        ],
        'nelayan' => [
            [4000.0, 4063.0],
            [8100.0, 8195.0],
            [5228.0, 5238.0],
            [6770.0, 6784.5],
            [7573.0, 7587.0],
            [8000.0, 8010.0],
            [10152.0, 10166.5],
            [11002.0, 11012.0],
            [13870.0, 13884.5],
            [14361.5, 14371.5],
        ],
    ];

    public function import(string $filePath, string $originalName, string $classificationMode = 'accurate'): int
    {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getSheet(0);

        $highestRow = $worksheet->getHighestDataRow('A');
        $inserted = 0;

        for ($row = 8; $row <= $highestRow; $row++) {
            $record = [
                'source_file' => $originalName,
                'sheet_name' => $worksheet->getTitle(),
                'source_row' => $row,
                'monitoring_type' => 'other',
                'is_forced_classification' => false,
                'classification_rule' => 'range',
                'is_archived' => false,
                'archived_at' => null,
                'kode_negara' => $this->clean($worksheet->getCell("A{$row}")->getCalculatedValue()),
                'stasiun_monitor' => $this->clean($worksheet->getCell("B{$row}")->getCalculatedValue()),
                'frekuensi_khz' => $this->toDecimal($worksheet->getCell("C{$row}")->getCalculatedValue()),
                'tanggal' => $this->toInt($worksheet->getCell("D{$row}")->getCalculatedValue()),
                'bulan' => $this->toInt($worksheet->getCell("E{$row}")->getCalculatedValue()),
                'jam_mulai' => $this->clean($worksheet->getCell("F{$row}")->getCalculatedValue()),
                'jam_akhir' => $this->clean($worksheet->getCell("G{$row}")->getCalculatedValue()),
                'kuat_medan_dbuvm' => $this->toDecimal($worksheet->getCell("H{$row}")->getCalculatedValue()),
                'identifikasi' => $this->clean($worksheet->getCell("I{$row}")->getCalculatedValue()),
                'administrasi_termonitor' => $this->clean($worksheet->getCell("J{$row}")->getCalculatedValue()),
                'kelas_stasiun' => $this->clean($worksheet->getCell("K{$row}")->getCalculatedValue()),
                'lebar_band' => $this->clean($worksheet->getCell("L{$row}")->getCalculatedValue()),
                'kelas_emisi' => $this->clean($worksheet->getCell("M{$row}")->getCalculatedValue()),
                'perkiraan_lokasi_sumber_pancaran' => $this->clean($worksheet->getCell("N{$row}")->getCalculatedValue()),
                'longitude_derajat' => $this->clean($worksheet->getCell("N{$row}")->getCalculatedValue()),
                'longitude_arah' => $this->clean($worksheet->getCell("O{$row}")->getCalculatedValue()),
                'longitude_menit' => $this->clean($worksheet->getCell("P{$row}")->getCalculatedValue()),
                'latitude_derajat' => $this->clean($worksheet->getCell("Q{$row}")->getCalculatedValue()),
                'latitude_arah' => $this->clean($worksheet->getCell("R{$row}")->getCalculatedValue()),
                'latitude_menit' => $this->clean($worksheet->getCell("S{$row}")->getCalculatedValue()),
                'north_bearing' => $this->clean($worksheet->getCell("T{$row}")->getCalculatedValue()),
                'akurasi' => $this->clean($worksheet->getCell("U{$row}")->getCalculatedValue()),
                'tidak_sesuai_rr' => $this->clean($worksheet->getCell("V{$row}")->getCalculatedValue()),
                'informasi_tambahan' => $this->clean($worksheet->getCell("W{$row}")->getCalculatedValue()),
            ];

            if ($this->isEmptyRecord($record)) {
                continue;
            }

            $resolution = $this->resolveMonitoringType($record['frekuensi_khz'], $classificationMode);
            $record['monitoring_type'] = $resolution['type'];
            $record['is_forced_classification'] = $resolution['forced'];
            $record['classification_rule'] = $resolution['rule'];

            $model = MonitoringLog::updateOrCreate(
                [
                    'source_file' => $record['source_file'],
                    'sheet_name' => $record['sheet_name'],
                    'source_row' => $record['source_row'],
                ],
                $record,
            );

            // Automatically promote classified import rows into final monitorings
            // and store back-reference for end-to-end traceability.
            $this->syncFinalMonitoring($model);

            if ($model->wasRecentlyCreated) {
                $inserted++;
            }
        }

        return $inserted;
    }

    public function backfillMonitoringLinks(?string $sourceFile = null): int
    {
        $baseQuery = MonitoringLog::query()
            ->where('monitoring_type', '!=', 'other')
            ->when($sourceFile, fn ($query) => $query->where('source_file', $sourceFile));

        $beforeLinked = (clone $baseQuery)
            ->whereNotNull('monitoring_id')
            ->count();

        (clone $baseQuery)
            ->orderBy('id')
            ->chunkById(200, function ($logs) {
                foreach ($logs as $log) {
                    $this->syncFinalMonitoring($log);
                }
            });

        $afterLinked = (clone $baseQuery)
            ->whereNotNull('monitoring_id')
            ->count();

        return max(0, $afterLinked - $beforeLinked);
    }

    private function clean(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim((string) $value);

        if ($normalized === '' || $normalized === '-') {
            return null;
        }

        return $normalized;
    }

    private function toInt(mixed $value): ?int
    {
        $normalized = $this->clean($value);

        if ($normalized === null || !is_numeric($normalized)) {
            return null;
        }

        return (int) $normalized;
    }

    private function toDecimal(mixed $value): ?float
    {
        $normalized = $this->clean($value);

        if ($normalized === null || !is_numeric($normalized)) {
            return null;
        }

        return (float) $normalized;
    }

    private function isEmptyRecord(array $record): bool
    {
        return $record['kode_negara'] === null
            && $record['stasiun_monitor'] === null
            && $record['frekuensi_khz'] === null
            && $record['identifikasi'] === null
            && $record['informasi_tambahan'] === null;
    }

    private function resolveMonitoringType(?float $frequencyKhz, string $classificationMode): array
    {
        if ($frequencyKhz === null) {
            if ($classificationMode === 'force') {
                return [
                    'type' => 'mf',
                    'forced' => true,
                    'rule' => 'default-missing-frequency',
                ];
            }

            return [
                'type' => 'other',
                'forced' => false,
                'rule' => 'missing-frequency',
            ];
        }

        foreach ($this->rangeGroups as $type => $ranges) {
            if ($this->inAnyRange($frequencyKhz, $ranges)) {
                return [
                    'type' => $type,
                    'forced' => false,
                    'rule' => 'range',
                ];
            }
        }

        // HF rutin mencakup pita 3-30 MHz (3000-30000 kHz) di luar alokasi khusus nelayan.
        if ($frequencyKhz >= 3000.0 && $frequencyKhz <= 30000.0) {
            return [
                'type' => 'rutin',
                'forced' => false,
                'rule' => 'hf-rutin-range',
            ];
        }

        if ($classificationMode === 'force') {
            $nearest = $this->resolveNearestType($frequencyKhz);

            return [
                'type' => $nearest,
                'forced' => true,
                'rule' => 'nearest-range',
            ];
        }

        return [
            'type' => 'other',
            'forced' => false,
            'rule' => 'out-of-range',
        ];
    }

    private function inAnyRange(float $value, array $ranges): bool
    {
        foreach ($ranges as [$min, $max]) {
            if ($value >= $min && $value <= $max) {
                return true;
            }
        }

        return false;
    }

    private function resolveNearestType(float $value): string
    {
        $nearestType = 'mf';
        $nearestDistance = PHP_FLOAT_MAX;

        foreach ($this->rangeGroups as $type => $ranges) {
            foreach ($ranges as [$min, $max]) {
                $distance = 0.0;

                if ($value < $min) {
                    $distance = $min - $value;
                } elseif ($value > $max) {
                    $distance = $value - $max;
                }

                if ($distance < $nearestDistance) {
                    $nearestDistance = $distance;
                    $nearestType = $type;
                }
            }
        }

        return $nearestType;
    }

    private function syncFinalMonitoring(MonitoringLog $log): void
    {
        $kategori = $this->mapMonitoringTypeToKategori($log->monitoring_type);

        // Rows that still need manual verification should not be promoted.
        if ($kategori === null) {
            if ($log->monitoring_id !== null) {
                $log->monitoring_id = null;
                $log->save();
            }

            return;
        }

        $tahun = (int) ($log->created_at?->year ?? now()->year);

        $lookup = [
            'kategori' => $kategori,
            'kode_negara' => $log->kode_negara,
            'stasiun_monitor' => $log->stasiun_monitor,
            'frekuensi_khz' => $log->frekuensi_khz,
            'tanggal' => $log->tanggal,
            'bulan' => $log->bulan,
            'tahun' => $tahun,
            'jam_mulai' => $log->jam_mulai,
            'identifikasi' => $log->identifikasi,
        ];

        $payload = [
            'kategori' => $kategori,
            'kode_negara' => $log->kode_negara,
            'stasiun_monitor' => $log->stasiun_monitor,
            'frekuensi_khz' => $log->frekuensi_khz,
            'tanggal' => $log->tanggal,
            'bulan' => $log->bulan,
            'tahun' => $tahun,
            'jam_mulai' => $log->jam_mulai,
            'jam_akhir' => $log->jam_akhir,
            'kuat_medan_dbuvm' => $log->kuat_medan_dbuvm,
            'identifikasi' => $log->identifikasi,
            'administrasi_termonitor' => $log->administrasi_termonitor,
            'kelas_stasiun' => $log->kelas_stasiun,
            'lebar_band' => $log->lebar_band,
            'kelas_emisi' => $log->kelas_emisi,
            'perkiraan_lokasi_sumber_pancaran' => $log->perkiraan_lokasi_sumber_pancaran,
            'longitude_derajat' => $log->longitude_derajat,
            'longitude_arah' => $log->longitude_arah,
            'longitude_menit' => $log->longitude_menit,
            'latitude_derajat' => $log->latitude_derajat,
            'latitude_arah' => $log->latitude_arah,
            'latitude_menit' => $log->latitude_menit,
            'north_bearing' => $log->north_bearing,
            'akurasi' => $log->akurasi,
            'tidak_sesuai_rr' => $log->tidak_sesuai_rr,
            'informasi_tambahan' => $log->informasi_tambahan,
        ];

        $monitoring = Monitoring::updateOrCreate($lookup, $payload);

        if ((int) ($log->monitoring_id ?? 0) !== (int) $monitoring->id) {
            $log->monitoring_id = $monitoring->id;
            $log->save();
        }
    }

    private function mapMonitoringTypeToKategori(?string $monitoringType): ?string
    {
        return match ($monitoringType) {
            'mf' => 'MF',
            'rutin' => 'HF Rutin',
            'nelayan' => 'HF Nelayan',
            default => null,
        };
    }
}
