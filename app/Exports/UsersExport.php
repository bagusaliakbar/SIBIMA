<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return User::whereIn('role', ['dosen', 'mahasiswa'])->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Email',
            'Peran (dosen/mahasiswa)',
            'NPM/NIDN',
            'Tahun Angkatan',
            'No. Telepon',
            'Status Aktif (1=Aktif, 0=Pending)',
        ];
    }

    public function map($user): array
    {
        return [
            $user->name,
            $user->email,
            $user->role,
            $user->identifier,
            $user->entry_year,
            $user->phone_number,
            $user->is_active ? 1 : 0,
        ];
    }
}
