<?php

namespace App\Console\Commands;

use App\Models\FasilkomJournal;
use App\Services\FasilkomJournalService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class SyncFasilkomJournals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'journals:sync-fasilkom';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Harvest and sync articles from Jurnal GLOBAL (FASILKOM UNSUB) via OAI-PMH';

    /**
     * Execute the console command.
     */
    public function handle(FasilkomJournalService $service): int
    {
        $this->info('Memulai pemanenan artikel dari Jurnal GLOBAL FASILKOM UNSUB...');
        $this->line('Endpoint: https://ejournal.unsub.ac.id/index.php/FASILKOM/oai');

        $bar = $this->output->createProgressBar();
        $bar->setFormat(' %current% artikel tersimpan [%bar%] %message%');
        $bar->setMessage('Mengunduh data...');

        $result = $service->harvestAll(function (int $savedCount, ?int $totalEstimated) use ($bar) {
            if ($totalEstimated && $bar->getMaxSteps() !== $totalEstimated) {
                $bar->setMaxSteps($totalEstimated);
            }
            $bar->setProgress($savedCount);
            $bar->setMessage("Memproses {$savedCount} artikel...");
        });

        $bar->finish();
        $this->newLine(2);

        if (!empty($result['errors'])) {
            $this->warn('Peringatan kendala selama pemanenan:');
            foreach ($result['errors'] as $err) {
                $this->error("- {$err}");
            }
        }

        $totalInDb = FasilkomJournal::count();
        $this->info("Sinkronisasi selesai! {$result['saved']} artikel berhasil diproses.");
        $this->info("Total artikel Jurnal GLOBAL FASILKOM di database SIBIMA: {$totalInDb}");

        return SymfonyCommand::SUCCESS;
    }
}
