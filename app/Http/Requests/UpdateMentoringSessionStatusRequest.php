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
        ];
    }
}
