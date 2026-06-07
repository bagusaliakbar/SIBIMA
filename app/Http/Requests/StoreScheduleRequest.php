<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi';
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'chairman_id' => 'required|exists:users,id',
            'moderator_id' => 'required|exists:users,id',
            'location' => 'nullable|string|max:255',
            'meeting_link' => 'nullable|url',
            'details' => 'required|array|min:1',
            'details.*.start_time' => 'required',
            'details.*.end_time' => 'required',
            'details.*.thesis_id' => 'nullable|exists:theses,id',
            'details.*.activity_name' => 'required_without:details.*.thesis_id|nullable|string|max:255',
            'details.*.examiner1_id' => 'nullable|exists:users,id',
            'details.*.examiner2_id' => 'nullable|exists:users,id',
        ];
    }
}
