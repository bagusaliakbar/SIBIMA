<?php

namespace App\Exports;

use App\Models\Wave;
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

abstract class BaseApplicationExport extends DefaultValueBinder implements
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
    protected $waveId;
    protected $status;
    protected $search;
    protected int $rowNumber = 0;
    protected int $totalCount = 0;

    public function __construct($waveId = null, $status = 'all', $search = null)
    {
        $this->waveId = $waveId;
        $this->status = $status ?? 'all';
        $this->search = $search;
    }

    abstract protected function getModelClass(): string;
    abstract public function title(): string;
    abstract protected function getReportTitle(): string;
    abstract protected function getHeaderColor(): string;

    public function collection()
    {
        $modelClass = $this->getModelClass();
        $query = $modelClass::with([
            'thesis.student',
            'thesis.pembimbing1',
            'thesis.pembimbing2',
            'wave'
        ]);

        if (!empty($this->waveId) && $this->waveId !== 'all') {
            $query->where('wave_id', $this->waveId);
        }

        if (!empty($this->status) && $this->status !== 'all') {
            $query->where('status', $this->status);
        }

        if (!empty($this->search)) {
            $search = $this->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('thesis.student', function($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                       ->orWhere('identifier', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('thesis', function($tq) use ($search) {
                    $tq->where('title', 'like', "%{$search}%");
                });
            });
        }

        $collection = $query->orderBy('created_at', 'desc')->get();
        $this->totalCount = $collection->count();
        return $collection;
    }

    public function startCell(): string
    {
        return 'A5';
    }

    public function headings(): array
    {
        return [
            'NO',
            'NPM',
            'NAMA MAHASISWA',
            'JUDUL SKRIPSI',
            'PEMBIMBING',
        ];
    }

    public function map($app): array
    {
        $this->rowNumber++;
        $student = $app->thesis?->student;
        $thesis = $app->thesis;

        $p1 = $thesis?->pembimbing1?->name;
        $p2 = $thesis?->pembimbing2?->name;

        if ($p1 && $p2) {
            $pembimbingText = "1. " . $p1 . "\n2. " . $p2;
        } elseif ($p1) {
            $pembimbingText = "1. " . $p1 . "\n2. -";
        } elseif ($p2) {
            $pembimbingText = "1. -\n2. " . $p2;
        } else {
            $pembimbingText = "-";
        }

        return [
            $this->rowNumber,
            $student?->identifier ?? '-',
            $student?->name ?? '-',
            $thesis?->title ?? '-',
            $pembimbingText,
        ];
    }

    public function bindValue(Cell $cell, $value)
    {
        // Bind NPM explicitly as string to preserve leading zeroes and prevent scientific notation
        if ($cell->getColumn() === 'B') {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // NO
            'B' => 18,  // NPM
            'C' => 32,  // NAMA MAHASISWA
            'D' => 52,  // JUDUL SKRIPSI
            'E' => 38,  // PEMBIMBING
        ];
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
                $headerColor = $this->getHeaderColor();

                // 1. Resolve Wave Name & Status Label for Metadata Bar
                $waveName = 'Semua Gelombang';
                if (!empty($this->waveId) && $this->waveId !== 'all') {
                    $wave = Wave::find($this->waveId);
                    if ($wave) {
                        $waveName = $wave->name . ($wave->is_active ? ' (Aktif)' : ' (Arsip)');
                    }
                }

                $statusMap = [
                    'all' => 'Semua Status',
                    'pending' => 'Menunggu Validasi',
                    'approved' => 'Disetujui',
                    'rejected' => 'Perlu Revisi / Ditolak',
                ];
                $statusLabel = $statusMap[$this->status] ?? 'Semua Status';
                $exportDate = now()->translatedFormat('d F Y, H:i') . ' WIB';

                // 2. Set Row 1 (Title Banner)
                $sheet->mergeCells('A1:E1');
                $sheet->setCellValue('A1', $this->getReportTitle());
                $sheet->getRowDimension(1)->setRowHeight(32);
                $sheet->getStyle('A1:E1')->applyFromArray([
                    'font' => [
                        'name' => 'Segoe UI',
                        'bold' => true,
                        'size' => 13,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => $headerColor],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // 3. Set Row 2 (Subtitle)
                $sheet->mergeCells('A2:E2');
                $sheet->setCellValue('A2', 'SISTEM INFORMASI BIMBINGAN MAHASISWA (SIBIMA) - FAKULTAS ILMU KOMPUTER - UNIVERSITAS SUBANG');
                $sheet->getRowDimension(2)->setRowHeight(18);
                $sheet->getStyle('A2:E2')->applyFromArray([
                    'font' => [
                        'name' => 'Segoe UI',
                        'bold' => true,
                        'size' => 8.5,
                        'color' => ['rgb' => 'E2E8F0'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => $headerColor],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // 4. Set Row 3 (Metadata Bar)
                $sheet->mergeCells('A3:E3');
                $metaText = "Gelombang: {$waveName}   |   Status: {$statusLabel}   |   Waktu Unduh: {$exportDate}   |   Total Data: {$this->totalCount} Mahasiswa";
                $sheet->setCellValue('A3', $metaText);
                $sheet->getRowDimension(3)->setRowHeight(22);
                $sheet->getStyle('A3:E3')->applyFromArray([
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

                // 5. Spacer Row 4
                $sheet->getRowDimension(4)->setRowHeight(10);

                // 6. Table Header Row 5
                $sheet->getRowDimension(5)->setRowHeight(28);
                $sheet->getStyle('A5:E5')->applyFromArray([
                    'font' => [
                        'name' => 'Segoe UI',
                        'bold' => true,
                        'size' => 9.5,
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
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '334155'],
                        ],
                    ],
                ]);

                // 7. Data Rows Styling (Rows 6 to highestRow)
                if ($highestRow >= 6) {
                    $sheet->getStyle("A6:E{$highestRow}")->applyFromArray([
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

                    // Specific column alignments & wrapping
                    $sheet->getStyle("A6:A{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("B6:B{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("C6:C{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle("D6:D{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setWrapText(true);
                    $sheet->getStyle("E6:E{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setWrapText(true);

                    // Row Heights & Subtle Alternating Zebra Striping
                    for ($row = 6; $row <= $highestRow; $row++) {
                        $sheet->getRowDimension($row)->setRowHeight(34);
                        if ($row % 2 === 1) {
                            $sheet->getStyle("A{$row}:E{$row}")->getFill()->applyFromArray([
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F8FAFC'], // Slate-50
                            ]);
                        }
                    }

                    // AutoFilter on the table headers
                    $sheet->setAutoFilter("A5:E{$highestRow}");
                }

                // 8. Footer Note
                $footerRow = max($highestRow + 2, 7);
                $sheet->mergeCells("A{$footerRow}:E{$footerRow}");
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

                // 9. Freeze Pane below Table Header
                $sheet->freezePane('A6');

                // 10. Enable Gridlines
                $sheet->setShowGridLines(true);
            },
        ];
    }
}
