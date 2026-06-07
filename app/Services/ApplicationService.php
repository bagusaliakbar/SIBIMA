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

        foreach ($files as $file) {
            if ($request->hasFile($file)) {
                $path = $request->file($file)->store(str_replace('App\\Models\\', '', $applicationModel) . '_files', 'local');
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
            }
        }

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

        $path = $file->store($storageDir, 'local');

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
                if ($application->$field && Storage::disk('local')->exists($application->$field)) {
                    $extension = pathinfo($application->$field, PATHINFO_EXTENSION);
                    $zip->addFromString($label . '.' . $extension, Storage::disk('local')->get($application->$field));
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
                Storage::disk('local')->delete($application->$file);
            }
        }
        
        $application->delete();
    }
}
