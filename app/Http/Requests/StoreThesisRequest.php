<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreThesisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(Auth::user()->role, ['mahasiswa', 'admin', 'kaprodi']);
    }

    public function rules(): array
    {
        $isStaff = in_array(Auth::user()->role, ['admin', 'kaprodi'], true);
        $studentId = $isStaff ? $this->student_id : Auth::id();

        $rules = [
            'title'    => 'required|string|max:255',
            'abstract' => 'required|string',
            'requested_pembimbing1_id' => 'nullable|exists:users,id',
            'requested_pembimbing2_id' => 'nullable|exists:users,id|different:requested_pembimbing1_id',
        ];

        if ($isStaff) {
            $rules['student_id'] = [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($studentId) {
                    if ($studentId) {
                        $hasActiveThesis = \App\Models\Thesis::where('student_id', $studentId)
                            ->whereIn('status', ['pending', 'active'])
                            ->exists();

                        if ($hasActiveThesis) {
                            $fail('Mahasiswa ini sudah memiliki pengajuan skripsi aktif / pending.');
                        }
                    }
                },
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'requested_pembimbing2_id.different' => 'Usulan Pembimbing 1 dan Pembimbing 2 tidak boleh orang yang sama.',
        ];
    }
}
