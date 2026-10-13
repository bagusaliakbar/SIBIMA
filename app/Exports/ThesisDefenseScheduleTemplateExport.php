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

class ThesisDefenseScheduleTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
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
        $sampleDate = Carbon::now()->addDays(5)->format('Y-m-d');

        return [
            [
                'Sidang Skripsi Sesi 1 - Ruang Utama',
                $sampleDate,
                'Ruang Sidang Utama (R. 201)',
                'https://meet.google.com/xyz-sidang-skripsi',
                '0410019202',
                '0412345602',
                '08:00',
                '09:00',
                '211011001',
                '',
                '0412345603',
                '0412345604',
            ],
            [
                'Sidang Skripsi Sesi 1 - Ruang Utama',
                $sampleDate,
                'Ruang Sidang Utama (R. 201)',
                'https://meet.google.com/xyz-sidang-skripsi',
                '0410019202',
                '0412345602',
                '09:00',
                '10:00',
                '211011002',
                '',
                '0412345603',
                '0412345604',
            ],
            [
                'Sidang Skripsi Sesi 1 - Ruang Utama',
                $sampleDate,
                'Ruang Sidang Utama (R. 201)',
                'https://meet.google.com/xyz-sidang-skripsi',
                '0410019202',
                '0412345602',
                '10:00',
                '10:30',
                '',
                'Rapat Pleno & Yudisium Kelulusan',
                '',
                '',
            ],
            [
                'Sidang Skripsi Sesi 2 - Ruang Smart Class',
                $sampleDate,
                'Ruang Smart Class (R. 202)',
                '',
                '0412345605',
                '0412345606',
                '08:00',
                '09:00',
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
