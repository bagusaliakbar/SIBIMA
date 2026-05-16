<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\MentoringSession;
use App\Models\SeminarApplication;
use App\Models\ThesisDefenseApplication;

class MigrateFilesToPrivate extends Command
{
    protected $signature = 'files:migrate-to-private';
    protected $description = 'Migrate files from public to local (private) storage.';

    public function handle()
    {
        $this->info('Starting file migration to private storage...');

        $this->migrateMentoringSessions();
        $this->migrateSeminarApplications();
        $this->migrateThesisDefenseApplications();

        $this->info('File migration completed successfully.');
    }

    private function migrateFile($path)
    {
        if ($path && Storage::disk('public')->exists($path)) {
            // Check if directory exists in local disk
            $dir = dirname($path);
            if (!Storage::disk('local')->exists($dir)) {
                Storage::disk('local')->makeDirectory($dir);
            }
            
            // Only move if it doesn't already exist in local
            if (!Storage::disk('local')->exists($path)) {
                Storage::disk('local')->put($path, Storage::disk('public')->get($path));
                Storage::disk('public')->delete($path);
                return true;
            }
        }
        return false;
    }

    private function migrateMentoringSessions()
    {
        $sessions = MentoringSession::whereNotNull('document_path')->get();
        $count = 0;
        foreach ($sessions as $session) {
            if ($this->migrateFile($session->document_path)) {
                $count++;
            }
        }
        $this->info("Migrated {$count} Mentoring Session documents.");
    }

    private function migrateSeminarApplications()
    {
        $applications = SeminarApplication::all();
        $count = 0;
        $files = ['file_acc_pembimbing', 'file_pembayaran', 'file_kartu_bimbingan', 'file_skripsi', 'file_formulir'];
        
        foreach ($applications as $app) {
            foreach ($files as $file) {
                if ($this->migrateFile($app->$file)) {
                    $count++;
                }
            }
        }
        $this->info("Migrated {$count} Seminar Application documents.");
    }

    private function migrateThesisDefenseApplications()
    {
        $applications = ThesisDefenseApplication::all();
        $count = 0;
        $files = [
            'file_formulir', 'file_transkrip', 'file_acc_pembimbing', 'file_logbook', 'file_pembayaran',
            'file_skripsi', 'file_ktm', 'file_pkkmb_univ', 'file_pkkmb_fak', 'file_makrab',
            'file_cisco', 'file_workshop', 'file_organisasi', 'file_toefl', 'file_kewirausahaan',
            'file_tahsin', 'file_komputer', 'file_perpus_pinjam', 'file_perpus_sumbang', 'file_ijazah'
        ];
        
        foreach ($applications as $app) {
            foreach ($files as $file) {
                if ($this->migrateFile($app->$file)) {
                    $count++;
                }
            }
        }
        $this->info("Migrated {$count} Thesis Defense Application documents.");
    }
}
