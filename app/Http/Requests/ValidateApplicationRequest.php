<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ValidateApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:approved,rejected',
            'admin_feedback' => 'nullable|string',
            'file_reviews' => 'nullable|array',
        ];
    }
}
