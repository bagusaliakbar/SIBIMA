<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SeminarScheduleTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public function headings(): array
    {
        return [
            'Nama Sesi / Judul Jadwal',
            'Tanggal (YYYY-MM-DD)',
            'Lokasi / Ruangan',
            'Tautan Online (Opsional)',
            'Ketua Sidang (NIDN/Nama)',
            'Moderator (NIDN/Nama)',
            'Jam Mulai (HH:mm)',
            'Jam Selesai (HH:mm)',
            'NPM Mahasiswa',
            'Kegiatan Khusus (Opsional jika bukan mahasiswa)',
            'Penguji 1 (NIDN/Nama)',
            'Penguji 2 (NIDN/Nama)',
        ];
    }

    public function array(): array
    {
        $sampleDate = Carbon::now()->addDays(3)->format('Y-m-d');

        return [
            [
                'Seminar Proposal Sesi 1 - Ruang 201',
                $sampleDate,
                'Ruang Sidang 201',
                'https://meet.google.com/abc-defg-hij',
                '0410019202',
                '0412345602',
                '08:00',
                '08:45',
                '211011001',
                '',
                '0412345603',
                '0412345604',
            ],
            [
                'Seminar Proposal Sesi 1 - Ruang 201',
                $sampleDate,
                'Ruang Sidang 201',
                'https://meet.google.com/abc-defg-hij',
                '0410019202',
                '0412345602',
                '08:45',
                '09:30',
                '211011002',
                '',
                '0412345603',
                '0412345604',
            ],
            [
                'Seminar Proposal Sesi 1 - Ruang 201',
                $sampleDate,
                'Ruang Sidang 201',
                'https://meet.google.com/abc-defg-hij',
                '0410019202',
                '0412345602',
                '09:30',
                '10:00',
                '',
                'ISHOMA & Coffee Break',
                '',
                '',
            ],
            [
                'Seminar Proposal Sesi 2 - Ruang 202',
                $sampleDate,
                'Ruang Smart Class 202',
                '',
                '0412345605',
                '0412345606',
                '08:00',
                '08:45',
                '211011003',
                '',
                '0412345607',
                '0412345608',
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFF97316'], // Orange brand color
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
