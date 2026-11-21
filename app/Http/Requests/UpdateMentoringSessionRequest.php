<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateMentoringSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function prepareForValidation()
    {
        if ($this->has('scheduled_date')) {
            if ($this->has('scheduled_hour') && $this->has('scheduled_minute')) {
                $this->merge([
                    'scheduled_at' => $this->scheduled_date . ' ' . $this->scheduled_hour . ':' . $this->scheduled_minute
                ]);
            } elseif ($this->has('scheduled_time')) {
                $this->merge([
                    'scheduled_at' => $this->scheduled_date . ' ' . $this->scheduled_time
                ]);
            }

            if (!$this->filled('scheduled_end_at')) {
                if ($this->filled('scheduled_end_hour') && $this->filled('scheduled_end_minute')) {
                    $this->merge([
                        'scheduled_end_at' => $this->scheduled_date . ' ' . $this->scheduled_end_hour . ':' . $this->scheduled_end_minute,
                    ]);
                } elseif ($this->filled('scheduled_end_time')) {
                    $this->merge([
                        'scheduled_end_at' => $this->scheduled_date . ' ' . $this->scheduled_end_time,
                    ]);
                }
            }
        }
    }

    public function rules(): array
    {
        return [
            'scheduled_at' => 'required|date|after:now',
            'scheduled_end_at' => 'required|date|after:scheduled_at',
            'topic' => 'required|string|max:255',
            'type' => 'required|in:offline,online',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'apply_to_group' => 'nullable|boolean',
            'add_thesis_ids' => 'nullable|array',
            'add_thesis_ids.*' => 'required|integer|exists:theses,id',
        ];
    }

    public function messages(): array
    {
        return [
            'scheduled_at.required' => 'Tanggal dan jam mulai bimbingan wajib diisi.',
            'scheduled_at.after' => 'Waktu mulai bimbingan baru harus di masa mendatang.',
            'scheduled_end_at.required' => 'Jam selesai bimbingan wajib diisi.',
            'scheduled_end_at.date' => 'Format jam selesai bimbingan tidak valid.',
            'scheduled_end_at.after' => 'Jam selesai bimbingan harus lebih akhir dari jam mulai.',
            'topic.required' => 'Topik bimbingan wajib diisi.',
            'type.required' => 'Tipe bimbingan wajib dipilih.',
            'add_thesis_ids.*.exists' => 'Salah satu mahasiswa tambahan yang dipilih tidak ditemukan.',
        ];
    }
}
