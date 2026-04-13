<?php

namespace App\Services;

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

            if ($model->wasRecentlyCreated) {
                $inserted++;
            }
        }

        return $inserted;
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
}
