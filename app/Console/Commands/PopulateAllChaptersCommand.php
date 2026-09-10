<?php

namespace App\Console\Commands;

use App\Services\UnsubRepositorySyncService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class PopulateAllChaptersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'repository:populate-chapters {--download : Unduh file fisik PDF ke disk lokal (default: simpan URL proxy streaming langsung)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populasi dan perkaya seluruh naskah BAB 1 s.d BAB 5 untuk arsip skripsi FASILKOM dari API repositori UNSUB';

    /**
     * Execute the console command.
     */
    public function handle(UnsubRepositorySyncService $service): int
    {
        $this->info('===============================================================');
        $this->info(' POPULASI BAB 1 s.d BAB 5 REPOSITORI FASILKOM UNSUB -> SIBIMA');
        $this->info(' Mode Pilihan 1: Buka Penuh Akses Semua Bab Naskah Skripsi');
        $this->info('===============================================================');

        $downloadPdf = (bool) $this->option('download');
        if ($downloadPdf) {
            $this->comment('Mode: Download file fisik PDF ke storage lokal.');
        } else {
            $this->comment('Mode: Simpan URL streaming cepat (Akses instan tanpa membebani kuota storage).');
        }

        $this->newLine();
        $this->info('1. Mengambil data dokumen dari API Repositori UNSUB...');
        $allDocs = $service->fetchDocuments();
        if (empty($allDocs)) {
            $this->error('Gagal mengambil data dari API Repositori UNSUB.');
            return SymfonyCommand::FAILURE;
        }

        $fasilkomDocs = $service->filterFasilkom($allDocs);
        $totalFasilkom = count($fasilkomDocs);
        $this->info("   -> Ditemukan {$totalFasilkom} dokumen skripsi FASILKOM.");

        if ($totalFasilkom === 0) {
            $this->warn('Tidak ada dokumen FASILKOM yang ditemukan.');
            return SymfonyCommand::SUCCESS;
        }

        $this->newLine();
        $this->info('2. Memproses dan memperkaya BAB 1 s.d BAB 6...');
        $bar = $this->output->createProgressBar($totalFasilkom);
        $bar->start();

        $stats = [
            'created' => 0,
            'enriched' => 0,
            'skipped' => 0,
            'has_bab1' => 0,
            'has_bab2' => 0,
            'has_bab3' => 0,
            'has_bab4' => 0,
            'has_bab5' => 0,
            'has_bab6' => 0,
        ];

        foreach ($fasilkomDocs as $doc) {
            $res = $service->syncDocument($doc, $downloadPdf);
            $status = $res['status'] ?? 'skipped';
            if (isset($stats[$status])) {
                $stats[$status]++;
            }
            if (!empty($res['has_bab1'])) $stats['has_bab1']++;
            if (!empty($res['has_bab2'])) $stats['has_bab2']++;
            if (!empty($res['has_bab3'])) $stats['has_bab3']++;
            if (!empty($res['has_bab4'])) $stats['has_bab4']++;
            if (!empty($res['has_bab5'])) $stats['has_bab5']++;
            if (!empty($res['has_bab6'])) $stats['has_bab6']++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info('===============================================================');
        $this->info(' HASIL POPULASI BAB 1 s.d BAB 6');
        $this->info('===============================================================');
        $this->table(
            ['Metrik Bab / Data', 'Jumlah Dokumen Terhubung'],
            [
                ['Dokumen FASILKOM Diproses', $totalFasilkom],
                ['Data Eksisting Diperkaya (Enriched)', $stats['enriched']],
                ['Data Baru Dibuat (Created)', $stats['created']],
                ['Naskah BAB 1 (Pendahuluan)', $stats['has_bab1']],
                ['Naskah BAB 2 (Tinjauan Pustaka)', $stats['has_bab2']],
                ['Naskah BAB 3 (Metodologi Penelitian)', $stats['has_bab3']],
                ['Naskah BAB 4 (Hasil dan Pembahasan)', $stats['has_bab4']],
                ['Naskah BAB 5 (Implementasi / Kesimpulan)', $stats['has_bab5']],
                ['Naskah BAB 6 (Kesimpulan dan Saran)', $stats['has_bab6']],
            ]
        );

        $this->info('Seluruh naskah bab skripsi berhasil dihubungkan ke repositori SIBIMA!');
        return SymfonyCommand::SUCCESS;
    }
}
