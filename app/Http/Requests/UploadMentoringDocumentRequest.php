<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UploadMentoringDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()->role === 'mahasiswa';
    }

    public function rules(): array
    {
        return [
            'document' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar,jpg,jpeg,png|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'document.required'  => 'Pilih file dokumen terlebih dahulu.',
            'document.mimes'     => 'Format file harus: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, ZIP, atau RAR.',
            'document.max'       => 'Ukuran file maksimal 10 MB.',
        ];
    }
}
