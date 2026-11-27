<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThesisDefenseApplication extends Model
{
    public const DEFAULT_STUDY_PROGRAM = 'Sistem Informasi / S1';

    protected $fillable = [
        'thesis_id',
        'wave_id',
        'file_formulir',
        'form_data',
        'statement_agreed_at',
        'verification_token',
        'file_transkrip',
        'file_acc_pembimbing',
        'file_logbook',
        'file_pembayaran',
        'file_skripsi',
        'file_ktm',
        'file_pkkmb_univ',
        'file_pkkmb_fak',
        'file_makrab',
        'file_cisco',
        'file_workshop',
        'file_organisasi',
        'file_toefl',
        'file_kewirausahaan',
        'file_tahsin',
        'file_komputer',
        'file_perpus_pinjam',
        'file_perpus_sumbang',
        'file_ijazah',
        'status',
        'admin_feedback',
        'file_reviews',
    ];

    protected $casts = [
        'file_reviews' => 'array',
        'form_data' => 'array',
        'statement_agreed_at' => 'datetime',
    ];

    public function thesis()
    {
        return $this->belongsTo(Thesis::class);
    }

    public function wave()
    {
        return $this->belongsTo(Wave::class);
    }

    /**
     * Determine whether this application uses the built-in digital registration form.
     */
    public function hasDigitalForm(): bool
    {
        return is_array($this->form_data) && !empty($this->form_data);
    }

    /**
     * Ensure this application has a unique verification token persisted in DB.
     */
    public function ensureVerificationToken(): string
    {
        if (!empty($this->verification_token)) {
            return $this->verification_token;
        }

        $token = \Illuminate\Support\Str::random(32);
        $this->verification_token = $token;
        if ($this->exists) {
            $this->saveQuietly();
        }

        return $token;
    }

    /**
     * Compute a deterministic HMAC-SHA256 integrity code bound to defense application & thesis data.
     */
    public function getIntegrityCode(): string
    {
        $resolved = $this->getResolvedFormData();
        $thesis = $this->thesis;
        $token = $this->verification_token ?: 'unissued';

        $payload = implode('|', [
            'thesis_defense_application',
            (string) ($this->id ?? 0),
            mb_strtoupper(trim((string) ($resolved['npm'] ?? ''))),
            mb_strtoupper(trim((string) ($resolved['full_name'] ?? ''))),
            mb_strtoupper(trim((string) ($resolved['thesis_title'] ?? ''))),
            (string) ((int) ($thesis?->acc_sidang_p1 ?? 0)),
            (string) ((int) ($thesis?->acc_sidang_p2 ?? 0)),
            $token,
        ]);

        $hash = strtoupper(substr(hash_hmac('sha256', $payload, (string) config('app.key')), 0, 12));

        return 'SBM-SDG-' . substr($hash, 0, 4) . '-' . substr($hash, 4, 4) . '-' . substr($hash, 8, 4);
    }

    /**
     * Compute default academic period (e.g. Ganjil 2026/2027 or Genap 2025/2026).
     */
    public static function getDefaultAcademicPeriod(): array
    {
        return SeminarApplication::getDefaultAcademicPeriod();
    }

    /**
     * Resolve form data snapshot with safe fallbacks from student profile, previous seminar form, and thesis records.
     */
    public function getResolvedFormData(): array
    {
        $data = is_array($this->form_data) ? $this->form_data : [];
        $student = $this->thesis?->student;
        $thesis = $this->thesis;
        $defaultPeriod = self::getDefaultAcademicPeriod();

        $seminarFallback = [];
        if (empty($data) && $thesis) {
            $latestSeminar = SeminarApplication::where('thesis_id', $thesis->id)
                ->whereNotNull('form_data')
                ->latest()
                ->first();
            if ($latestSeminar && is_array($latestSeminar->form_data)) {
                $seminarFallback = $latestSeminar->form_data;
            }
        }

        return [
            'full_name' => $data['full_name'] ?? $student?->name ?? $seminarFallback['full_name'] ?? '-',
            'birth_place' => $data['birth_place'] ?? $student?->birth_place ?? $seminarFallback['birth_place'] ?? '-',
            'birth_date' => $data['birth_date'] ?? ($student?->birth_date ? $student->birth_date->format('Y-m-d') : ($seminarFallback['birth_date'] ?? null)),
            'npm' => $data['npm'] ?? $student?->identifier ?? $seminarFallback['npm'] ?? '-',
            'study_program' => $data['study_program'] ?? $student?->study_program ?? $seminarFallback['study_program'] ?? self::DEFAULT_STUDY_PROGRAM,
            'semester_number' => $data['semester_number'] ?? $student?->current_semester ?? $seminarFallback['semester_number'] ?? '-',
            'academic_year' => $data['academic_year'] ?? $defaultPeriod['label'],
            'address' => $data['address'] ?? $student?->address ?? $seminarFallback['address'] ?? '-',
            'postal_code' => $data['postal_code'] ?? $student?->postal_code ?? $seminarFallback['postal_code'] ?? '-',
            'home_phone' => $data['home_phone'] ?? $student?->home_phone ?? $seminarFallback['home_phone'] ?? '-',
            'email' => $data['email'] ?? $student?->email ?? $seminarFallback['email'] ?? '-',
            'phone_number' => $data['phone_number'] ?? $student?->phone_number ?? $seminarFallback['phone_number'] ?? '-',
            'thesis_title' => $data['thesis_title'] ?? $thesis?->display_title ?? '-',
            'pembimbing1_name' => $data['pembimbing1_name'] ?? $thesis?->pembimbing1?->name ?? '-',
            'pembimbing2_name' => $data['pembimbing2_name'] ?? $thesis?->pembimbing2?->name ?? '-',
            'submitted_at' => $data['submitted_at'] ?? ($this->statement_agreed_at?->toIso8601String() ?? $this->created_at?->toIso8601String()),
        ];
    }
}
