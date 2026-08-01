<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RepositoryTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'NPM',
            'Nama Mahasiswa',
            'Angkatan',
            'Judul Skripsi',
            'Abstrak',
            'Pembimbing 1',
            'Pembimbing 2'
        ];
    }

    public function array(): array
    {
        return [
            [
                '14101001',
                'Budi Santoso',
                '2019',
                'Sistem Pendukung Keputusan Pemilihan Karyawan Terbaik',
                'Abstrak singkat di sini...',
                'Dr. Ir. Pembimbing Satu',
                'Bpk. Pembimbing Dua, M.Kom'
            ]
        ];
    }
}
