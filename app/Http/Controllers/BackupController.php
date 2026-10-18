<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Services\BackupService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class BackupController extends Controller implements HasMiddleware
{
    protected BackupService $backupService;

    public function __construct(BackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'kaprodi') {
                    abort(403, 'Akses terbatas untuk Administrator dan Kaprodi.');
                }
                return $next($request);
            }),
        ];
    }

    /**
     * Display list of backups and database statistics.
     */
    public function index(Request $request)
    {
        $backups = $this->backupService->getBackupsList();
        $stats = $this->backupService->getStats();

        return view('backups.index', compact('backups', 'stats'));
    }

    /**
     * Trigger manual database backup.
     */
    public function createBackup(Request $request)
    {
        $request->validate([
            'custom_name' => 'nullable|string|max:50|regex:/^[a-zA-Z0-9_\-]+$/',
        ], [
            'custom_name.regex' => 'Nama cadangan hanya boleh berupa huruf, angka, strip (-), atau garis bawah (_).',
        ]);

        try {
            $result = $this->backupService->createBackup($request->input('custom_name'));

            return redirect()->route('admin.backups.index')->with(
                'success',
                "Cadangan database berhasil dibuat! ({$result['filename']} - {$result['size_formatted']}, {$result['tables_count']} tabel)"
            );
        } catch (\Throwable $e) {
            return redirect()->route('admin.backups.index')->with(
                'error',
                'Gagal membuat cadangan database: ' . $e->getMessage()
            );
        }
    }

    /**
     * Download backup file.
     */
    public function download(string $filename)
    {
        $safeFilename = basename($filename);
        $filePath = $this->backupService->getBackupFilePath($safeFilename);

        if (!$filePath || !file_exists($filePath)) {
            abort(404, 'File backup tidak ditemukan.');
        }

        try {
            ActivityLog::log(
                activity: 'Unduh Backup Database',
                description: "Mengunduh file backup: {$safeFilename}",
                module: 'Backup'
            );
        } catch (\Throwable $e) {}

        return response()->download($filePath, $safeFilename, [
            'Content-Type' => 'application/sql',
        ]);
    }

    /**
     * Restore database from an existing backup file.
     */
    public function restore(string $filename)
    {
        $safeFilename = basename($filename);

        try {
            $result = $this->backupService->restoreBackup($safeFilename);

            return redirect()->route('admin.backups.index')->with(
                'success',
                "Restorasi database berhasil! {$result['executed_queries']} kueri SQL berhasil dieksekusi dari file {$safeFilename}."
            );
        } catch (\Throwable $e) {
            return redirect()->route('admin.backups.index')->with(
                'error',
                'Gagal melakukan restorasi database: ' . $e->getMessage()
            );
        }
    }

    /**
     * Upload an external SQL backup file and restore immediately.
     */
    public function uploadAndRestore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|max:102400',
        ], [
            'backup_file.required' => 'Silakan pilih file cadangan (.sql) terlebih dahulu.',
            'backup_file.max' => 'Ukuran file cadangan tidak boleh melebihi 100 MB.',
        ]);

        $file = $request->file('backup_file');
        $ext = strtolower($file->getClientOriginalExtension());

        if (!in_array($ext, ['sql', 'gz'])) {
            return redirect()->route('admin.backups.index')->with(
                'error',
                'Format file tidak valid. Hanya file berformat .sql atau .sql.gz yang diperbolehkan.'
            );
        }

        try {
            $result = $this->backupService->uploadAndRestore($file);

            return redirect()->route('admin.backups.index')->with(
                'success',
                "File backup berhasil diunggah dan database telah berhasil dipulihkan! ({$result['executed_queries']} kueri dieksekusi)"
            );
        } catch (\Throwable $e) {
            return redirect()->route('admin.backups.index')->with(
                'error',
                'Gagal memulihkan database dari file unggahan: ' . $e->getMessage()
            );
        }
    }

    /**
     * Delete an existing backup file.
     */
    public function destroy(string $filename)
    {
        $safeFilename = basename($filename);

        try {
            $this->backupService->deleteBackup($safeFilename);

            return redirect()->route('admin.backups.index')->with(
                'success',
                "File backup {$safeFilename} berhasil dihapus."
            );
        } catch (\Throwable $e) {
            return redirect()->route('admin.backups.index')->with(
                'error',
                'Gagal menghapus file backup: ' . $e->getMessage()
            );
        }
    }
}
