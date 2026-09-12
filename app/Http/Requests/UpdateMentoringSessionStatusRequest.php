<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateMentoringSessionStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']);
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:approved,rejected,completed,absent',
            'feedback' => 'nullable|string',
            'feedback_document_url' => 'nullable|url|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'feedback_document_url.url' => 'Format link dokumen feedback harus berupa URL yang valid (misal: https://drive.google.com/...).',
        ];
    }
}
