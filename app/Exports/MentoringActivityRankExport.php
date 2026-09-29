<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MentoringActivityRankExport implements WithMultipleSheets
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        return [
            new TopStudentsSheet($this->data['top_students'] ?? collect(), $this->data['period_label'] ?? ''),
            new InactiveStudentsSheet($this->data['inactive_students'] ?? collect(), $this->data['period_label'] ?? ''),
            new TopDosensSheet($this->data['top_dosens'] ?? collect(), $this->data['period_label'] ?? ''),
            new InactiveDosensSheet($this->data['inactive_dosens'] ?? collect(), $this->data['period_label'] ?? ''),
        ];
    }
}

class TopStudentsSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected Collection $items;
    protected string $periodLabel;
    private int $row = 0;

    public function __construct($items, string $periodLabel)
    {
        $this->items = collect($items);
        $this->periodLabel = $periodLabel;
    }

    public function collection()
    {
        return $this->items;
    }

    public function title(): string
    {
        return 'Mahasiswa Terajin';
    }

    public function headings(): array
    {
        return [
            'PERINGKAT',
            'NAMA MAHASISWA',
            'NIM',
            'ANGKATAN',
            'SESI PERIODE INI',
            'SESI P1',
            'SESI P2',
            'TOTAL SEMUA SESI',
            'PEMBIMBING 1',
            'PEMBIMBING 2',
            'TAHAPAN SKRIPSI',
            'BIMBINGAN TERAKHIR',
            'HARI SEJAK BIMBINGAN TERAKHIR',
        ];
    }

    public function map($item): array
    {
        $this->row++;
        return [
            $this->row,
            $item['name'] ?? '-',
            $item['identifier'] ?? '-',
            $item['entry_year'] ?? '-',
            $item['sessions_in_period'] ?? 0,
            $item['p1_sessions'] ?? 0,
            $item['p2_sessions'] ?? 0,
            $item['total_all_time'] ?? 0,
            $item['pembimbing1_name'] ?? '-',
            $item['pembimbing2_name'] ?? '-',
            $item['stage'] ?? '-',
            $item['last_session_at'] ? \Carbon\Carbon::parse($item['last_session_at'])->format('d/m/Y') : 'Belum Pernah',
            $item['days_since_last'] !== null ? $item['days_since_last'] . ' Hari Lalu' : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF10B981'], // Emerald
                ],
            ],
        ];
    }
}

class InactiveStudentsSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected Collection $items;
    protected string $periodLabel;
    private int $row = 0;

    public function __construct($items, string $periodLabel)
    {
        $this->items = collect($items);
        $this->periodLabel = $periodLabel;
    }

    public function collection()
    {
        return $this->items;
    }

    public function title(): string
    {
        return 'Mahasiswa Pasif & Kritis';
    }

    public function headings(): array
    {
        return [
            'NO',
            'NAMA MAHASISWA',
            'NIM',
            'ANGKATAN',
            'STATUS KEAKTIFAN',
            'HARI TANPA BIMBINGAN',
            'BIMBINGAN TERAKHIR',
            'SESI PERIODE INI',
            'PEMBIMBING 1',
            'PEMBIMBING 2',
            'NO WHATSAPP',
            'JUDUL SKRIPSI',
        ];
    }

    public function map($item): array
    {
        $this->row++;
        $daysText = $item['days_since_last'] !== null ? $item['days_since_last'] . ' Hari' : 'Belum Pernah Sesi';

        return [
            $this->row,
            $item['name'] ?? '-',
            $item['identifier'] ?? '-',
            $item['entry_year'] ?? '-',
            $item['health_status'] ?? '-',
            $daysText,
            $item['last_session_at'] ? \Carbon\Carbon::parse($item['last_session_at'])->format('d/m/Y') : 'Belum Pernah',
            $item['sessions_in_period'] ?? 0,
            $item['pembimbing1_name'] ?? '-',
            $item['pembimbing2_name'] ?? '-',
            $item['phone'] ?? '-',
            $item['title'] ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFE11D48'], // Rose
                ],
            ],
        ];
    }
}

class TopDosensSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected Collection $items;
    protected string $periodLabel;
    private int $row = 0;

    public function __construct($items, string $periodLabel)
    {
        $this->items = collect($items);
        $this->periodLabel = $periodLabel;
    }

    public function collection()
    {
        return $this->items;
    }

    public function title(): string
    {
        return 'Dosen Terajin';
    }

    public function headings(): array
    {
        return [
            'PERINGKAT',
            'NAMA DOSEN',
            'NIDN / KODE',
            'SESI BIMBINGAN PERIODE INI',
            'MAHASISWA UNIK DIBIMBING',
            'TOTAL MAHASISWA AKTIF',
            'TOTAL SEMUA SESI',
            'STATUS KEAKTIFAN',
            'BIMBINGAN TERAKHIR',
            'HARI SEJAK SESI TERAKHIR',
        ];
    }

    public function map($item): array
    {
        $this->row++;
        return [
            $this->row,
            $item['name'] ?? '-',
            $item['identifier'] ?? '-',
            $item['sessions_in_period'] ?? 0,
            $item['unique_students_in_period'] ?? 0,
            $item['supervised_count'] ?? 0,
            $item['total_all_time'] ?? 0,
            $item['activity_level'] ?? '-',
            $item['last_session_at'] ? \Carbon\Carbon::parse($item['last_session_at'])->format('d/m/Y') : 'Belum Pernah',
            $item['days_since_last'] !== null ? $item['days_since_last'] . ' Hari Lalu' : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF2563EB'], // Blue
                ],
            ],
        ];
    }
}

class InactiveDosensSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected Collection $items;
    protected string $periodLabel;
    private int $row = 0;

    public function __construct($items, string $periodLabel)
    {
        $this->items = collect($items);
        $this->periodLabel = $periodLabel;
    }

    public function collection()
    {
        return $this->items;
    }

    public function title(): string
    {
        return 'Dosen Perlu Perhatian';
    }

    public function headings(): array
    {
        return [
            'NO',
            'NAMA DOSEN',
            'NIDN / KODE',
            'STATUS KEAKTIFAN',
            'SESI PERIODE INI',
            'MAHASISWA AKTIF MENUNGGU',
            'BIMBINGAN TERAKHIR',
            'HARI SEJAK SESI TERAKHIR',
            'NO WHATSAPP',
            'EMAIL',
        ];
    }

    public function map($item): array
    {
        $this->row++;
        $daysText = $item['days_since_last'] !== null ? $item['days_since_last'] . ' Hari Lalu' : 'Belum Pernah Sesi';

        return [
            $this->row,
            $item['name'] ?? '-',
            $item['identifier'] ?? '-',
            $item['activity_level'] ?? '-',
            $item['sessions_in_period'] ?? 0,
            $item['supervised_count'] ?? 0,
            $item['last_session_at'] ? \Carbon\Carbon::parse($item['last_session_at'])->format('d/m/Y') : 'Belum Pernah',
            $daysText,
            $item['phone'] ?? '-',
            $item['email'] ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFF59E0B'], // Amber
                ],
            ],
        ];
    }
}
