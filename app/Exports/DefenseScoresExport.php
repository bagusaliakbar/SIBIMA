<?php

namespace App\Exports;

use App\Models\ThesisDefenseScheduleDetail;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DefenseScoresExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $waveId;

    public function __construct($waveId = null)
    {
        $this->waveId = $waveId;
    }

    public function collection()
    {
        return ThesisDefenseScheduleDetail::with(['thesis.student', 'thesis.pembimbing1', 'examiner1', 'examiner2', 'revisions'])
            ->whereHas('thesis')
            ->when($this->waveId, function($q) {
                $q->whereHas('thesis.defenseApplication', function($query) {
                    $query->where('wave_id', $this->waveId);
                });
            })
            ->get();
    }

    public function headings(): array
    {
        return [
            'NO',
            'NPM',
            'NAMA MAHASISWA',
            'JUDUL SKRIPSI',
            'TIM PENELAAH',
            'PRESENTASI (25%)',
            'NASKAH TA (40%)',
            'PENULISAN (35%)',
            'TOTAL NILAI',
            'NILAI AKHIR',
            'NILAI HURUF'
        ];
    }

    private $rowNumber = 0;

    public function map($detail): array
    {
        $this->rowNumber++;
        
        $revP1 = $detail->revisions->where('examiner_id', $detail->thesis->pembimbing1_id)->first();
        $revE1 = $detail->revisions->where('examiner_id', $detail->examiner1_id)->first();
        $revE2 = $detail->revisions->where('examiner_id', $detail->examiner2_id)->first();

        $calc = function($rev) {
            if (!$rev || $rev->score_presentation === null) return null;
            return ($rev->score_presentation * 0.25) + ($rev->score_explanation * 0.40) + ($rev->score_writing * 0.35);
        };

        $scoreP1 = $calc($revP1);
        $scoreE1 = $calc($revE1);
        $scoreE2 = $calc($revE2);

        $scores = collect([$scoreP1, $scoreE1, $scoreE2])->filter(fn($s) => $s !== null);
        $totalScore = $scores->sum();
        $finalScore = $scores->count() > 0 ? $totalScore / $scores->count() : 0;

        $getGrade = function($s) {
            if ($s >= 80) return 'A';
            if ($s >= 75) return 'B+';
            if ($s >= 70) return 'B';
            if ($s >= 65) return 'C+';
            if ($s >= 60) return 'C';
            if ($s >= 50) return 'D';
            return 'E';
        };
        $finalGrade = $scores->count() > 0 ? $getGrade($finalScore) : '-';

        $examiners = "P1: " . ($detail->thesis->pembimbing1->name ?? '-') . "\n" .
                     "U1: " . ($detail->examiner1->name ?? '-') . "\n" .
                     "U2: " . ($detail->examiner2->name ?? '-');

        return [
            $this->rowNumber,
            $detail->thesis->student->identifier,
            $detail->thesis->student->name,
            $detail->thesis->title,
            $examiners,
            ($revP1->score_presentation ?? '-') . " | " . ($revE1->score_presentation ?? '-') . " | " . ($revE2->score_presentation ?? '-'),
            ($revP1->score_explanation ?? '-') . " | " . ($revE1->score_explanation ?? '-') . " | " . ($revE2->score_explanation ?? '-'),
            ($revP1->score_writing ?? '-') . " | " . ($revE1->score_writing ?? '-') . " | " . ($revE2->score_writing ?? '-'),
            number_format($totalScore, 1),
            number_format($finalScore, 1),
            $finalGrade
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('E')->getAlignment()->setWrapText(true);
        $sheet->getStyle('F:H')->getAlignment()->setWrapText(true);
        
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0']
                ],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
            ],
        ];
    }
}
