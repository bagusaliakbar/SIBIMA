<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AssignPembimbingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'pembimbing1_id' => 'required|exists:users,id',
            'pembimbing2_id' => 'required|exists:users,id|different:pembimbing1_id',
        ];
    }

    public function messages(): array
    {
        return [
            'pembimbing2_id.different' => 'Pembimbing 1 dan Pembimbing 2 tidak boleh orang yang sama.'
        ];
    }
}
