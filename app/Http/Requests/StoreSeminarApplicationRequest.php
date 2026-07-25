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
                $rules[$file] = 'required|url';
            } else {
                $rules[$file] = 'nullable|url';
            }
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'file_acc_pembimbing' => 'link ACC pembimbing',
            'file_pembayaran' => 'link bukti pembayaran',
            'file_kartu_bimbingan' => 'link kartu bimbingan',
            'file_skripsi' => 'link draf proposal/skripsi',
            'file_formulir' => 'link formulir pendaftaran',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'url' => ':attribute harus berupa URL/link Google Drive yang valid.',
        ];
    }
}
