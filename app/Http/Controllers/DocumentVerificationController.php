<?php

namespace App\Http\Controllers;

use App\Models\SeminarScheduleDetail;
use App\Models\ThesisDefenseScheduleDetail;
use Illuminate\Http\Request;

class DocumentVerificationController extends Controller
{
    public function verify($token)
    {
        // Try to find in seminar details
        $detail = SeminarScheduleDetail::with(['thesis.student', 'thesis.pembimbing1', 'thesis.pembimbing2', 'examiner1', 'examiner2', 'schedule'])
            ->where('verification_token', $token)
            ->first();

        $type = 'Seminar';

        if (!$detail) {
            // Try to find in defense details
            $detail = ThesisDefenseScheduleDetail::with(['thesis.student', 'thesis.pembimbing1', 'thesis.pembimbing2', 'examiner1', 'examiner2', 'schedule'])
                ->where('verification_token', $token)
                ->first();
            $type = 'Sidang Akhir';
        }

        if (!$detail) {
            return view('verification.invalid');
        }

        return view('verification.document', compact('detail', 'type'));
    }
}
