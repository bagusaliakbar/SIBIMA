<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class BulkUpdateMentoringSessionStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']);
    }

    public function rules(): array
    {
        return [
            'session_ids' => 'required|array|min:1',
            'session_ids.*' => 'required|integer|exists:mentoring_sessions,id',
            'status' => 'required|in:absent,completed,approved',
            'feedback' => 'nullable|string',
            'feedback_document_url' => 'nullable|url|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'session_ids.required' => 'Pilih minimal satu mahasiswa bimbingan.',
            'session_ids.min' => 'Pilih minimal satu mahasiswa bimbingan.',
            'status.required' => 'Status aksi massal wajib ditentukan.',
            'status.in' => 'Status yang dipilih tidak valid.',
            'feedback_document_url.url' => 'Format link dokumen revisi harus berupa URL yang valid (misal: https://drive.google.com/...).',
        ];
    }
}
