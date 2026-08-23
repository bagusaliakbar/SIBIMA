<?php

namespace App\Exports;

use App\Models\ThesisRepository;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class RepositoryCatalogExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithCustomValueBinder
{
    protected $search;
    protected $year;
    protected $advisor;
    protected $topic;
    protected $rowNumber = 0;

    public function __construct($search = null, $year = null, $advisor = null, $topic = 'all')
    {
        $this->search = $search;
        $this->year = $year;
        $this->advisor = $advisor;
        $this->topic = $topic;
    }

    public function bindValue(Cell $cell, $value)
    {
        // Bind NPM/Identifier as explicit text to avoid scientific notation
        if ($cell->getColumn() === 'C') {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function collection()
    {
        $query = ThesisRepository::query();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('name', 'like', "%{$this->search}%")
                  ->orWhere('identifier', 'like', "%{$this->search}%")
                  ->orWhere('abstract', 'like', "%{$this->search}%")
                  ->orWhere('pembimbing1', 'like', "%{$this->search}%")
                  ->orWhere('pembimbing2', 'like', "%{$this->search}%");
            });
        }

        if ($this->year) {
            $query->where('year', $this->year);
        }

        if ($this->advisor) {
            $cleanAdv = preg_replace('/^(drs\.|dr\.|ir\.|prof\.|h\.|hj\.)\s+/i', '', preg_replace('/^\d+[\.\)]\s*/', '', trim($this->advisor)));
            $baseAdvName = trim(explode(',', $cleanAdv)[0]);

            $query->where(function($q) use ($baseAdvName) {
                $q->where('pembimbing1', 'like', "%{$baseAdvName}%")
                  ->orWhere('pembimbing2', 'like', "%{$baseAdvName}%")
                  ->orWhere('pembimbing1', 'like', "%{$this->advisor}%")
                  ->orWhere('pembimbing2', 'like', "%{$this->advisor}%");
            });
        }

        if ($this->topic && $this->topic !== 'all') {
            $definitions = ThesisRepository::getTopicDefinitions();
            $topicKeywords = $definitions[$this->topic]['keywords'] ?? [$this->topic];

            $query->where(function($q) use ($topicKeywords) {
                foreach ($topicKeywords as $kw) {
                    $q->orWhere('title', 'like', "%{$kw}%")
                      ->orWhere('abstract', 'like', "%{$kw}%");
                }
            });
        }

        return $query->orderBy('year', 'desc')->orderBy('name', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'NO',
            'ANGKATAN',
            'NPM',
            'NAMA MAHASISWA',
            'JUDUL SKRIPSI',
            'KATEGORI TOPIK',
            'PEMBIMBING 1',
            'PEMBIMBING 2',
            'ABSTRAK'
        ];
    }

    public function map($repo): array
    {
        $this->rowNumber++;
        $badge = $repo->topic_badge;

        return [
            $this->rowNumber,
            $repo->year ?? '-',
            $repo->identifier ?? '-',
            $repo->name,
            $repo->title,
            $badge['label'] ?? 'Umum',
            $repo->pembimbing1 ?? '-',
            $repo->pembimbing2 ?? '-',
            $repo->abstract ? strip_tags($repo->abstract) : '-'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // No
            'B' => 12,  // Angkatan
            'C' => 16,  // NPM
            'D' => 28,  // Nama
            'E' => 45,  // Judul
            'F' => 20,  // Kategori Topik
            'G' => 28,  // Pembimbing 1
            'H' => 28,  // Pembimbing 2
            'I' => 50,  // Abstrak
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->rowNumber + 1;

        // Header style
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 10,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'EA580C'], // SIBIMA Orange 600
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(28);

        // Center specific columns
        if ($lastRow > 1) {
            $sheet->getStyle("A2:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B2:B{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C2:C{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F2:F{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Wrap text for title and abstract
            $sheet->getStyle("E2:E{$lastRow}")->getAlignment()->setWrapText(true);
            $sheet->getStyle("I2:I{$lastRow}")->getAlignment()->setWrapText(true);

            // Border styling
            $sheet->getStyle("A1:I{$lastRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'D1D5DB'],
                    ],
                ],
            ]);
        }

        return [];
    }
}
