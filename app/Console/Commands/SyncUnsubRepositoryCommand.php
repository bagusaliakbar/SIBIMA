<?php

namespace App\Console\Commands;

use App\Services\UnsubRepositorySyncService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class SyncUnsubRepositoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'repository:sync-unsub {--no-download : Hanya simpan link proxy stream tanpa mengunduh fisik ke storage}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi data katalog pustaka SIBIMA dari repositori UNSUB (Khusus Fakultas Ilmu Komputer & File BAB I & BAB II)';

    /**
     * Execute the console command.
     */
    public function handle(UnsubRepositorySyncService $service): int
    {
        $this->info('===============================================================');
        $this->info(' SINKRONISASI REPOSITORI UNIVERSITAS SUBANG -> SIBIMA');
        $this->info(' Khusus: Fakultas Ilmu Komputer (FASILKOM)');
        $this->info(' Filter Naskah: BAB I & BAB II (PDF) untuk efisiensi penyimpanan');
        $this->info('===============================================================');

        $downloadPdf = !$this->option('no-download');
        if ($downloadPdf) {
            $this->comment('Mode: Download file fisik BAB 1 & BAB 2 ke public storage (theses_bab1/ & theses_bab2/)');
        } else {
            $this->comment('Mode: Hanya simpan URL proxy streaming (0 bytes local storage)');
        }

        $this->newLine();
        $this->info('1. Menghubungi API Repositori UNSUB (https://repository.unsub.ac.id/api/documents)...');

        $allDocs = $service->fetchDocuments();
        if (empty($allDocs)) {
            $this->error('Gagal mengambil data dari API Repositori UNSUB atau endpoint tidak merespons.');
            return SymfonyCommand::FAILURE;
        }

        $totalAll = count($allDocs);
        $this->info("   -> Ditemukan {$totalAll} total dokumen repositori universitas.");

        $this->newLine();
        $this->info('2. Menyaring dokumen: HANYA Fakultas Ilmu Komputer...');
        $fasilkomDocs = $service->filterFasilkom($allDocs);
        $totalFasilkom = count($fasilkomDocs);
        $ignoredCount = $totalAll - $totalFasilkom;

        $this->info("   -> Ditemukan {$totalFasilkom} dokumen skripsi FASILKOM.");
        $this->comment("   -> {$ignoredCount} dokumen dari fakultas lain diabaikan secara ketat.");

        if ($totalFasilkom === 0) {
            $this->warn('Tidak ada dokumen FASILKOM yang ditemukan.');
            return SymfonyCommand::SUCCESS;
        }

        $this->newLine();
        $this->info('3. Memulai sinkronisasi data & ekstraksi file BAB I & BAB II...');
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
            if (!empty($res['has_bab1'])) {
                $stats['has_bab1']++;
            }
            if (!empty($res['has_bab2'])) {
                $stats['has_bab2']++;
            }
            if (!empty($res['has_bab3'])) {
                $stats['has_bab3']++;
            }
            if (!empty($res['has_bab4'])) {
                $stats['has_bab4']++;
            }
            if (!empty($res['has_bab5'])) {
                $stats['has_bab5']++;
            }
            if (!empty($res['has_bab6'])) {
                $stats['has_bab6']++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info('===============================================================');
        $this->info(' HASIL SINKRONISASI REPOSITORI UNSUB');
        $this->info('===============================================================');
        $this->table(
            ['Metrik', 'Jumlah'],
            [
                ['Total Dokumen Universitas Terdeteksi', $totalAll],
                ['Dokumen Fakultas Lain (Diabaikan)', $ignoredCount],
                ['Dokumen FASILKOM Diproses', $totalFasilkom],
                ['Data Baru Ditambahkan (Created)', $stats['created']],
                ['Data Eksisting Diperkaya (Enriched)', $stats['enriched']],
                ['Dokumen dengan File BAB I', $stats['has_bab1']],
                ['Dokumen dengan File BAB II', $stats['has_bab2']],
                ['Dokumen dengan File BAB III', $stats['has_bab3']],
                ['Dokumen dengan File BAB IV', $stats['has_bab4']],
                ['Dokumen dengan File BAB V', $stats['has_bab5']],
                ['Dokumen dengan File BAB VI', $stats['has_bab6']],
            ]
        );

        $this->info('Sinkronisasi selesai dengan sukses!');
        return SymfonyCommand::SUCCESS;
    }
}
