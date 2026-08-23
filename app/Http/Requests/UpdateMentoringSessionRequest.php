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
        }
    }

    public function rules(): array
    {
        return [
            'scheduled_at' => 'required|date',
            'topic' => 'required|string|max:255',
            'type' => 'required|in:offline,online',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'apply_to_group' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'scheduled_at.required' => 'Tanggal dan jam bimbingan wajib diisi.',
            'topic.required' => 'Topik bimbingan wajib diisi.',
            'type.required' => 'Tipe bimbingan wajib dipilih.',
        ];
    }
}
