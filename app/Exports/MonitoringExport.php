<?php

namespace App\Exports;

use App\Models\Thesis;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MonitoringExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $type;
    protected $startDate;
    protected $endDate;

    public function __construct($type = 'akademik', $startDate = null, $endDate = null)
    {
        $this->type = $type;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        if ($this->type === 'dosen') {
            return User::where('role', 'dosen')->withCount(['thesesAsP1', 'thesesAsP2'])->get();
        }

        if ($this->type === 'logs') {
            return \App\Models\ActivityLog::with('user')
                ->when($this->startDate && $this->endDate, function($q) {
                    $q->whereBetween('created_at', [$this->startDate, $this->endDate]);
                })->latest()->get();
        }

        $query = Thesis::with(['student', 'pembimbing1', 'pembimbing2']);

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
        }

        if ($this->type === 'kelulusan') {
            $query->where('status', 'completed');
        }

        return $query->get();
    }

    public function headings(): array
    {
        switch ($this->type) {
            case 'dosen':
                return ['NO', 'NAMA DOSEN', 'NIP/NIDN', 'BIMBINGAN P1', 'BIMBINGAN P2', 'TOTAL BEBAN'];
            case 'logs':
                return ['NO', 'WAKTU', 'PENGGUNA', 'AKSI', 'DESKRIPSI'];
            case 'kelulusan':
                return ['NO', 'NAMA MAHASISWA', 'NPM', 'JUDUL AKHIR', 'TANGGAL LULUS', 'NILAI'];
            case 'waktu':
                return ['NO', 'NAMA MAHASISWA', 'NPM', 'GELOMBANG', 'DURASI (BULAN)'];
            case 'mahasiswa':
                return ['NO', 'NAMA MAHASISWA', 'NPM', 'TOTAL BIMBINGAN', 'LOGBOOK', 'STATUS'];
            default:
                return ['NO', 'NAMA MAHASISWA', 'NPM', 'JUDUL SKRIPSI', 'PEMBIMBING 1', 'PEMBIMBING 2', 'STATUS'];
        }
    }

    private $rowNumber = 0;

    public function map($item): array
    {
        $this->rowNumber++;
        switch ($this->type) {
            case 'mahasiswa':
                return [
                    $this->rowNumber,
                    $item->student->name,
                    $item->student->identifier,
                    $item->mentoring_sessions_count ?? \App\Models\MentoringSession::where('thesis_id', $item->id)->where('status', 'completed')->where('is_absent', false)->count(),
                    $item->logbooks_count ?? \App\Models\Logbook::where('thesis_id', $item->id)->count(),
                    strtoupper($item->status)
                ];
            case 'dosen':
                return [
                    $this->rowNumber,
                    $item->name,
                    $item->identifier,
                    $item->theses_as_p1_count,
                    $item->theses_as_p2_count,
                    $item->theses_as_p1_count + $item->theses_as_p2_count
                ];
            case 'logs':
                return [
                    $this->rowNumber,
                    $item->created_at->format('d/m/Y H:i'),
                    $item->user->name ?? 'System',
                    strtoupper($item->activity),
                    $item->description
                ];
            case 'kelulusan':
                return [
                    $this->rowNumber,
                    $item->student->name,
                    $item->student->identifier,
                    $item->final_title ?? $item->title,
                    $item->updated_at->format('d/m/Y'),
                    'A'
                ];
            case 'waktu':
                return [
                    $this->rowNumber,
                    $item->student->name,
                    $item->student->identifier,
                    $item->seminarApplication->wave->name ?? '-',
                    6
                ];
            default:
                return [
                    $this->rowNumber,
                    $item->student->name,
                    $item->student->identifier,
                    $item->final_title ?? $item->title,
                    $item->pembimbing1->name ?? '-',
                    $item->pembimbing2->name ?? '-',
                    strtoupper($item->status)
                ];
        }
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
