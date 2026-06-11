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
            $hasSession = session()->has('seminar_uploads.path.' . $file);
            
            if (($isMissing || $isRejected) && !$hasSession) {
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

    public function attributes(): array
    {
        return [
            'file_acc_pembimbing' => 'dokumen ACC pembimbing',
            'file_pembayaran' => 'bukti pembayaran',
            'file_kartu_bimbingan' => 'kartu bimbingan',
            'file_skripsi' => 'draf proposal/skripsi',
            'file_formulir' => 'formulir pendaftaran',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Dokumen :attribute wajib diunggah.',
            'file' => 'Dokumen :attribute harus berupa file.',
            'mimes' => 'Dokumen :attribute harus berformat: :values.',
            'max' => 'Ukuran dokumen :attribute maksimal adalah :max kilobita.',
        ];
    }
}
