<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanThesisCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'repositories:clean-cache 
                            {--days=7 : Hapus file cache PDF yang lebih tua dari jumlah hari ini (default 7 hari)} 
                            {--all : Hapus seluruh file cache tanpa melihat umur file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bersihkan cache file PDF repositori skripsi untuk menghemat ruang penyimpanan server VPS';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $clearAll = (bool) $this->option('all');
        $disk = Storage::disk('public');

        $baseFolder = 'theses_cache';
        if (!$disk->exists($baseFolder)) {
            $this->info("Folder cache '{$baseFolder}' belum ada atau masih kosong.");
            return Command::SUCCESS;
        }

        $this->info("Memindai cache PDF di penyimpanan '{$baseFolder}'...");

        $files = $disk->allFiles($baseFolder);
        $totalFiles = count($files);

        if ($totalFiles === 0) {
            $this->info("Tidak ada file cache yang perlu dibersihkan (0 file ditemukan).");
            return Command::SUCCESS;
        }

        $now = Carbon::now();
        $cutoffTime = $clearAll ? null : $now->subDays($days)->timestamp;

        $deletedCount = 0;
        $freedBytes = 0;

        foreach ($files as $file) {
            // Only target PDF files or files in theses_cache
            if (!str_ends_with(strtolower($file), '.pdf')) {
                continue;
            }

            $lastModified = $disk->lastModified($file);
            $fileSize = $disk->size($file);

            if ($clearAll || ($cutoffTime !== null && $lastModified < $cutoffTime)) {
                if ($disk->delete($file)) {
                    $deletedCount++;
                    $freedBytes += $fileSize;
                }
            }
        }

        $freedFormatted = $this->formatBytes($freedBytes);

        if ($clearAll) {
            $this->info("Berhasil membersihkan SEMUA cache repositori skripsi.");
        } else {
            $this->info("Berhasil membersihkan cache repositori skripsi yang berusia lebih dari {$days} hari.");
        }

        $this->line(" - Total file dipindai : {$totalFiles}");
        $this->line(" - File dihapus        : {$deletedCount}");
        $this->line(" - Ruang disk dibebaskan: {$freedFormatted}");

        return Command::SUCCESS;
    }

    /**
     * Format bytes into human readable format (KB, MB, GB).
     */
    protected function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }
        if ($bytes < 1048576) {
            return round($bytes / 1024, 2) . ' KB';
        }
        if ($bytes < 1073741824) {
            return round($bytes / 1048576, 2) . ' MB';
        }

        return round($bytes / 1073741824, 2) . ' GB';
    }
}
