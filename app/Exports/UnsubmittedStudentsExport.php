<?php

namespace App\Exports;

use App\Models\User;
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

class UnsubmittedStudentsExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithCustomValueBinder
{
    protected $search;
    protected $entryYear;
    protected $semesterFilter;
    protected $rowNumber = 0;

    public function __construct($search = null, $entryYear = null, $semesterFilter = null)
    {
        $this->search = $search;
        $this->entryYear = $entryYear;
        $this->semesterFilter = $semesterFilter;
    }

    public function bindValue(Cell $cell, $value)
    {
        // Bind NPM and Phone Number as explicit strings
        if (in_array($cell->getColumn(), ['C', 'H'])) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function collection()
    {
        $query = User::where('role', 'mahasiswa')
            ->whereDoesntHave('thesis')
            ->when($this->search, function ($q, $search) {
                $q->where(function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                       ->orWhere('identifier', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($this->entryYear, function ($q, $entryYear) {
                $q->where('entry_year', $entryYear);
            });

        $students = $query->orderBy('entry_year', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($this->semesterFilter === 'critical') {
            $students = $students->filter(fn($u) => $u->is_critical_semester);
        } elseif ($this->semesterFilter === 'warning') {
            $students = $students->filter(fn($u) => $u->current_semester >= 7 && $u->current_semester < 13);
        } elseif ($this->semesterFilter === 'normal') {
            $students = $students->filter(fn($u) => $u->current_semester < 7);
        }

        return $students;
    }

    public function headings(): array
    {
        return [
            'NO',
            'NAMA MAHASISWA',
            'NPM',
            'ANGKATAN',
            'SEMESTER AKTIF',
            'STATUS MASA STUDI',
            'EMAIL',
            'NO. WHATSAPP',
            'TANGGAL REGISTRASI AKUN',
            'LAMA BELUM MENGAJUKAN (HARI)',
            'STATUS AKUN',
        ];
    }

    public function map($student): array
    {
        $this->rowNumber++;

        $semester = $student->current_semester ?? '-';
        $statusMasaStudi = 'Normal (Sem ' . $semester . ')';
        if ($student->is_critical_semester) {
            $statusMasaStudi = 'KRITIS (Sem ' . $semester . ')';
        } elseif ($semester >= 7) {
            $statusMasaStudi = 'Perhatian (Sem ' . $semester . ')';
        }

        $daysSinceCreation = $student->created_at ? (int) $student->created_at->diffInDays(now()) : 0;

        return [
            $this->rowNumber,
            strtoupper($student->name),
            $student->identifier ?? '-',
            $student->entry_year ?? '-',
            $semester,
            $statusMasaStudi,
            $student->email,
            $student->phone_number ? "'" . $student->phone_number : '-',
            $student->created_at ? $student->created_at->translatedFormat('d F Y') : '-',
            $daysSinceCreation . ' Hari',
            $student->is_active ? 'Aktif' : 'Nonaktif',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // NO
            'B' => 32,  // NAMA
            'C' => 18,  // NPM
            'D' => 14,  // ANGKATAN
            'E' => 18,  // SEMESTER
            'F' => 24,  // STATUS MASA STUDI
            'G' => 30,  // EMAIL
            'H' => 22,  // NO WA
            'I' => 26,  // TANGGAL BUAT AKUN
            'J' => 28,  // LAMA BELUM MENGAJUKAN
            'K' => 16,  // STATUS AKUN
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestCol = 'K';

        // Header styling
        $sheet->getStyle("A1:{$highestCol}1")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 10,
                'name' => 'Arial',
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E293B'], // Slate-800
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        if ($highestRow > 1) {
            // Data borders and alignment
            $sheet->getStyle("A2:{$highestCol}{$highestRow}")->applyFromArray([
                'font' => [
                    'size' => 9,
                    'name' => 'Arial',
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'E2E8F0'],
                    ],
                ],
            ]);

            // Center alignment for specific columns
            $sheet->getStyle("A2:A{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C2:F{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("H2:K{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        return [];
    }
}
