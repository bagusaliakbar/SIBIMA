<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreThesisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()->role === 'mahasiswa';
    }

    public function rules(): array
    {
        return [
            'title'    => 'required|string|max:255',
            'abstract' => 'required|string',
            'requested_pembimbing1_id' => 'nullable|exists:users,id',
            'requested_pembimbing2_id' => 'nullable|exists:users,id|different:requested_pembimbing1_id',
        ];
    }

    public function messages(): array
    {
        return [
            'requested_pembimbing2_id.different' => 'Usulan Pembimbing 1 dan Pembimbing 2 tidak boleh orang yang sama.',
        ];
    }
}
