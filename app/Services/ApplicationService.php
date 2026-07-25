<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ApplicationService
{
    /**
     * Submit or update an application.
     */
    public function submitApplication($applicationModel, $thesis, $wave, $request, $files, $logTitle, $notifTitle, $indexRoute)
    {
        $existingApplication = $applicationModel::where('thesis_id', $thesis->id)
            ->where('wave_id', $wave->id)
            ->first();

        $data = [
            'thesis_id' => $thesis->id,
            'wave_id' => $wave->id,
            'status' => 'pending',
        ];

        $sessionKey = str_replace('App\\Models\\', '', $applicationModel) === 'SeminarApplication' ? 'seminar_uploads' : 'defense_uploads';

        foreach ($files as $file) {
            if ($request->filled($file)) {
                $data[$file] = $request->input($file);
                
                // Clear rejection status for this file
                if ($existingApplication) {
                    $reviews = $existingApplication->file_reviews;
                    if (isset($reviews[$file])) {
                        unset($reviews[$file]);
                        $existingApplication->file_reviews = $reviews;
                        $existingApplication->save();
                    }
                }
            } elseif ($request->hasFile($file)) {
                $path = $request->file($file)->store(str_replace('App\\Models\\', '', $applicationModel) . '_files', config('filesystems.default'));
                $data[$file] = $path;
                
                // Clear rejection status for this file
                if ($existingApplication) {
                    $reviews = $existingApplication->file_reviews;
                    if (isset($reviews[$file])) {
                        unset($reviews[$file]);
                        $existingApplication->file_reviews = $reviews;
                        $existingApplication->save();
                    }
                }
            } elseif (session()->has($sessionKey . '.path.' . $file)) {
                $tempPath = session()->get($sessionKey . '.path.' . $file);
                if (\Illuminate\Support\Facades\Storage::disk('local')->exists($tempPath)) {
                    $permanentPath = str_replace('App\\Models\\', '', $applicationModel) . '_files/' . basename($tempPath);
                    \Illuminate\Support\Facades\Storage::disk(config('filesystems.default'))->put($permanentPath, \Illuminate\Support\Facades\Storage::disk('local')->get($tempPath));
                    \Illuminate\Support\Facades\Storage::disk('local')->delete($tempPath);
                    $data[$file] = $permanentPath;
                }
            }
        }

        // Clear session files after complete submission
        session()->forget($sessionKey);

        if ($existingApplication) {
            $existingApplication->update($data);
            $appRecord = $existingApplication;
        } else {
            $appRecord = $applicationModel::create($data);
        }

        ActivityLog::log($logTitle, "Mahasiswa " . Auth::user()->name . " mengajukan pendaftaran " . str_replace('Pengajuan ', '', $logTitle) . ".", 'Pendaftaran', $appRecord, [
            'wave' => $wave->name,
            'status' => 'pending'
        ]);

        // Notify Admins & Kaprodi
        $admins = User::whereIn('role', ['admin', 'kaprodi'])->get();
        Notification::send($admins, new GeneralNotification(
            $notifTitle,
            "Mahasiswa " . Auth::user()->name . " melakukan pengajuan baru.",
            route($indexRoute),
            'info'
        ));
    }

    /**
     * Validate an application (Admin).
     */
    public function validateApplication($application, array $data, string $logTitle, string $notifTitle, string $indexRoute)
    {
        $application->update([
            'status' => $data['status'],
            'admin_feedback' => $data['admin_feedback'] ?? null,
            'file_reviews' => $data['file_reviews'] ?? null,
        ]);

        ActivityLog::log($logTitle, "Admin memperbarui status mahasiswa " . $application->thesis->student->name . " menjadi: " . strtoupper($data['status']), 'Pendaftaran', $application, [
            'status' => $data['status'],
            'feedback' => $data['admin_feedback'] ?? null
        ]);

        // Notify Student
        $application->thesis->student->notify(new GeneralNotification(
            $notifTitle,
            "Pengajuan Anda telah " . strtoupper($data['status']),
            route($indexRoute),
            $data['status'] === 'approved' ? 'success' : 'danger'
        ));
    }

    /**
     * Upload template (Admin).
     */
    public function uploadTemplate($templateModel, array $data, $file, $storageDir)
    {
        // Deactivate old templates
        $templateModel::query()->update(['is_active' => false]);

        $path = $file->store($storageDir, config('filesystems.default'));

        return $templateModel::create([
            'title' => $data['title'],
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'is_active' => true,
        ]);
    }

    /**
     * Download application files as ZIP.
     */
    public function downloadZip($application, string $prefix, array $fileMap)
    {
        $studentName = str_replace(' ', '_', $application->thesis->student->name);
        $studentId = $application->thesis->student->identifier;
        $fileName = "{$prefix}_{$studentId}_{$studentName}.zip";

        $zip = new ZipArchive();
        $tempFile = tempnam(sys_get_temp_dir(), 'zip');

        if ($zip->open($tempFile, ZipArchive::CREATE) === TRUE) {
            foreach ($fileMap as $field => $label) {
                if ($application->$field) {
                    if (filter_var($application->$field, FILTER_VALIDATE_URL)) {
                        $zip->addFromString($label . '.url', "[InternetShortcut]\r\nURL=" . $application->$field);
                    } elseif (Storage::disk(config('filesystems.default'))->exists($application->$field)) {
                        $extension = pathinfo($application->$field, PATHINFO_EXTENSION);
                        $zip->addFromString($label . '.' . ($extension ?: 'file'), Storage::disk(config('filesystems.default'))->get($application->$field));
                    }
                }
            }

            $zip->close();
            return $tempFile;
        }

        return false;
    }

    /**
     * Delete rejected application files and the application record.
     */
    public function deleteRejectedApplication($application, array $files)
    {
        if ($application->status !== 'rejected') {
            throw new \Exception('Hanya pengajuan yang ditolak yang dapat dihapus.');
        }

        foreach ($files as $file) {
            if ($application->$file) {
                Storage::disk(config('filesystems.default'))->delete($application->$file);
            }
        }
        
        $application->delete();
    }
}
