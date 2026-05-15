<?php

namespace App\Http\Controllers;

use App\Models\LetterSetting;
use App\Models\SeminarScheduleDetail;
use App\Models\ThesisDefenseSchedule;
use App\Models\ThesisDefenseScheduleDetail;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DocumentController extends Controller
{
    private function getNextLetterNumber($type)
    {
        return DB::transaction(function () use ($type) {
            $setting = LetterSetting::where('type', $type)->first();
            if (!$setting) return "000/SURAT-TIDAK-DITEMUKAN";

            $setting->increment('last_number');
            $number = str_pad($setting->last_number, 3, '0', STR_PAD_LEFT);
            $month = Carbon::now()->format('m');
            $year = Carbon::now()->format('Y');
            $romans = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
            $romanMonth = $romans[(int)$month];

            return str_replace(
                ['[NUMBER]', '[MONTH]', '[ROMAN_MONTH]', '[YEAR]'],
                [$number, $month, $romanMonth, $year],
                $setting->format
            );
        });
    }

    public function generateSuratTugasSeminar(SeminarScheduleDetail $detail)
    {
        $detail->load(['schedule.chairman', 'thesis.student', 'thesis.pembimbing1', 'thesis.pembimbing2', 'examiner1', 'examiner2']);
        $letterNumber = $this->getNextLetterNumber('surat_tugas_seminar');
        
        $pdf = Pdf::loadView('documents.surat_tugas_pdf', [
            'detail' => $detail,
            'letterNumber' => $letterNumber,
            'title' => 'Surat Tugas Penguji Seminar'
        ]);

        return $pdf->stream("Surat_Tugas_Seminar_{$detail->thesis->student->identifier}.pdf");
    }

    public function generateSKTimPengujiSidang(ThesisDefenseSchedule $schedule)
    {
        $schedule->load(['details.thesis.student', 'details.thesis.pembimbing1', 'details.thesis.pembimbing2', 'details.examiner1', 'details.examiner2', 'chairman', 'moderator']);
        $letterNumber = $this->getNextLetterNumber('sk_penguji_sidang');

        $pdf = Pdf::loadView('documents.sk_penguji_pdf', [
            'schedule' => $schedule,
            'letterNumber' => $letterNumber,
            'title' => 'SK Tim Penguji Sidang'
        ]);

        return $pdf->stream("SK_Tim_Penguji_Sidang_{$schedule->id}.pdf");
    }
}
