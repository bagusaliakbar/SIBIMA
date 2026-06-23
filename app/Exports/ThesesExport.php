<?php

namespace App\Exports;

use App\Models\Thesis;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class ThesesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $search;
    protected $status;

    public function __construct($search = null, $status = 'all')
    {
        $this->search = $search;
        $this->status = $status;
    }

    public function collection()
    {
        $user = Auth::user();
        $query = Thesis::with(['student', 'pembimbing1', 'pembimbing2']);

        if ($user->role === 'dosen') {
            $query->where(function($q) use ($user) {
                $q->where('pembimbing1_id', $user->id)
                  ->orWhere('pembimbing2_id', $user->id);
            });
        }

        if ($this->status && $this->status !== 'all') {
            $query->where('status', $this->status);
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->whereHas('student', function($sq) {
                    $sq->where('name', 'like', '%' . $this->search . '%')
                       ->orWhere('identifier', 'like', '%' . $this->search . '%');
                })
                ->orWhere('title', 'like', '%' . $this->search . '%')
                ->orWhere('final_title', 'like', '%' . $this->search . '%');
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Nama Mahasiswa',
            'NPM',
            'Rencana Judul',
            'Judul Final',
            'Deskripsi',
            'Status',
            'Pembimbing 1',
            'Pembimbing 2',
            'Tanggal Pengajuan',
        ];
    }

    public function map($thesis): array
    {
        return [
            $thesis->student->name,
            $thesis->student->identifier,
            $thesis->title,
            $thesis->final_title ?? '-',
            $thesis->abstract ?? '-',
            ucfirst($thesis->status),
            $thesis->pembimbing1->name ?? '-',
            $thesis->pembimbing2->name ?? '-',
            $thesis->created_at->format('d/m/Y'),
        ];
    }
}
