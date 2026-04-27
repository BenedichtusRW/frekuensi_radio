<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MonitoringLogbookExport implements FromArray, ShouldAutoSize, WithEvents
{
    public function __construct(private readonly Collection $rows)
    {
    }

    public function array(): array
    {
        return array_merge($this->templateHeaderRows(), $this->dataRows());
    }

    private function templateHeaderRows(): array
    {
        return [
            array_fill(0, 23, ''),
            array_fill(0, 23, ''),
            ['Monitoring Center', '', 'Keterangan dari stasiun yang dimonitor', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''],
            [
                'Kode Negara',
                'Stasiun Monitor',
                'Frekuensi (KHz)',
                'Waktu Pengamatan',
                '',
                'Jam Pengamatan',
                '',
                'Kuat Medan (dBµV/m)',
                'Identifikasi',
                'Administrasi Termonitor',
                'Kelas Stasiun',
                'Lebar Band',
                'Kelas Emisi',
                'Perkiraan Lokasi Sumber Pancaran',
                '',
                '',
                '',
                '',
                '',
                'North Bearing',
                'Akurasi',
                'Tidak sesuai RR',
                'Informasi Tambahan',
            ],
            [
                '', '', '', 'Tanggal', 'Bulan', 'Mulai', 'Akhir', '', '', '', '', '', '',
                'Long (0-180)', 'E atau W', 'Long (0-59)', 'Lat (0-90)', 'N atau S', 'Lat (0-59)',
                '', '', '', '',
            ],
            array_fill(0, 23, ''),
            ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23'],
        ];
    }

    private function dataRows(): array
    {
        $f = function($value) {
            $trimmed = trim((string)$value);
            return ($trimmed === '') ? '-' : $value;
        };

        return $this->rows
            ->map(function ($item) use ($f) {
                return [
                    $f($item->kode_negara),
                    $f($item->stasiun_monitor),
                    $item->frekuensi_khz !== null ? number_format((float) $item->frekuensi_khz, 3, '.', '') : '-',
                    $f($item->tanggal),
                    $f($item->bulan),
                    $f($item->jam_mulai),
                    $f($item->jam_akhir),
                    $item->kuat_medan_dbuvm !== null ? (float)$item->kuat_medan_dbuvm : '-',
                    $f($item->identifikasi),
                    $f($item->administrasi_termonitor),
                    $f($item->kelas_stasiun),
                    $f($item->lebar_band),
                    $f($item->kelas_emisi),
                    $f($item->longitude_derajat),
                    $f($item->longitude_arah),
                    $f($item->longitude_menit),
                    $f($item->latitude_derajat),
                    $f($item->latitude_arah),
                    $f($item->latitude_menit),
                    $f($item->north_bearing),
                    $f($item->akurasi),
                    $f($item->tidak_sesuai_rr),
                    $f($item->informasi_tambahan),
                ];
            })
            ->values()
            ->all();
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();
                $sheet->setTitle('Monitoring Harian');
                $lastRow = $sheet->getHighestRow();
                $lastCol = 'W';

                foreach ($this->mergeRanges() as $range) {
                    $sheet->mergeCells($range);
                }

                $sheet->freezePane('A8');
                $sheet->getStyle("A3:{$lastCol}7")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FF000000'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFFFFFFF'],
                    ],
                ]);

                $sheet->getStyle("A3:{$lastCol}{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                if ($lastRow >= 8) {
                    $sheet->getStyle("A8:{$lastCol}{$lastRow}")->applyFromArray([
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);

                    $sheet->getStyle("B8:B{$lastRow}")->applyFromArray([
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_LEFT,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);

                    $sheet->getStyle("I8:I{$lastRow}")->applyFromArray([
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_LEFT,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                }

                $previousDateKey = null;
                $previousExcelRow = null;
                foreach ($this->rows as $index => $item) {
                    $excelRow = $index + 8;
                    $currentDateKey = sprintf('%04d-%02d-%02d', (int) ($item->tahun ?? 0), (int) ($item->bulan ?? 0), (int) ($item->tanggal ?? 0));

                    if ($previousDateKey !== null && $currentDateKey !== $previousDateKey && $previousExcelRow !== null) {
                        $sheet->getStyle("A{$previousExcelRow}:{$lastCol}{$previousExcelRow}")->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['argb' => 'FFFFFF00'],
                            ],
                            'font' => [
                                'bold' => false,
                            ],
                        ]);
                    }

                    $previousDateKey = $currentDateKey;
                    $previousExcelRow = $excelRow;
                }

                $sheet->getStyle("A7:{$lastCol}7")->applyFromArray([
                    'font' => [
                        'bold' => false,
                        'color' => ['argb' => 'FF000000'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFFDE9D9'],
                    ],
                ]);
            },
        ];
    }

    private function mergeRanges(): array
    {
        return [
            'A2:W2',
            'A3:B3',
            'C3:W3',
            'A4:A6',
            'B4:B6',
            'C4:C6',
            'D5:D6',
            'E5:E6',
            'F4:G4',
            'F5:F6',
            'G5:G6',
            'H4:H6',
            'I4:I6',
            'J4:J6',
            'K4:K6',
            'L4:L6',
            'M4:M6',
            'N4:S4',
            'N5:N6',
            'O5:O6',
            'P5:P6',
            'Q5:Q6',
            'R5:R6',
            'S5:S6',
            'T4:T6',
            'U4:U6',
            'V4:V6',
            'W4:W6',
        ];
    }
}
