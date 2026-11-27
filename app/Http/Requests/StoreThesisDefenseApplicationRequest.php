<?php

namespace App\Http\Requests;

use App\Models\Thesis;
use App\Models\ThesisDefenseApplication;
use App\Models\Wave;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreThesisDefenseApplicationRequest extends FormRequest
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
        $existingApplication = ThesisDefenseApplication::where('thesis_id', $thesis->id)
            ->when($activeWave, fn ($q) => $q->where('wave_id', $activeWave->id))
            ->first();

        if (!$existingApplication) {
            $existingApplication = ThesisDefenseApplication::where('thesis_id', $thesis->id)
                ->latest()
                ->first();
        }

        // 19 Google Drive files (file_formulir is now generated automatically from the digital form)
        $files = [
            'file_transkrip', 'file_acc_pembimbing', 'file_logbook', 'file_pembayaran',
            'file_skripsi', 'file_ktm', 'file_pkkmb_univ', 'file_pkkmb_fak', 'file_makrab',
            'file_cisco', 'file_workshop', 'file_organisasi', 'file_toefl', 'file_kewirausahaan',
            'file_tahsin', 'file_komputer', 'file_perpus_pinjam', 'file_perpus_sumbang', 'file_ijazah',
        ];

        $safeUrlRule = function (string $attribute, mixed $value, \Closure $fail) {
            if ($value !== null && $value !== '' && !\App\Support\UrlSecurity::isSafeUserSubmittedUrl((string) $value)) {
                $fail('Tautan tidak diizinkan (harus berupa URL publik HTTP/HTTPS yang valid).');
            }
        };

        $rules = [];
        foreach ($files as $file) {
            $isRejected = isset($existingApplication->file_reviews[$file]['status']) && $existingApplication->file_reviews[$file]['status'] === 'rejected';
            $isMissing = !$existingApplication || !$existingApplication->$file;
            $hasSession = session()->has('defense_uploads.path.' . $file);

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
            'file_formulir' => 'link formulir pendaftaran sidang',
            'file_transkrip' => 'link transkrip nilai',
            'file_acc_pembimbing' => 'link dokumen ACC pembimbing',
            'file_logbook' => 'link logbook bimbingan',
            'file_pembayaran' => 'link bukti pembayaran',
            'file_skripsi' => 'link draf skripsi final',
            'file_ktm' => 'link KTM',
            'file_pkkmb_univ' => 'link sertifikat PKKMB Universitas',
            'file_pkkmb_fak' => 'link sertifikat PKKMB Fakultas',
            'file_makrab' => 'link sertifikat Makrab',
            'file_cisco' => 'link sertifikat Cisco',
            'file_workshop' => 'link sertifikat Workshop/Seminar',
            'file_organisasi' => 'link sertifikat Organisasi',
            'file_toefl' => 'link sertifikat TOEFL',
            'file_kewirausahaan' => 'link sertifikat Kewirausahaan',
            'file_tahsin' => 'link sertifikat Tahsin',
            'file_komputer' => 'link sertifikat Komputer',
            'file_perpus_pinjam' => 'link surat bebas pinjam perpustakaan',
            'file_perpus_sumbang' => 'link surat bebas sumbang perpustakaan',
            'file_ijazah' => 'link ijazah terakhir',
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
            'statement_agreed' => 'pernyataan kesiapan sidang',
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
