<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ThesisRepository;

class FixThesisRepositoryYears extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'repository:fix-years';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perbaiki tahun angkatan naskah skripsi berdasarkan pola NPM mahasiswa';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memeriksa dan menyelaraskan tahun angkatan berdasarkan pola NPM...');

        $repositories = ThesisRepository::whereNotNull('identifier')->get();
        $updatedCount = 0;

        foreach ($repositories as $repo) {
            $extracted = ThesisRepository::extractYearFromIdentifier($repo->identifier);
            if ($extracted && $extracted != $repo->year) {
                $oldYear = $repo->year;
                $repo->year = $extracted;
                $repo->saveQuietly();
                $updatedCount++;
                $this->line("  [✓] {$repo->identifier} ({$repo->name}): Angkatan {$oldYear} => {$extracted}");
            }
        }

        $this->info("Selesai! Total {$updatedCount} repositori berhasil diselaraskan tahun angkatannya.");
        return 0;
    }
}
