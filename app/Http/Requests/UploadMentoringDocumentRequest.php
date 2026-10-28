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
            'document' => [
                'required',
                'url',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if (!\App\Support\UrlSecurity::isSafeUserSubmittedUrl((string) $value)) {
                        $fail('Tautan tidak diizinkan (harus berupa URL publik HTTP/HTTPS yang valid).');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'document.required'  => 'Masukkan link Google Drive terlebih dahulu.',
            'document.url'       => 'Format link tidak valid.',
        ];
    }
}
