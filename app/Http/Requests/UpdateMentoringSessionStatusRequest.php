<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateMentoringSessionStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()->role === 'dosen';
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:approved,rejected,completed,absent',
            'feedback' => 'nullable|string',
        ];
    }
}
