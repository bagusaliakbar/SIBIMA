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

        if ($this->has('thesis_ids')) {
            $ids = is_array($this->thesis_ids) ? $this->thesis_ids : explode(',', (string) $this->thesis_ids);
            $ids = array_filter(array_map('trim', array_map('strval', $ids)));
            $this->merge(['thesis_ids' => array_values($ids)]);
        } elseif ($this->has('thesis_id') && !empty($this->thesis_id)) {
            $this->merge(['thesis_ids' => [(string) $this->thesis_id]]);
        }
    }

    public function rules(): array
    {
        $isStaff = in_array(Auth::user()->role, ['dosen', 'admin', 'kaprodi']);

        return [
            'scheduled_at' => 'required|date|after:now',
            'scheduled_end_at' => 'required|date|after:scheduled_at',
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
            'scheduled_at.required' => 'Tanggal dan jam mulai bimbingan wajib diisi.',
            'scheduled_at.after' => 'Waktu mulai bimbingan harus di masa mendatang.',
            'scheduled_end_at.required' => 'Jam selesai bimbingan wajib diisi.',
            'scheduled_end_at.date' => 'Format jam selesai bimbingan tidak valid.',
            'scheduled_end_at.after' => 'Jam selesai bimbingan harus lebih akhir dari jam mulai.',
            'thesis_ids.required' => 'Pilih minimal satu mahasiswa untuk dijadwalkan bimbingan.',
            'thesis_ids.min' => 'Pilih minimal satu mahasiswa untuk dijadwalkan bimbingan.',
        ];
    }
}
