<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\MentoringSession;
use App\Models\SeminarApplication;
use App\Models\ThesisDefenseApplication;
use App\Models\SeminarRevisionMessage;
use App\Models\ThesisDefenseRevisionMessage;

class DownloadController extends Controller
{
    /**
     * Download a private file securely.
     */
    public function downloadPrivateFile(Request $request)
    {
        $path = $request->query('path');

        if (!$path || !Storage::disk(config('filesystems.default'))->exists($path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $user = Auth::user();
        $isAdmin = ($user->role === 'admin' || $user->role === 'kaprodi');
        $inline = $request->boolean('inline') || $request->boolean('preview');

        // 1. Mentoring Session Documents
        if (str_starts_with($path, 'session-documents/')) {
            $session = MentoringSession::where('document_path', $path)->first();
            if ($session) {
                $isAllowed = $isAdmin ||
                    ($user->role === 'mahasiswa' && $session->thesis->student_id === $user->id) ||
                    ($user->role === 'dosen' && ($session->dosen_id === $user->id || $session->thesis->pembimbing1_id === $user->id || $session->thesis->pembimbing2_id === $user->id));
                
                if ($isAllowed) {
                    return $this->deliverFile($path, $session->document_original_name, $inline);
                }
            }
        }

        // 2. Seminar Application Files
        if (str_starts_with($path, 'SeminarApplication_files/') || str_starts_with($path, 'seminar_applications/')) {
            $application = SeminarApplication::where(function($query) use ($path) {
                $query->where('file_acc_pembimbing', $path)
                      ->orWhere('file_pembayaran', $path)
                      ->orWhere('file_kartu_bimbingan', $path)
                      ->orWhere('file_skripsi', $path)
                      ->orWhere('file_formulir', $path);
            })->first();

            if ($application) {
                $isAllowed = $isAdmin ||
                    ($user->role === 'mahasiswa' && $application->thesis && $application->thesis->student_id === $user->id) ||
                    ($user->role === 'dosen' && $application->thesis && (
                        $application->thesis->pembimbing1_id === $user->id || 
                        $application->thesis->pembimbing2_id === $user->id ||
                        $application->thesis->seminarScheduleDetails()->where(function($q) use ($user) {
                            $q->where('examiner1_id', $user->id)->orWhere('examiner2_id', $user->id);
                        })->exists() ||
                        $application->thesis->defenseScheduleDetails()->where(function($q) use ($user) {
                            $q->where('examiner1_id', $user->id)->orWhere('examiner2_id', $user->id);
                        })->exists()
                    ));
                
                if ($isAllowed) {
                    $student = $application->thesis->student;
                    $npm = $student->identifier;
                    $name = str_replace(' ', '_', $student->name);
                    
                    $fieldName = 'file';
                    foreach (['file_acc_pembimbing', 'file_pembayaran', 'file_kartu_bimbingan', 'file_skripsi', 'file_formulir'] as $f) {
                        if ($application->$f === $path) {
                            $fieldName = $f;
                            break;
                        }
                    }
                    $extension = pathinfo($path, PATHINFO_EXTENSION);
                    $downloadName = "{$npm}_{$name}_{$fieldName}.{$extension}";

                    return $this->deliverFile($path, $downloadName, $inline);
                }
            }
        }

        // 3. Thesis Defense Application Files
        if (str_starts_with($path, 'ThesisDefenseApplication_files/') || str_starts_with($path, 'thesis_defense_applications/')) {
            $files = [
                'file_formulir', 'file_transkrip', 'file_acc_pembimbing', 'file_logbook', 'file_pembayaran',
                'file_skripsi', 'file_ktm', 'file_pkkmb_univ', 'file_pkkmb_fak', 'file_makrab',
                'file_cisco', 'file_workshop', 'file_organisasi', 'file_toefl', 'file_kewirausahaan',
                'file_tahsin', 'file_komputer', 'file_perpus_pinjam', 'file_perpus_sumbang', 'file_ijazah'
            ];
            
            $application = ThesisDefenseApplication::where(function($query) use ($path, $files) {
                foreach ($files as $file) {
                    $query->orWhere($file, $path);
                }
            })->first();

            if ($application) {
                $isAllowed = $isAdmin ||
                    ($user->role === 'mahasiswa' && $application->thesis && $application->thesis->student_id === $user->id) ||
                    ($user->role === 'dosen' && $application->thesis && (
                        $application->thesis->pembimbing1_id === $user->id || 
                        $application->thesis->pembimbing2_id === $user->id ||
                        $application->thesis->seminarScheduleDetails()->where(function($q) use ($user) {
                            $q->where('examiner1_id', $user->id)->orWhere('examiner2_id', $user->id);
                        })->exists() ||
                        $application->thesis->defenseScheduleDetails()->where(function($q) use ($user) {
                            $q->where('examiner1_id', $user->id)->orWhere('examiner2_id', $user->id);
                        })->exists()
                    ));
                
                if ($isAllowed) {
                    $student = $application->thesis->student;
                    $npm = $student->identifier;
                    $name = str_replace(' ', '_', $student->name);
                    
                    $fieldName = 'file';
                    foreach ($files as $f) {
                        if ($application->$f === $path) {
                            $fieldName = $f;
                            break;
                        }
                    }
                    $extension = pathinfo($path, PATHINFO_EXTENSION);
                    $downloadName = "{$npm}_{$name}_{$fieldName}.{$extension}";

                    return $this->deliverFile($path, $downloadName, $inline);
                }
            }
        }

        // 4. Seminar Revisions
        if (str_starts_with($path, 'seminarrevisions/') || str_starts_with($path, 'seminarrevisionmessages/')) {
            $message = SeminarRevisionMessage::where('file_path', $path)->first();
            if ($message && $message->revision) {
                $revision = $message->revision;
                $detail = $revision->detail;
                if ($detail && $detail->thesis) {
                    $thesis = $detail->thesis;
                    $isAllowed = $isAdmin ||
                        ($user->role === 'mahasiswa' && $thesis->student_id === $user->id) ||
                        ($user->role === 'dosen' && (
                            $revision->examiner_id === $user->id ||
                            $thesis->pembimbing1_id === $user->id ||
                            $thesis->pembimbing2_id === $user->id ||
                            $detail->examiner1_id === $user->id ||
                            $detail->examiner2_id === $user->id
                        ));
                    
                    if ($isAllowed) {
                        $student = $thesis->student;
                        $npm = $student->identifier;
                        $name = str_replace(' ', '_', $student->name);
                        $extension = pathinfo($path, PATHINFO_EXTENSION);
                        
                        $downloadName = "{$npm}_{$name}_revisi_seminar_" . $message->id . ".{$extension}";
                        return $this->deliverFile($path, $downloadName, $inline);
                    }
                }
            }
        }

        // 5. Thesis Defense Revisions
        if (str_starts_with($path, 'thesisdefenserevisions/') || str_starts_with($path, 'thesisdefenserevisionmessages/')) {
            $message = ThesisDefenseRevisionMessage::where('file_path', $path)->first();
            if ($message && $message->revision) {
                $revision = $message->revision;
                $detail = $revision->detail;
                if ($detail && $detail->thesis) {
                    $thesis = $detail->thesis;
                    $isAllowed = $isAdmin ||
                        ($user->role === 'mahasiswa' && $thesis->student_id === $user->id) ||
                        ($user->role === 'dosen' && (
                            $revision->examiner_id === $user->id ||
                            $thesis->pembimbing1_id === $user->id ||
                            $thesis->pembimbing2_id === $user->id ||
                            $detail->examiner1_id === $user->id ||
                            $detail->examiner2_id === $user->id
                        ));
                    
                    if ($isAllowed) {
                        $student = $thesis->student;
                        $npm = $student->identifier;
                        $name = str_replace(' ', '_', $student->name);
                        $extension = pathinfo($path, PATHINFO_EXTENSION);
                        
                        $downloadName = "{$npm}_{$name}_revisi_sidang_" . $message->id . ".{$extension}";
                        return $this->deliverFile($path, $downloadName, $inline);
                    }
                }
            }
        }

        // 6. Graduation & Clearance Files
        if (str_starts_with($path, 'graduation_files/')) {
            $graduation = \App\Models\Graduation::where(function($query) use ($path) {
                $query->where('final_thesis_file', $path)
                      ->orWhere('journal_article_file', $path)
                      ->orWhere('plagiarism_file', $path);
            })->first();

            if ($graduation) {
                $isAllowed = $isAdmin || ($user->role === 'mahasiswa' && $graduation->student_id === $user->id);
                if ($isAllowed) {
                    $student = $graduation->student;
                    $npm = $student->identifier ?? 'MHS';
                    $name = str_replace(' ', '_', $student->name ?? 'Mahasiswa');
                    $extension = pathinfo($path, PATHINFO_EXTENSION);
                    $downloadName = "{$npm}_{$name}_berkas_yudisium.{$extension}";
                    return $this->deliverFile($path, $downloadName, $inline);
                }
            }
        }

        // 7. Templates
        if (str_starts_with($path, 'seminar_templates/')) {
            $template = \App\Models\SeminarTemplate::where('file_path', $path)->first();
            return $this->deliverFile($path, $template ? $template->original_name : null, $inline);
        }

        if (str_starts_with($path, 'thesis_defense_templates/')) {
            $template = \App\Models\ThesisDefenseTemplate::where('file_path', $path)->first();
            return $this->deliverFile($path, $template ? $template->original_name : null, $inline);
        }

        // 8. Fallback for admin if path didn't match any standard prefixes but they are authorized
        if ($isAdmin) {
            return $this->deliverFile($path, null, $inline);
        }

        abort(403, 'Anda tidak memiliki izin untuk mengunduh file ini.');
    }

    /**
     * Helper to deliver file as either inline preview or forced attachment download.
     */
    protected function deliverFile(string $path, ?string $downloadName = null, bool $inline = false)
    {
        $disk = Storage::disk(config('filesystems.default'));
        if ($inline) {
            $fullPath = $disk->path($path);
            if (file_exists($fullPath)) {
                $headers = [
                    'Content-Disposition' => 'inline' . ($downloadName ? '; filename="' . $downloadName . '"' : ''),
                ];
                return response()->file($fullPath, $headers);
            }
        }

        return $downloadName ? $disk->download($path, $downloadName) : $disk->download($path);
    }
}
