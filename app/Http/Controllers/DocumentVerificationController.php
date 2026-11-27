<?php

namespace App\Http\Controllers;

use App\Models\SeminarApplication;
use App\Models\SeminarScheduleDetail;
use App\Models\ThesisDefenseScheduleDetail;

class DocumentVerificationController extends Controller
{
    public function verify($token)
    {
        // 1. Check Seminar Berita Acara
        $detail = SeminarScheduleDetail::with(['thesis.student', 'thesis.pembimbing1', 'thesis.pembimbing2', 'examiner1', 'examiner2', 'schedule'])
            ->where('verification_token', $token)
            ->first();

        $type = 'Seminar';

        // 2. Check Defense Berita Acara
        if (!$detail) {
            $detail = ThesisDefenseScheduleDetail::with(['thesis.student', 'thesis.pembimbing1', 'thesis.pembimbing2', 'examiner1', 'examiner2', 'schedule'])
                ->where('verification_token', $token)
                ->first();
            $type = 'Sidang Akhir';
        }

        if ($detail) {
            return view('verification.document', compact('detail', 'type'));
        }

        // 3. Check Seminar Registration Form Application
        $application = SeminarApplication::with(['thesis.student', 'thesis.pembimbing1', 'thesis.pembimbing2', 'wave'])
            ->where('verification_token', $token)
            ->first();

        if ($application) {
            $formData = $application->getResolvedFormData();
            $integrityCode = $application->getIntegrityCode();

            return view('verification.seminar_application', compact('application', 'formData', 'integrityCode'));
        }

        return view('verification.invalid');
    }
}
