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

        if ($this->has('thesis_ids')) {
            $ids = is_array($this->thesis_ids) ? $this->thesis_ids : explode(',', $this->thesis_ids);
            $ids = array_filter(array_map('trim', $ids));
            $this->merge(['thesis_ids' => array_values($ids)]);
        } elseif ($this->has('thesis_id') && !empty($this->thesis_id)) {
            $this->merge(['thesis_ids' => [$this->thesis_id]]);
        }
    }

    public function rules(): array
    {
        $isStaff = in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']);

        return [
            'scheduled_at' => 'required|date|after:now',
            'topic' => 'required|string|max:255',
            'type' => 'required|in:offline,online',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'thesis_id' => 'nullable',
            'thesis_ids' => $isStaff ? 'required|array|min:1' : 'nullable|array',
            'thesis_ids.*' => 'string',
            'dosen_id' => Auth::user()->role === 'mahasiswa' ? 'required|exists:users,id' : 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'scheduled_at.after' => 'Waktu bimbingan harus di masa mendatang.',
            'thesis_ids.required' => 'Pilih minimal satu mahasiswa untuk dijadwalkan bimbingan.',
            'thesis_ids.min' => 'Pilih minimal satu mahasiswa untuk dijadwalkan bimbingan.',
        ];
    }
}
