<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UncompletedMentoringExport implements WithMultipleSheets
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        return [
            new UncompletedLecturersSheet($this->data['lecturers'] ?? collect()),
            new UncompletedSessionsSheet($this->data['all_sessions'] ?? collect()),
        ];
    }
}

class UncompletedLecturersSheet extends DefaultValueBinder implements FromCollection, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithCustomValueBinder
{
    protected Collection $lecturers;
    private int $row = 0;

    public function __construct(Collection $lecturers)
    {
        $this->lecturers = $lecturers;
    }

    public function bindValue(Cell $cell, $value)
    {
        if ($cell->getColumn() === 'C' || $cell->getColumn() === 'D') {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }
        return parent::bindValue($cell, $value);
    }

    public function collection()
    {
        return $this->lecturers;
    }

    public function title(): string
    {
        return 'Rekap per Dosen';
    }

    public function headings(): array
    {
        return [
            'NO',
            'NAMA DOSEN',
            'NIDN',
            'NO WHATSAPP',
            'TOTAL SESI BELUM SELESAI',
            'LEWAT JADWAL (OVERDUE)',
            'JADWAL HARI INI / MENDATANG',
            'JADWAL TERLAMA',
            'KETERLAMBATAN (HARI)',
            'DAFTAR MAHASISWA',
        ];
    }

    public function map($item): array
    {
        $this->row++;
        $dosen = $item['dosen'];
        $studentsList = $item['students']->pluck('name')->implode(', ');
        $oldest = $item['oldest_session_at'] ? $item['oldest_session_at']->format('d/m/Y H:i') : '-';

        return [
            $this->row,
            $dosen->name,
            $dosen->identifier ?: '-',
            $dosen->phone_number ?: '-',
            $item['total_sessions'],
            $item['overdue_sessions'],
            $item['upcoming_sessions'],
            $oldest,
            $item['days_overdue'] > 0 ? "{$item['days_overdue']} Hari" : 'Hari ini',
            $studentsList,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFEA580C'], // Orange
                ],
            ],
        ];
    }
}

class UncompletedSessionsSheet extends DefaultValueBinder implements FromCollection, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithCustomValueBinder
{
    protected Collection $sessions;
    private int $row = 0;

    public function __construct(Collection $sessions)
    {
        $this->sessions = $sessions;
    }

    public function bindValue(Cell $cell, $value)
    {
        if ($cell->getColumn() === 'E') {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }
        return parent::bindValue($cell, $value);
    }

    public function collection()
    {
        return $this->sessions;
    }

    public function title(): string
    {
        return 'Rincian Sesi Bimbingan';
    }

    public function headings(): array
    {
        return [
            'NO',
            'TANGGAL & WAKTU',
            'DOSEN PEMBIMBING',
            'MAHASISWA',
            'NIM',
            'JUDUL SKRIPSI',
            'TOPIK PEMBAHASAN',
            'LOKASI / METODE',
            'STATUS KEHADIRAN MAHASISWA',
            'STATUS SESI',
            'KETERLAMBATAN',
        ];
    }

    public function map($session): array
    {
        $this->row++;
        $student = $session->thesis?->student;
        $dosen = $session->dosen ?? $session->thesis?->pembimbing1;
        $isOverdue = $session->scheduled_at->isPast();
        $days = $isOverdue ? $session->scheduled_at->diffInDays(now()) : 0;
        $delayNotice = $isOverdue ? ($days > 0 ? "Lewat {$days} hari" : "Lewat hari ini") : "Akan datang";

        return [
            $this->row,
            $session->scheduled_at->format('d/m/Y H:i'),
            $dosen?->name ?? '-',
            $student?->name ?? '-',
            $student?->identifier ?? '-',
            $session->thesis?->title ?? '-',
            $session->topic,
            ucfirst($session->type ?? 'offline') . ($session->location ? " ({$session->location})" : ''),
            ucfirst($session->student_attendance_status ?? 'pending'),
            ucfirst($session->status),
            $delayNotice,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFDC2626'], // Red
                ],
            ],
        ];
    }
}
