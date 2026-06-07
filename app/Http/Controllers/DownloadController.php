<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\MentoringSession;
use App\Models\SeminarApplication;
use App\Models\ThesisDefenseApplication;

class DownloadController extends Controller
{
    /**
     * Download a private file securely.
     */
    public function downloadPrivateFile(Request $request)
    {
        $path = $request->query('path');

        if (!$path || !Storage::disk('local')->exists($path)) {
            abort(404, 'File tidak ditemukan.');
        }

        // Basic Authorization: 
        // We need to ensure that the user requesting the file is authorized.
        // For simplicity, since the path might not easily map back to the owner without a DB query,
        // we will check if the user is an admin or if they are involved in the file.
        $user = Auth::user();

        if ($user->role === 'admin' || $user->role === 'kaprodi') {
            return response()->download(Storage::disk('local')->path($path));
        }

        // If it's a mentoring session document
        if (str_starts_with($path, 'session-documents/')) {
            $session = MentoringSession::where('document_path', $path)->first();
            if ($session) {
                if ($user->role === 'mahasiswa' && $session->thesis->student_id === $user->id) {
                    return response()->download(Storage::disk('local')->path($path));
                }
                if ($user->role === 'dosen' && ($session->dosen_id === $user->id || $session->thesis->pembimbing1_id === $user->id || $session->thesis->pembimbing2_id === $user->id)) {
                    return response()->download(Storage::disk('local')->path($path));
                }
            }
        }

        // If it's a seminar application document
        if (str_starts_with($path, 'SeminarApplication_files/')) {
            $application = SeminarApplication::where(function($query) use ($path) {
                $query->where('file_acc_pembimbing', $path)
                      ->orWhere('file_pembayaran', $path)
                      ->orWhere('file_kartu_bimbingan', $path)
                      ->orWhere('file_skripsi', $path)
                      ->orWhere('file_formulir', $path);
            })->first();

            if ($application) {
                if ($user->role === 'mahasiswa' && $application->thesis->student_id === $user->id) {
                    return response()->download(Storage::disk('local')->path($path));
                }
                if ($user->role === 'dosen' && ($application->thesis->pembimbing1_id === $user->id || $application->thesis->pembimbing2_id === $user->id)) {
                    return response()->download(Storage::disk('local')->path($path));
                }
            }
        }

        // If it's a thesis defense application document
        if (str_starts_with($path, 'ThesisDefenseApplication_files/')) {
            $application = ThesisDefenseApplication::where(function($query) use ($path) {
                // A massive OR query to find which record owns the file
                $files = [
                    'file_formulir', 'file_transkrip', 'file_acc_pembimbing', 'file_logbook', 'file_pembayaran',
                    'file_skripsi', 'file_ktm', 'file_pkkmb_univ', 'file_pkkmb_fak', 'file_makrab',
                    'file_cisco', 'file_workshop', 'file_organisasi', 'file_toefl', 'file_kewirausahaan',
                    'file_tahsin', 'file_komputer', 'file_perpus_pinjam', 'file_perpus_sumbang', 'file_ijazah'
                ];
                foreach ($files as $file) {
                    $query->orWhere($file, $path);
                }
            })->first();

            if ($application) {
                if ($user->role === 'mahasiswa' && $application->thesis->student_id === $user->id) {
                    return response()->download(Storage::disk('local')->path($path));
                }
                if ($user->role === 'dosen' && ($application->thesis->pembimbing1_id === $user->id || $application->thesis->pembimbing2_id === $user->id)) {
                    return response()->download(Storage::disk('local')->path($path));
                }
            }
        }

        // Fallback for generic templates that anyone authenticated can download
        if (str_starts_with($path, 'seminar_templates/')) {
            return response()->download(Storage::disk('local')->path($path));
        }

        abort(403, 'Anda tidak memiliki izin untuk mengunduh file ini.');
    }
}
