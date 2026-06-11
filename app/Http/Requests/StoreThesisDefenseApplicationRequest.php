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
                if ($file === 'file_skripsi') {
                    $rules[$file] = 'required|file|mimes:pdf,doc,docx|max:10240';
                } elseif ($file === 'file_formulir') {
                    $rules[$file] = 'required|file|mimes:pdf,doc,docx|max:2048';
                } else {
                    $rules[$file] = 'required|file|mimes:pdf,jpg,jpeg,png|max:2048';
                }
            } else {
                $rules[$file] = 'nullable|file';
            }
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'file_formulir' => 'formulir pendaftaran sidang',
            'file_transkrip' => 'transkrip nilai',
            'file_acc_pembimbing' => 'dokumen ACC pembimbing',
            'file_logbook' => 'logbook bimbingan',
            'file_pembayaran' => 'bukti pembayaran',
            'file_skripsi' => 'draf skripsi final',
            'file_ktm' => 'KTM',
            'file_pkkmb_univ' => 'sertifikat PKKMB Universitas',
            'file_pkkmb_fak' => 'sertifikat PKKMB Fakultas',
            'file_makrab' => 'sertifikat Makrab',
            'file_cisco' => 'sertifikat Cisco',
            'file_workshop' => 'sertifikat Workshop/Seminar',
            'file_organisasi' => 'sertifikat Organisasi',
            'file_toefl' => 'sertifikat TOEFL',
            'file_kewirausahaan' => 'sertifikat Kewirausahaan',
            'file_tahsin' => 'sertifikat Tahsin',
            'file_komputer' => 'sertifikat Komputer',
            'file_perpus_pinjam' => 'surat bebas pinjam perpustakaan',
            'file_perpus_sumbang' => 'surat bebas sumbang perpustakaan',
            'file_ijazah' => 'ijazah terakhir',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Dokumen :attribute wajib diunggah.',
            'file' => 'Dokumen :attribute harus berupa file.',
            'mimes' => 'Dokumen :attribute harus berformat: :values.',
            'max' => 'Ukuran dokumen :attribute maksimal adalah :max kilobita.',
        ];
    }
}
