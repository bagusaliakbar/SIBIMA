<?php

namespace App\Exports;

use App\Models\Thesis;
use App\Models\User;
use App\Models\ActivityLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class MonitoringExport extends DefaultValueBinder implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    WithCustomStartCell, 
    WithColumnWidths, 
    WithStyles, 
    WithTitle, 
    WithEvents, 
    WithCustomValueBinder
{
    protected string $type;
    protected $startDate;
    protected $endDate;
    protected $accStatus;
    protected $pembimbingId;
    protected $entryYear;
    protected $search;
    protected $graduationStatus;
    protected int $rowNumber = 0;
    protected int $totalCount = 0;

    public function __construct(
        $type = 'akademik', 
        $startDate = null, 
        $endDate = null, 
        $accStatus = 'all', 
        $pembimbingId = null, 
        $entryYear = null, 
        $search = null,
        $graduationStatus = 'active'
    ) {
        $this->type = $type ?? 'akademik';
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->accStatus = $accStatus ?? 'all';
        $this->pembimbingId = $pembimbingId;
        $this->entryYear = $entryYear;
        $this->search = $search;
        $this->graduationStatus = $graduationStatus ?? 'active';
    }

    public function collection()
    {
        if ($this->type === 'dosen') {
            $query = User::where('role', 'dosen')->withCount(['thesesAsP1', 'thesesAsP2']);
            if ($this->pembimbingId) {
                $query->where('id', $this->pembimbingId);
            }
            if ($this->search) {
                $query->where('name', 'like', "%{$this->search}%");
            }
            $res = $query->orderBy('name')->get();
            $this->totalCount = $res->count();
            return $res;
        }

        if ($this->type === 'logs') {
            $query = ActivityLog::with('user')
                ->when($this->startDate && $this->endDate, function($q) {
                    $q->whereBetween('created_at', [$this->startDate, $this->endDate]);
                })
                ->when($this->search, function($q) {
                    $q->where('description', 'like', "%{$this->search}%");
                });
            $res = $query->latest()->get();
            $this->totalCount = $res->count();
            return $res;
        }

        // Monitoring Theses (akademik, mahasiswa, kelulusan)
        $query = Thesis::with(['student', 'pembimbing1', 'pembimbing2', 'graduation'])
            ->withMentoringCounts();

        if ($this->type === 'kelulusan' || $this->graduationStatus === 'graduated') {
            $query->graduated();
        } elseif ($this->graduationStatus === 'active') {
            $query->notGraduated();
        }

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
        }

        if (!empty($this->pembimbingId)) {
            $query->where(function($q) {
                $q->where('pembimbing1_id', $this->pembimbingId)
                  ->orWhere('pembimbing2_id', $this->pembimbingId);
            });
        }

        if (!empty($this->entryYear)) {
            $query->whereHas('student', function($q) {
                $q->where('entry_year', $this->entryYear);
            });
        }

        if (!empty($this->accStatus) && $this->accStatus !== 'all') {
            $query->accStatus($this->accStatus);
        }

        if (!empty($this->search)) {
            $query->search($this->search);
        }

        $res = $query->orderBy('created_at', 'desc')->get();
        $this->totalCount = $res->count();
        return $res;
    }

    public function startCell(): string
    {
        return 'A5';
    }

    public function title(): string
    {
        return match($this->type) {
            'dosen' => 'Beban Dosen',
            'logs' => 'Log Aktivitas',
            'kelulusan' => 'Data Kelulusan',
            default => match($this->graduationStatus) {
                'graduated' => 'Mahasiswa Lulus',
                'all' => 'Semua Mahasiswa',
                default => 'Mahasiswa Aktif',
            }
        };
    }

    public function headings(): array
    {
        switch ($this->type) {
            case 'dosen':
                return ['NO', 'NAMA DOSEN', 'NIP / NIDN', 'PEMBIMBING 1', 'PEMBIMBING 2', 'TOTAL BEBAN BIMBINGAN'];
            case 'logs':
                return ['NO', 'WAKTU', 'PENGGUNA', 'AKTIVITAS', 'DESKRIPSI'];
            case 'kelulusan':
                return ['NO', 'NPM', 'NAMA MAHASISWA', 'ANGKATAN', 'JUDUL SKRIPSI', 'PEMBIMBING 1', 'PEMBIMBING 2', 'TANGGAL SELESAI', 'STATUS'];
            default:
                return [
                    'NO',
                    'NPM',
                    'NAMA MAHASISWA',
                    'ANGKATAN',
                    'JUDUL SKRIPSI',
                    'PEMBIMBING 1',
                    'PEMBIMBING 2',
                    'SESI P1',
                    'SESI P2',
                    'TOTAL SESI',
                    'STATUS ACC SEMINAR (UP)',
                    'STATUS ACC SIDANG',
                    'STATUS KELULUSAN',
                    'STATUS BIMBINGAN',
                ];
        }
    }

    public function map($item): array
    {
        $this->rowNumber++;

        if ($this->type === 'dosen') {
            return [
                $this->rowNumber,
                $item->name,
                $item->identifier ?? '-',
                $item->theses_as_p1_count ?? 0,
                $item->theses_as_p2_count ?? 0,
                ($item->theses_as_p1_count ?? 0) + ($item->theses_as_p2_count ?? 0),
            ];
        }

        if ($this->type === 'logs') {
            return [
                $this->rowNumber,
                $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-',
                $item->user?->name ?? 'System',
                strtoupper($item->activity ?? '-'),
                $item->description ?? '-',
            ];
        }

        if ($this->type === 'kelulusan') {
            return [
                $this->rowNumber,
                $item->student?->identifier ?? '-',
                $item->student?->name ?? '-',
                $item->student?->entry_year ?? '-',
                $item->final_title ?? $item->title ?? '-',
                $item->pembimbing1?->name ?? 'Belum Ditugaskan',
                $item->pembimbing2?->name ?? 'Belum Ditugaskan',
                $item->updated_at ? $item->updated_at->format('d/m/Y') : '-',
                'Lulus / Selesai',
            ];
        }

        // Monitoring Akademik / Mahasiswa
        $accUpLabel = match(true) {
            $item->isAccUpFinal() => 'Siap Seminar (ACC P1 & P2)',
            ($item->acc_up_p1 && !$item->acc_up_p2) => 'ACC P1 Saja',
            (!$item->acc_up_p1 && $item->acc_up_p2) => 'ACC P2 Saja',
            default => 'Belum ACC',
        };

        $accSidangLabel = match(true) {
            $item->isAccSidangFinal() => 'Siap Sidang (ACC P1 & P2)',
            ($item->acc_sidang_p1 && !$item->acc_sidang_p2) => 'ACC P1 Saja',
            (!$item->acc_sidang_p1 && $item->acc_sidang_p2) => 'ACC P2 Saja',
            default => 'Belum ACC',
        };

        $graduationLabel = $item->isGraduated() ? 'Lulus / Selesai' : 'Aktif / Belum Lulus';

        $statusLabel = match(true) {
            $item->isGraduated() => 'Selesai',
            $item->status === 'active' => 'Bimbingan Aktif',
            $item->status === 'pending' => 'Menunggu Pembimbing',
            $item->status === 'rejected' => 'Ditolak',
            default => ucfirst($item->status ?? 'Aktif'),
        };

        $p1Sessions = $item->sessions_p1 ?? 0;
        $p2Sessions = $item->sessions_p2 ?? 0;
        $totalSessions = $item->total_sessions ?? ($p1Sessions + $p2Sessions);

        return [
            $this->rowNumber,
            $item->student?->identifier ?? '-',
            $item->student?->name ?? '-',
            $item->student?->entry_year ?? '-',
            $item->final_title ?? $item->title ?? '-',
            $item->pembimbing1?->name ?? 'Belum Ditugaskan',
            $item->pembimbing2?->name ?? 'Belum Ditugaskan',
            $p1Sessions,
            $p2Sessions,
            $totalSessions,
            $accUpLabel,
            $accSidangLabel,
            $graduationLabel,
            $statusLabel,
        ];
    }

    public function bindValue(Cell $cell, $value)
    {
        // Bind NPM / NIP explicitly as string to prevent scientific notation & retain leading zeros
        if (in_array($cell->getColumn(), ['B', 'C'])) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function columnWidths(): array
    {
        switch ($this->type) {
            case 'dosen':
                return [
                    'A' => 6,
                    'B' => 32,
                    'C' => 20,
                    'D' => 18,
                    'E' => 18,
                    'F' => 24,
                ];
            case 'logs':
                return [
                    'A' => 6,
                    'B' => 20,
                    'C' => 24,
                    'D' => 20,
                    'E' => 45,
                ];
            case 'kelulusan':
                return [
                    'A' => 6,
                    'B' => 18,
                    'C' => 32,
                    'D' => 12,
                    'E' => 48,
                    'F' => 28,
                    'G' => 28,
                    'H' => 18,
                    'I' => 18,
                ];
            default:
                return [
                    'A' => 6,   // NO
                    'B' => 18,  // NPM
                    'C' => 32,  // NAMA MAHASISWA
                    'D' => 12,  // ANGKATAN
                    'E' => 48,  // JUDUL SKRIPSI
                    'F' => 28,  // PEMBIMBING 1
                    'G' => 28,  // PEMBIMBING 2
                    'H' => 10,  // SESI P1
                    'I' => 10,  // SESI P2
                    'J' => 12,  // TOTAL SESI
                    'K' => 25,  // STATUS ACC UP
                    'L' => 25,  // STATUS ACC SIDANG
                    'M' => 20,  // STATUS KELULUSAN
                    'N' => 20,  // STATUS BIMBINGAN
                ];
        }
    }

    public function styles(Worksheet $sheet)
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestCol = match($this->type) {
                    'dosen' => 'F',
                    'logs' => 'E',
                    'kelulusan' => 'I',
                    default => 'N',
                };

                // Title Banner (Row 1)
                $sheet->mergeCells("A1:{$highestCol}1");
                $sheet->setCellValue('A1', 'LAPORAN MONITORING PROGRES BIMBINGAN SKRIPSI MAHASISWA');
                $sheet->getRowDimension(1)->setRowHeight(32);
                $sheet->getStyle("A1:{$highestCol}1")->applyFromArray([
                    'font' => [
                        'name' => 'Segoe UI',
                        'bold' => true,
                        'size' => 13,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1E293B'], // Slate-800
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Subtitle (Row 2) - EXACTLY AS REQUESTED
                $sheet->mergeCells("A2:{$highestCol}2");
                $sheet->setCellValue('A2', 'SISTEM INFORMASI BIMBINGAN MAHASISWA (SIBIMA) - FAKULTAS ILMU KOMPUTER - UNIVERSITAS SUBANG');
                $sheet->getRowDimension(2)->setRowHeight(18);
                $sheet->getStyle("A2:{$highestCol}2")->applyFromArray([
                    'font' => [
                        'name' => 'Segoe UI',
                        'bold' => true,
                        'size' => 8.5,
                        'color' => ['rgb' => 'E2E8F0'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1E293B'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Metadata Bar (Row 3)
                $sheet->mergeCells("A3:{$highestCol}3");
                $pembimbingName = 'Semua Pembimbing';
                if (!empty($this->pembimbingId)) {
                    $d = User::find($this->pembimbingId);
                    if ($d) $pembimbingName = $d->name;
                }

                $entryYearLabel = !empty($this->entryYear) ? 'Angkatan ' . $this->entryYear : 'Semua Angkatan';
                $accLabels = [
                    'all' => 'Semua Status ACC',
                    'ready_seminar' => 'Siap Seminar (ACC UP)',
                    'ready_defense' => 'Siap Sidang (ACC Sidang)',
                    'partial_acc' => 'Sebagian ACC',
                    'in_mentoring' => 'Dalam Bimbingan',
                ];
                $accLabel = $accLabels[$this->accStatus] ?? 'Semua Status ACC';
                $gradLabel = match($this->graduationStatus) {
                    'graduated' => 'Sudah Lulus',
                    'all' => 'Semua Mahasiswa',
                    default => 'Belum Lulus (Aktif)',
                };
                $exportDate = now()->translatedFormat('d F Y, H:i') . ' WIB';

                $metaText = "Status: {$gradLabel}   |   Dosen Pembimbing: {$pembimbingName}   |   Angkatan: {$entryYearLabel}   |   Status ACC: {$accLabel}   |   Total: {$this->totalCount} Mahasiswa   |   Waktu Unduh: {$exportDate}";
                $sheet->setCellValue('A3', $metaText);
                $sheet->getRowDimension(3)->setRowHeight(22);
                $sheet->getStyle("A3:{$highestCol}3")->applyFromArray([
                    'font' => [
                        'name' => 'Segoe UI',
                        'size' => 8.5,
                        'color' => ['rgb' => '475569'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F1F5F9'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CBD5E1'],
                        ],
                    ],
                ]);

                // Row 4: Spacer
                $sheet->getRowDimension(4)->setRowHeight(10);

                // Table Header (Row 5)
                $sheet->getRowDimension(5)->setRowHeight(28);
                $sheet->getStyle("A5:{$highestCol}5")->applyFromArray([
                    'font' => [
                        'name' => 'Segoe UI',
                        'bold' => true,
                        'size' => 9.5,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '0F172A'], // Slate-900 / Navy
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '334155'],
                        ],
                    ],
                ]);

                // Data Rows (Row 6 to highestRow)
                if ($highestRow >= 6) {
                    $sheet->getStyle("A6:{$highestCol}{$highestRow}")->applyFromArray([
                        'font' => [
                            'name' => 'Segoe UI',
                            'size' => 9.5,
                            'color' => ['rgb' => '1E293B'],
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'E2E8F0'],
                            ],
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);

                    // Specific alignments for default monitoring
                    if ($this->type === 'akademik' || $this->type === 'mahasiswa') {
                        $sheet->getStyle("A6:A{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("B6:B{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("C6:C{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                        $sheet->getStyle("D6:D{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("E6:E{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setWrapText(true);
                        $sheet->getStyle("F6:F{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                        $sheet->getStyle("G6:G{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                        $sheet->getStyle("H6:H{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("I6:I{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("J6:J{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("K6:K{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("L6:L{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("M6:M{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("N6:N{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }

                    // Row Heights & Zebra striping
                    for ($row = 6; $row <= $highestRow; $row++) {
                        $sheet->getRowDimension($row)->setRowHeight(32);
                        if ($row % 2 === 1) {
                            $sheet->getStyle("A{$row}:{$highestCol}{$row}")->getFill()->applyFromArray([
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F8FAFC'], // Slate-50
                            ]);
                        }
                    }

                    // AutoFilter on Table Header
                    $sheet->setAutoFilter("A5:{$highestCol}{$highestRow}");
                }

                // Footer Note
                $footerRow = max($highestRow + 2, 7);
                $sheet->mergeCells("A{$footerRow}:{$highestCol}{$footerRow}");
                $sheet->setCellValue("A{$footerRow}", "* Dokumen ini diunduh secara resmi melalui Sistem Informasi SIBIMA pada " . now()->format('d/m/Y H:i:s') . ".");
                $sheet->getStyle("A{$footerRow}")->applyFromArray([
                    'font' => [
                        'name' => 'Segoe UI',
                        'italic' => true,
                        'size' => 8,
                        'color' => ['rgb' => '94A3B8'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Freeze Pane below Table Header
                $sheet->freezePane('A6');

                // Enable Gridlines
                $sheet->setShowGridLines(true);
            },
        ];
    }
}
