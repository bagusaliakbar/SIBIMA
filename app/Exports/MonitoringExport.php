<?php

namespace App\Exports;

use App\Models\Thesis;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MonitoringExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return Thesis::with(['student', 'pembimbing1', 'pembimbing2'])
            ->where(function($query) {
                $query->where(function($q) {
                    $q->where('acc_up_p1', true)->where('acc_up_p2', true);
                })
                ->orWhere(function($q) {
                    $q->where('acc_sidang_p1', true)->where('acc_sidang_p2', true);
                });
            })
            ->get();
    }

    public function headings(): array
    {
        return [
            'NO',
            'NAMA MAHASISWA',
            'NPM',
            'JUDUL SKRIPSI',
            'PEMBIMBING 1',
            'PEMBIMBING 2'
        ];
    }

    private $rowNumber = 0;

    public function map($thesis): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber,
            $thesis->student->name,
            $thesis->student->identifier,
            $thesis->final_title ?? $thesis->title,
            $thesis->pembimbing1->name ?? '-',
            $thesis->pembimbing2->name ?? '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0']
                ]
            ],
        ];
    }
}
