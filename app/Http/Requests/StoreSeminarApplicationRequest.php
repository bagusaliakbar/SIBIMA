<?php

namespace App\Http\Requests;

use App\Models\SeminarApplication;
use App\Models\Thesis;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreSeminarApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()->role === 'mahasiswa';
    }

    public function rules(): array
    {
        $thesis = Thesis::where('student_id', Auth::id())->first();
        if (!$thesis) return [];

        $existingApplication = SeminarApplication::where('thesis_id', $thesis->id)->first();
        
        $files = ['file_acc_pembimbing', 'file_pembayaran', 'file_kartu_bimbingan', 'file_skripsi', 'file_formulir'];
        $rules = [];

        foreach ($files as $file) {
            $isRejected = isset($existingApplication->file_reviews[$file]['status']) && $existingApplication->file_reviews[$file]['status'] === 'rejected';
            $isMissing = !$existingApplication || !$existingApplication->$file;
            
            if ($isMissing || $isRejected) {
                if ($file === 'file_skripsi') {
                    $rules[$file] = 'required|file|mimes:pdf,doc,docx|max:10240';
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
