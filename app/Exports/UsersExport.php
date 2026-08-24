<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    protected ?string $search;
    protected ?string $status;
    protected ?string $role;
    protected ?string $cohortFilter;
    protected ?string $entryYear;

    public function __construct(
        ?string $search = null,
        ?string $status = 'all',
        ?string $role = 'all',
        ?string $cohortFilter = 'all',
        ?string $entryYear = null
    ) {
        $this->search = $search;
        $this->status = $status ?? 'all';
        $this->role = $role ?? 'all';
        $this->cohortFilter = $cohortFilter ?? 'all';
        $this->entryYear = $entryYear;
    }

    public function collection()
    {
        $currentYear = now()->year;
        $isSecondHalf = now()->month >= 9;
        $oldCohortThresholdYear = $isSecondHalf ? ($currentYear - 4) : ($currentYear - 5);

        $query = User::whereIn('role', ['dosen', 'mahasiswa', 'kaprodi']);

        if (!empty($this->role) && $this->role !== 'all') {
            $query->where('role', $this->role);
        }

        if (!empty($this->status) && $this->status !== 'all') {
            if ($this->status === 'active') {
                $query->where('is_active', true);
            } elseif ($this->status === 'pending') {
                $query->where('is_active', false);
            }
        }

        if ($this->cohortFilter === 'new') {
            $query->where(function ($q) use ($oldCohortThresholdYear) {
                $q->where('role', '!=', 'mahasiswa')
                  ->orWhere('entry_year', '>', $oldCohortThresholdYear)
                  ->orWhereNull('entry_year');
            });
        } elseif ($this->cohortFilter === 'old') {
            $query->where('role', 'mahasiswa')
                  ->where('entry_year', '<=', $oldCohortThresholdYear);
        }

        if (!empty($this->entryYear) && $this->entryYear !== 'all') {
            $query->where('entry_year', $this->entryYear);
        }

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('identifier', 'like', "%{$this->search}%")
                  ->orWhere('phone_number', 'like', "%{$this->search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
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
