<?php

namespace App\Exports;

use App\Models\Thesis;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
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

class ThesesExport extends DefaultValueBinder implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    ShouldAutoSize, 
    WithStyles, 
    WithTitle, 
    WithEvents, 
    WithCustomValueBinder
{
    protected $search;
    protected $status;
    private $rowNumber = 0;

    public function __construct($search = null, $status = 'all')
    {
        $this->search = $search;
        $this->status = $status;
    }

    public function collection()
    {
        $user = Auth::user();
        $query = Thesis::with([
            'student', 
            'pembimbing1', 
            'pembimbing2', 
            'requestedPembimbing1', 
            'requestedPembimbing2'
        ])
        ->withCount([
            'mentoringSessions as completed_mentoring_count' => function($q) {
                $q->where('status', 'completed')->where('is_absent', false);
            },
            'logbooks'
        ]);

        if ($user && $user->role === 'dosen') {
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

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'NO',
            'NPM',
            'NAMA MAHASISWA',
            'ANGKATAN',
            'NO. HP / WA',
            'EMAIL',
            'RENCANA JUDUL SKRIPSI',
            'JUDUL FINAL',
            'BIDANG / TOPIK',
            'STATUS SKRIPSI',
            'DOSEN PEMBIMBING 1',
            'NIP/NIDN P1',
            'DOSEN PEMBIMBING 2',
            'NIP/NIDN P2',
            'USULAN P1 (MAHASISWA)',
            'USULAN P2 (MAHASISWA)',
            'STATUS ACC SEMINAR (UP)',
            'STATUS ACC SIDANG',
            'BIMBINGAN SELESAI',
            'TOTAL LOGBOOK',
            'TANGGAL PENGAJUAN',
            'TERAKHIR DIPERBARUI',
            'RINGKASAN / ABSTRAK',
        ];
    }

    public function map($thesis): array
    {
        $this->rowNumber++;

        // Status Label
        $statusLabel = match($thesis->status) {
            'pending' => 'Menunggu Pembimbing',
            'active' => 'Bimbingan Aktif',
            'completed' => 'Selesai (Lulus)',
            'rejected' => 'Ditolak',
            default => ucfirst($thesis->status),
        };

        // ACC Seminar Proposal Status
        $accUp = 'Belum ACC';
        if ($thesis->acc_up_p1 && $thesis->acc_up_p2) {
            $accUp = 'Disetujui P1 & P2 (Siap Sempro)';
        } elseif ($thesis->acc_up_p1) {
            $accUp = 'ACC P1 Saja';
        } elseif ($thesis->acc_up_p2) {
            $accUp = 'ACC P2 Saja';
        }

        // ACC Sidang Status
        $accSidang = 'Belum ACC';
        if ($thesis->acc_sidang_p1 && $thesis->acc_sidang_p2) {
            $accSidang = 'Disetujui P1 & P2 (Siap Sidang)';
        } elseif ($thesis->acc_sidang_p1) {
            $accSidang = 'ACC P1 Saja';
        } elseif ($thesis->acc_sidang_p2) {
            $accSidang = 'ACC P2 Saja';
        }

        return [
            $this->rowNumber,
            $thesis->student->identifier ?? '-',
            $thesis->student->name ?? '-',
            $thesis->student->entry_year ?? '-',
            $thesis->student->phone_number ?? '-',
            $thesis->student->email ?? '-',
            $thesis->title ?? '-',
            $thesis->final_title ?? '-',
            $thesis->topic ?? '-',
            $statusLabel,
            $thesis->pembimbing1->name ?? 'Belum Ditugaskan',
            $thesis->pembimbing1->identifier ?? '-',
            $thesis->pembimbing2->name ?? 'Belum Ditugaskan',
            $thesis->pembimbing2->identifier ?? '-',
            $thesis->requestedPembimbing1->name ?? 'Tidak Memilih',
            $thesis->requestedPembimbing2->name ?? 'Tidak Memilih',
            $accUp,
            $accSidang,
            ($thesis->completed_mentoring_count ?? 0) . ' Sesi',
            ($thesis->logbooks_count ?? 0) . ' Catatan',
            $thesis->created_at ? $thesis->created_at->translatedFormat('d/m/Y H:i') : '-',
            $thesis->updated_at ? $thesis->updated_at->translatedFormat('d/m/Y H:i') : '-',
            $thesis->abstract ?? '-',
        ];
    }

    public function bindValue(Cell $cell, $value)
    {
        // Force NPM, Contact, and NIP/NIDN to String format so leading zeros & large numbers are not lost
        if (in_array($cell->getColumn(), ['B', 'E', 'L', 'N'])) {
            $cell->setValueExplicit((string)$value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function title(): string
    {
        return 'Data Skripsi Mahasiswa';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 10,
                    'color' => ['rgb' => 'FFFFFF'],
                    'name' => 'Calibri',
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Freeze Pane on Header Row
                $sheet->freezePane('A2');

                // Enable AutoFilter
                $sheet->setAutoFilter('A1:' . $highestColumn . '1');

                // Header Styling
                $sheet->getRowDimension(1)->setRowHeight(30);
                $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '047857'], // Emerald 700
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                // All Data Rows Styling
                if ($highestRow >= 2) {
                    $sheet->getStyle('A2:' . $highestColumn . $highestRow)->applyFromArray([
                        'font' => [
                            'size' => 10,
                            'name' => 'Calibri',
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'CBD5E1'], // Slate 300
                            ],
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);

                    // Center-align specific structured columns
                    $centerCols = ['A', 'B', 'D', 'E', 'J', 'L', 'N', 'Q', 'R', 'S', 'T', 'U', 'V'];
                    foreach ($centerCols as $col) {
                        $sheet->getStyle($col . '2:' . $col . $highestRow)
                              ->getAlignment()
                              ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }

                    // Wrap text for title & abstract
                    $sheet->getStyle('G2:G' . $highestRow)->getAlignment()->setWrapText(true);
                    $sheet->getStyle('H2:H' . $highestRow)->getAlignment()->setWrapText(true);
                    $sheet->getStyle('W2:W' . $highestRow)->getAlignment()->setWrapText(true);

                    // Set custom column widths for long content
                    $sheet->getColumnDimension('G')->setWidth(42);
                    $sheet->getColumnDimension('H')->setWidth(42);
                    $sheet->getColumnDimension('W')->setWidth(55);
                }
            },
        ];
    }
}
