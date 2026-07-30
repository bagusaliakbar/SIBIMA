<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MigrationTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'NIM', 
            'Judul', 
            'Abstrak', 
            'NIDN_Pembimbing_1', 
            'NIDN_Pembimbing_2', 
            'Tahapan_Saat_Ini'
        ];
    }

    public function array(): array
    {
        return [
            [
                '1012345', 
                'Contoh Judul Skripsi', 
                'Deskripsi singkat...', 
                '0011223344', 
                '0022334455', 
                'Bimbingan Skripsi'
            ]
        ];
    }
}
