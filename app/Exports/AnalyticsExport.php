<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AnalyticsExport implements WithMultipleSheets
{
    use Exportable;

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        return [
            new AnalyticsSummarySheet($this->data),
            new AnalyticsSeminarAdvisorSheet($this->data),
            new AnalyticsCohortProgressSheet($this->data),
            new AnalyticsUnfinishedByAdvisorSheet($this->data),
            new AnalyticsWorkloadSheet($this->data),
            new AnalyticsGradesSheet($this->data),
        ];
    }
}

class AnalyticsSummarySheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Ringkasan KPI';
    }

    public function headings(): array
    {
        return ['Indikator Kinerja / Metrik SIBIMA', 'Nilai / Jumlah', 'Keterangan'];
    }

    public function array(): array
    {
        $kpi = $this->data['kpi'] ?? [];
        return [
            ['Total Mahasiswa Terdaftar Skripsi', $kpi['totalStudents'] ?? 0, 'Mahasiswa yang mengajukan atau memiliki skripsi'],
            ['Skripsi Aktif (Sedang Berjalan)', $kpi['activeTheses'] ?? 0, 'Status skripsi aktif saat ini'],
            ['Mahasiswa Sudah Lulus', $kpi['completedTheses'] ?? 0, 'Skripsi berstatus completed/selesai'],
            ['Mahasiswa Sudah Seminar Proposal', $kpi['seminarDone'] ?? 0, 'Telah melaksanakan/disetujui seminar'],
            ['Mahasiswa Belum Seminar Proposal', $kpi['seminarPending'] ?? 0, 'Masih dalam proses pengerjaan Bab 1-3'],
            ['Mahasiswa Sudah Sidang Skripsi', $kpi['defenseDone'] ?? 0, 'Telah melaksanakan/disetujui sidang skripsi'],
            ['Mahasiswa Belum Sidang Skripsi', $kpi['defensePending'] ?? 0, 'Belum mendaftar/melaksanakan sidang skripsi'],
            ['Total Sesi Bimbingan Terlaksana', $kpi['completedSessions'] ?? 0, 'Sesi bimbingan status completed dan hadir'],
            ['Tingkat Kelulusan Tepat Waktu', ($kpi['onTimePercentage'] ?? 0) . '%', 'Lulus <= 4 tahun masa studi'],
            ['Mahasiswa Kritis Masa Studi (> 4 Thn)', $kpi['criticalCohortStudents'] ?? 0, 'Mendekati atau melebihi batas masa studi'],
            ['Mahasiswa Kritis Bimbingan (> 14 Hari)', $kpi['criticalMentoringStudents'] ?? 0, 'Tidak bimbingan lebih dari 14 hari'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:C1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFE06D10']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $sheet->getStyle('A1:C12')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFCBD5E1']
                ]
            ]
        ]);

        return [];
    }
}

class AnalyticsSeminarAdvisorSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Seminar per Pembimbing';
    }

    public function headings(): array
    {
        return [
            'Nama Dosen Pembimbing',
            'Mahasiswa P1 (Total)',
            'Sudah Seminar (P1)',
            'Belum Seminar (P1)',
            '% Selesai P1',
            'Mahasiswa P2 (Total)',
            'Sudah Seminar (P2)',
            'Belum Seminar (P2)',
            '% Selesai P2',
        ];
    }

    public function array(): array
    {
        $rows = [];
        $advisors = $this->data['seminarByAdvisor'] ?? [];

        foreach ($advisors as $dosenName => $info) {
            $p1Total = ($info['p1_done'] ?? 0) + ($info['p1_pending'] ?? 0);
            $p2Total = ($info['p2_done'] ?? 0) + ($info['p2_pending'] ?? 0);
            $p1Rate = $p1Total > 0 ? round((($info['p1_done'] ?? 0) / $p1Total) * 100, 1) . '%' : '0%';
            $p2Rate = $p2Total > 0 ? round((($info['p2_done'] ?? 0) / $p2Total) * 100, 1) . '%' : '0%';

            $rows[] = [
                $dosenName,
                $p1Total,
                $info['p1_done'] ?? 0,
                $info['p1_pending'] ?? 0,
                $p1Rate,
                $p2Total,
                $info['p2_done'] ?? 0,
                $info['p2_pending'] ?? 0,
                $p2Rate,
            ];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF1E293B']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $highestRow = $sheet->getHighestRow();
        if ($highestRow >= 2) {
            $sheet->getStyle("A1:I{$highestRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFCBD5E1']
                    ]
                ]
            ]);
        }

        return [];
    }
}

class AnalyticsCohortProgressSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Progres per Angkatan';
    }

    public function headings(): array
    {
        return [
            'Tahun Angkatan',
            'Total Mahasiswa',
            'Sudah Seminar',
            'Belum Seminar',
            '% Sudah Seminar',
            'Sudah Sidang / Lulus',
            'Belum Sidang',
            '% Sudah Sidang',
        ];
    }

    public function array(): array
    {
        $rows = [];
        $cohorts = $this->data['cohortProgress'] ?? [];

        foreach ($cohorts as $year => $stat) {
            $total = $stat['total'] ?? 0;
            $semDone = $stat['seminar_done'] ?? 0;
            $semPending = $stat['seminar_pending'] ?? 0;
            $defDone = $stat['defense_done'] ?? 0;
            $defPending = $stat['defense_pending'] ?? 0;

            $semRate = $total > 0 ? round(($semDone / $total) * 100, 1) . '%' : '0%';
            $defRate = $total > 0 ? round(($defDone / $total) * 100, 1) . '%' : '0%';

            $rows[] = [
                'Angkatan ' . $year,
                $total,
                $semDone,
                $semPending,
                $semRate,
                $defDone,
                $defPending,
                $defRate,
            ];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF2563EB']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $highestRow = $sheet->getHighestRow();
        if ($highestRow >= 2) {
            $sheet->getStyle("A1:H{$highestRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFCBD5E1']
                    ]
                ]
            ]);
        }

        return [];
    }
}

class AnalyticsUnfinishedByAdvisorSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Mahasiswa Belum Lulus per Dosen';
    }

    public function headings(): array
    {
        return [
            'Nama Dosen Pembimbing',
            'Belum Lulus (Sebagai P1)',
            'Belum Lulus (Sebagai P2)',
            'Total Belum Lulus',
            'Batas Kuota Bimbingan',
            'Status Beban Kerja',
        ];
    }

    public function array(): array
    {
        $rows = [];
        $advisors = $this->data['unfinishedByAdvisor'] ?? [];

        foreach ($advisors as $dosenName => $info) {
            $p1 = $info['p1'] ?? 0;
            $p2 = $info['p2'] ?? 0;
            $total = $p1 + $p2;
            $quota = $info['quota'] ?? 10;
            $status = $total >= $quota ? 'Overload / Penuh' : ($total >= ($quota * 0.8) ? 'Mendekati Kuota' : 'Normal');

            $rows[] = [
                $dosenName,
                $p1,
                $p2,
                $total,
                $quota,
                $status,
            ];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFDC2626']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $highestRow = $sheet->getHighestRow();
        if ($highestRow >= 2) {
            $sheet->getStyle("A1:F{$highestRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFCBD5E1']
                    ]
                ]
            ]);
        }

        return [];
    }
}

class AnalyticsWorkloadSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Beban & Kuota Dosen';
    }

    public function headings(): array
    {
        return [
            'Nama Dosen',
            'Bimbingan Aktif (P1)',
            'Bimbingan Aktif (P2)',
            'Total Bimbingan Aktif',
            'Batas Kuota',
            'Sisa Kuota Tersedia',
        ];
    }

    public function array(): array
    {
        $rows = [];
        $workload = $this->data['dosenWorkloadDetails'] ?? [];

        foreach ($workload as $item) {
            $rows[] = [
                $item['name'],
                $item['p1_count'],
                $item['p2_count'],
                $item['total_count'],
                $item['quota'],
                max(0, $item['quota'] - $item['total_count']),
            ];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF059669']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $highestRow = $sheet->getHighestRow();
        if ($highestRow >= 2) {
            $sheet->getStyle("A1:F{$highestRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFCBD5E1']
                    ]
                ]
            ]);
        }

        return [];
    }
}

class AnalyticsGradesSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Distribusi Nilai Sidang';
    }

    public function headings(): array
    {
        return [
            'Grade / Predikat',
            'Rentang Nilai Standar',
            'Jumlah Mahasiswa',
            'Persentase Kelulusan',
        ];
    }

    public function array(): array
    {
        $dist = $this->data['scoreDistribution'] ?? ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0];
        $total = array_sum($dist);

        return [
            ['Grade A (Sangat Memuaskan)', '80.00 - 100.00', $dist['A'] ?? 0, $total > 0 ? round((($dist['A'] ?? 0) / $total) * 100, 1) . '%' : '0%'],
            ['Grade B (Memuaskan)', '70.00 - 79.99', $dist['B'] ?? 0, $total > 0 ? round((($dist['B'] ?? 0) / $total) * 100, 1) . '%' : '0%'],
            ['Grade C (Cukup)', '60.00 - 69.99', $dist['C'] ?? 0, $total > 0 ? round((($dist['C'] ?? 0) / $total) * 100, 1) . '%' : '0%'],
            ['Grade D (Kurang)', '50.00 - 59.99', $dist['D'] ?? 0, $total > 0 ? round((($dist['D'] ?? 0) / $total) * 100, 1) . '%' : '0%'],
            ['Grade E (Tidak Lulus)', '0.00 - 49.99', $dist['E'] ?? 0, $total > 0 ? round((($dist['E'] ?? 0) / $total) * 100, 1) . '%' : '0%'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF7C3AED']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $sheet->getStyle('A1:D6')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFCBD5E1']
                ]
            ]
        ]);

        return [];
    }
}
