<?php

namespace App\Exports;

use App\Models\MonitoringLog;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MonitoringTypeExport implements FromCollection, WithHeadings, WithMapping, WithEvents, ShouldAutoSize, WithTitle
{
    public function __construct(
        private readonly string $type,
        private readonly ?string $sourceFile = null,
    )
    {
    }

    public function collection(): Collection
    {
        $query = MonitoringLog::query()
            ->where('is_archived', false)
            ->where('monitoring_type', $this->type)
            ->orderBy('bulan')
            ->orderBy('tanggal')
            ->orderBy('frekuensi_khz');

        if ($this->sourceFile !== null && $this->sourceFile !== '') {
            $query->where('source_file', $this->sourceFile);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            [
                'Kode Negara',
                'Stasiun Monitor',
                'Frekuensi (KHz)',
                'Waktu Pengamatan',
                '',
                '',
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
                '',
                'North Bearing',
                'Akurasi',
                'Tidak Sesuai RR',
                'Informasi Tambahan',
            ],
            [
                '',
                '',
                '',
                'Tanggal',
                'Bulan',
                'Mulai',
                'Akhir',
                '',
                '',
                '',
                '',
                '',
                '',
                'Long (0-180)',
                'E atau W',
                'Long (0-59)',
                'Lat (0-90)',
                'N atau S',
                'Lat (0-59)',
                'Catatan Lokasi',
                '',
                '',
                '',
                '',
            ],
        ];
    }

    public function map($row): array
    {
        return [
            $row->kode_negara,
            $row->stasiun_monitor,
            $row->frekuensi_khz,
            $row->tanggal,
            $row->bulan,
            $row->jam_mulai,
            $row->jam_akhir,
            $row->kuat_medan_dbuvm,
            $row->identifikasi,
            $row->administrasi_termonitor,
            $row->kelas_stasiun,
            $row->lebar_band,
            $row->kelas_emisi,
            $row->perkiraan_lokasi_sumber_pancaran,
            $row->longitude_derajat,
            $row->longitude_arah,
            $row->longitude_menit,
            $row->latitude_derajat,
            $row->latitude_arah,
            $row->latitude_menit,
            $row->north_bearing,
            $row->akurasi,
            $row->tidak_sesuai_rr,
            $row->informasi_tambahan,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();

                $sheet->insertNewRowBefore(1, 4);

                $sheet->setCellValue('A1', 'BALAI MONITOR SPEKTRUM FREKUENSI RADIO KELAS II LAMPUNG');
                $sheet->setCellValue('A2', 'DIREKTORAT JENDERAL INFRASTRUKTUR DIGITAL - KEMENTERIAN KOMUNIKASI DAN DIGITAL');
                $sheet->setCellValue('A3', 'LAPORAN HASIL MONITORING ' . strtoupper($this->title()));
                $sheet->setCellValue('A4', 'Tanggal Cetak: ' . now()->format('d-m-Y H:i') . ' WIB');

                $sheet->mergeCells('A1:X1');
                $sheet->mergeCells('A2:X2');
                $sheet->mergeCells('A3:X3');
                $sheet->mergeCells('A4:X4');

                $sheet->getStyle('A1:A3')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FF083B66'],
                        'size' => 11,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('A4')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FF374151'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                    ],
                ]);

                $sheet->mergeCells('A5:A6');
                $sheet->mergeCells('B5:B6');
                $sheet->mergeCells('C5:C6');
                $sheet->mergeCells('D5:G5');
                $sheet->mergeCells('H5:H6');
                $sheet->mergeCells('I5:I6');
                $sheet->mergeCells('J5:J6');
                $sheet->mergeCells('K5:K6');
                $sheet->mergeCells('L5:L6');
                $sheet->mergeCells('M5:M6');
                $sheet->mergeCells('N5:T5');
                $sheet->mergeCells('U5:U6');
                $sheet->mergeCells('V5:V6');
                $sheet->mergeCells('W5:W6');
                $sheet->mergeCells('X5:X6');

                $sheet->getStyle('A5:X6')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FFFFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF083B66'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFCCE5FF'],
                        ],
                    ],
                ]);

                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A7:X{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFD1D5DB'],
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_TOP,
                    ],
                ]);

                $sheet->freezePane('A7');
                $sheet->getRowDimension(5)->setRowHeight(24);
                $sheet->getRowDimension(6)->setRowHeight(24);
            },
        ];
    }

    public function title(): string
    {
        return match ($this->type) {
            'nelayan' => 'HF Nelayan',
            'rutin' => 'HF Rutin',
            'mf' => 'MF',
            default => 'Perlu Verifikasi',
        };
    }
}
