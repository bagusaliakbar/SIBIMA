<?php

namespace App\Http\Requests;

use App\Models\SeminarApplication;
use App\Models\Thesis;
use App\Models\Wave;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreSeminarApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()->role === 'mahasiswa';
    }

    public function rules(): array
    {
        $thesis = Thesis::where('student_id', Auth::id())->first();
        if (!$thesis) return [];

        $activeWave = Wave::getCurrentActive();
        $existingApplication = SeminarApplication::where('thesis_id', $thesis->id)
            ->when($activeWave, fn ($q) => $q->where('wave_id', $activeWave->id))
            ->first();

        if (!$existingApplication) {
            $existingApplication = SeminarApplication::where('thesis_id', $thesis->id)
                ->latest()
                ->first();
        }

        $files = ['file_acc_pembimbing', 'file_pembayaran', 'file_kartu_bimbingan', 'file_skripsi'];
        $rules = [];

        $safeUrlRule = function (string $attribute, mixed $value, \Closure $fail) {
            if ($value !== null && $value !== '' && !\App\Support\UrlSecurity::isSafeUserSubmittedUrl((string) $value)) {
                $fail('Tautan tidak diizinkan (harus berupa URL publik HTTP/HTTPS yang valid).');
            }
        };

        foreach ($files as $file) {
            $isRejected = isset($existingApplication->file_reviews[$file]['status']) && $existingApplication->file_reviews[$file]['status'] === 'rejected';
            $isMissing = !$existingApplication || !$existingApplication->$file;
            $hasSession = session()->has('seminar_uploads.path.' . $file);

            if (($isMissing || $isRejected) && !$hasSession) {
                $rules[$file] = ['required', 'url', $safeUrlRule];
            } else {
                $rules[$file] = ['nullable', 'url', $safeUrlRule];
            }
        }

        // Backward-compatible legacy URL field (optional — replaced by built-in digital form)
        $rules['file_formulir'] = ['nullable', 'url', $safeUrlRule];

        $isFormulirRejected = isset($existingApplication->file_reviews['file_formulir']['status'])
            && $existingApplication->file_reviews['file_formulir']['status'] === 'rejected';

        $needsDigitalForm = !$existingApplication
            || (!$existingApplication->hasDigitalForm() && empty($existingApplication->file_formulir))
            || $isFormulirRejected;

        $formRequirement = $needsDigitalForm ? 'required' : 'nullable';

        $rules['birth_place'] = [$formRequirement, 'string', 'max:120'];
        $rules['birth_date'] = [$formRequirement, 'date', 'before:today'];
        $rules['study_program'] = ['nullable', 'string', 'max:120'];
        $rules['semester_number'] = [$formRequirement, 'integer', 'min:1', 'max:20'];
        $rules['academic_year'] = [$formRequirement, 'string', 'max:50'];
        $rules['address'] = [$formRequirement, 'string', 'max:500'];
        $rules['postal_code'] = [$formRequirement, 'string', 'max:10'];
        $rules['home_phone'] = ['nullable', 'string', 'max:30'];
        $rules['email'] = [$formRequirement, 'email', 'max:255'];
        $rules['phone_number'] = [$formRequirement, 'string', 'max:25'];
        $rules['statement_agreed'] = $needsDigitalForm ? ['accepted'] : ['nullable', 'boolean'];

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'file_acc_pembimbing' => 'link ACC pembimbing',
            'file_pembayaran' => 'link bukti pembayaran',
            'file_kartu_bimbingan' => 'link kartu bimbingan',
            'file_skripsi' => 'link draf proposal/skripsi',
            'file_formulir' => 'link formulir pendaftaran',
            'birth_place' => 'tempat lahir',
            'birth_date' => 'tanggal lahir',
            'study_program' => 'program studi/jenjang',
            'semester_number' => 'semester',
            'academic_year' => 'tahun akademik',
            'address' => 'alamat lengkap',
            'postal_code' => 'kode pos',
            'home_phone' => 'nomor telepon',
            'email' => 'alamat e-mail',
            'phone_number' => 'nomor HP',
            'statement_agreed' => 'pernyataan kesiapan seminar',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'url' => ':attribute harus berupa URL/link Google Drive yang valid.',
            'email' => ':attribute harus berupa alamat e-mail yang valid.',
            'date' => ':attribute harus berupa format tanggal yang valid.',
            'before' => ':attribute harus sebelum hari ini.',
            'accepted' => 'Anda wajib menyetujui :attribute sebelum mengirim pengajuan.',
        ];
    }
}
