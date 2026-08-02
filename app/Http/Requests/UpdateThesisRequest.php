<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateThesisRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = Auth::user();
        if (in_array($user->role, ['admin', 'kaprodi'])) {
            return true;
        }

        if ($user->role === 'mahasiswa') {
            $thesis = $this->route('thesis');
            return $user->id === $thesis->student_id && !$thesis->isAccSidangFinal();
        }

        return false;
    }

    public function rules(): array
    {
        if (Auth::user()->role === 'mahasiswa') {
            return [
                'title' => 'required|string|max:255',
                'abstract' => 'nullable|string',
            ];
        }

        return [
            'final_title' => 'required|string|max:255',
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
