<?php

namespace App\Http\Requests;

use App\Models\ThesisDefenseApplication;
use App\Models\Thesis;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreThesisDefenseApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()->role === 'mahasiswa';
    }

    public function rules(): array
    {
        $thesis = Thesis::where('student_id', Auth::id())->first();
        if (!$thesis) return [];

        $existingApplication = ThesisDefenseApplication::where('thesis_id', $thesis->id)->first();
        
        $files = [
            'file_formulir', 'file_transkrip', 'file_acc_pembimbing', 'file_logbook', 'file_pembayaran',
            'file_skripsi', 'file_ktm', 'file_pkkmb_univ', 'file_pkkmb_fak', 'file_makrab',
            'file_cisco', 'file_workshop', 'file_organisasi', 'file_toefl', 'file_kewirausahaan',
            'file_tahsin', 'file_komputer', 'file_perpus_pinjam', 'file_perpus_sumbang', 'file_ijazah'
        ];

        $rules = [];
        foreach ($files as $file) {
            $isRejected = isset($existingApplication->file_reviews[$file]['status']) && $existingApplication->file_reviews[$file]['status'] === 'rejected';
            $isMissing = !$existingApplication || !$existingApplication->$file;

            if ($isMissing || $isRejected) {
                if ($file === 'file_skripsi') {
                    $rules[$file] = 'required|file|mimes:pdf,doc,docx|max:10240';
                } elseif ($file === 'file_formulir') {
                    $rules[$file] = 'required|file|mimes:pdf,doc,docx|max:2048';
                } else {
                    $rules[$file] = 'required|file|mimes:pdf,jpg,jpeg,png|max:2048';
                }
            } else {
                $rules[$file] = 'nullable|file';
            }
        }

        return $rules;
    }
}
