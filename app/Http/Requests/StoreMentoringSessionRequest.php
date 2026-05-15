<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreMentoringSessionRequest extends FormRequest
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
            'scheduled_at' => 'required|date|after:now',
            'topic' => 'required|string|max:255',
            'type' => 'required|in:offline,online',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'thesis_id' => Auth::user()->role === 'dosen' ? 'required' : 'nullable',
            'dosen_id' => Auth::user()->role === 'mahasiswa' ? 'required|exists:users,id' : 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'scheduled_at.after' => 'Waktu bimbingan harus di masa mendatang.',
        ];
    }
}
