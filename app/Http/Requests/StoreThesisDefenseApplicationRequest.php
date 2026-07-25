<?php

namespace App\Http\Requests;

use App\Models\ThesisDefenseApplication;
use App\Models\Thesis;
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

        $existingApplication = ThesisDefenseApplication::where('thesis_id', $thesis->id)->first();
        
        $files = [
            'file_formulir', 'file_transkrip', 'file_acc_pembimbing', 'file_logbook', 'file_pembayaran',
            'file_skripsi', 'file_ktm', 'file_pkkmb_univ', 'file_pkkmb_fak', 'file_makrab',
            'file_cisco', 'file_workshop', 'file_organisasi', 'file_toefl', 'file_kewirausahaan',
            'file_tahsin', 'file_komputer', 'file_perpus_pinjam', 'file_perpus_sumbang', 'file_ijazah'
        ];

        $rules = [];
        foreach ($files as $file) {
            $isRejected = isset($existingApplication->file_reviews[$file]['status']) && $existingApplication->file_reviews[$file]['status'] === 'rejected';
            $isMissing = !$existingApplication || !$existingApplication->$file;
            $hasSession = session()->has('defense_uploads.path.' . $file);

            if (($isMissing || $isRejected) && !$hasSession) {
                $rules[$file] = 'required|url';
            } else {
                $rules[$file] = 'nullable|url';
            }
        }

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
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'url' => ':attribute harus berupa URL/link Google Drive yang valid.',
        ];
    }
}
