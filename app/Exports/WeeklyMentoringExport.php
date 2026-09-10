<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WeeklyMentoringExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected Collection $collection;
    protected Carbon $startDate;
    protected Carbon $endDate;
    private int $rowNumber = 0;

    public function __construct(Collection $collection, Carbon $startDate, Carbon $endDate)
    {
        $this->collection = $collection;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return $this->collection;
    }

    public function headings(): array
    {
        return [
            'NO',
            'NAMA MAHASISWA',
            'NPM',
            'ANGKATAN',
            'PEMBIMBING 1',
            'SESI P1 MINGGU INI',
            'PEMBIMBING 2',
            'SESI P2 MINGGU INI',
            'TOTAL SESI MINGGU INI',
            'STATUS KEPATUHAN',
            'SESI TERAKHIR P1',
            'SESI TERAKHIR P2',
        ];
    }

    public function map($thesis): array
    {
        $this->rowNumber++;

        $student = $thesis->student;
        $p1 = $thesis->pembimbing1;
        $p2 = $thesis->pembimbing2;

        $p1Count = $thesis->weekly_p1_count ?? 0;
        $p2Count = $thesis->weekly_p2_count ?? 0;
        $totalCount = $p1Count + $p2Count;

        $latestP1 = $thesis->weekly_latest_p1 ? Carbon::parse($thesis->weekly_latest_p1->scheduled_at)->format('d/m/Y H:i') : '-';
        $latestP2 = $thesis->weekly_latest_p2 ? Carbon::parse($thesis->weekly_latest_p2->scheduled_at)->format('d/m/Y H:i') : '-';

        return [
            $this->rowNumber,
            $student ? $student->name : '-',
            $student ? $student->identifier : '-',
            $student ? ($student->entry_year ?? '-') : '-',
            $p1 ? $p1->name : '-',
            $p1Count . 'x',
            $p2 ? $p2->name : ($thesis->pembimbing2_id ? '-' : 'Belum Ditugaskan'),
            $thesis->pembimbing2_id ? ($p2Count . 'x') : 'N/A',
            $totalCount . 'x',
            $thesis->weekly_compliance_label ?? 'Belum Bimbingan',
            $latestP1,
            $latestP2,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestCol = $sheet->getHighestColumn();

        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1E40AF'], // Primary Blue
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
            "A1:{$highestCol}{$highestRow}" => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FFCBD5E1'],
                    ],
                ],
            ],
        ];
    }
}
