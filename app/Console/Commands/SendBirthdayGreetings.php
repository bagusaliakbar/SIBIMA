<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\BirthdayNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendBirthdayGreetings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-birthday-greetings {--dry-run : Jalankan simulasi tanpa mengirim notifikasi nyata}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim ucapan selamat ulang tahun otomatis via WhatsApp dan in-app notification untuk mahasiswa skripsi dan dosen';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = (bool) $this->option('dry-run');
        $currentYear = Carbon::now()->year;
        $todayStr = Carbon::now()->locale('id')->translatedFormat('d F');

        $this->info("=== SIBIMA Birthday Greetings Checker [{$todayStr}] ===");
        if ($isDryRun) {
            $this->warn("Mode SIMULASI (--dry-run) aktif. Tidak ada pesan WA yang akan terkirim.");
        }

        // 1. Ambil Mahasiswa Berulang Tahun Hari Ini (yang sedang aktif menyusun skripsi)
        $students = User::where('role', 'mahasiswa')
            ->birthdayToday()
            ->where(function ($q) use ($currentYear) {
                $q->whereNull('last_birthday_wished_year')
                  ->orWhere('last_birthday_wished_year', '!=', $currentYear);
            })
            ->whereNotNull('phone_number')
            ->whereHas('thesis', function ($q) {
                $q->where('status', '!=', 'completed');
            })
            ->with(['thesis.pembimbing1'])
            ->get();

        // 2. Ambil Dosen & Kaprodi Berulang Tahun Hari Ini
        $lecturers = User::whereIn('role', ['dosen', 'kaprodi'])
            ->where('is_active', true)
            ->birthdayToday()
            ->where(function ($q) use ($currentYear) {
                $q->whereNull('last_birthday_wished_year')
                  ->orWhere('last_birthday_wished_year', '!=', $currentYear);
            })
            ->whereNotNull('phone_number')
            ->get();

        $allCelebrants = $students->concat($lecturers);
        $totalCount = $allCelebrants->count();

        $this->info("Ditemukan {$students->count()} Mahasiswa Skripsi dan {$lecturers->count()} Dosen yang berulang tahun hari ini.");

        if ($totalCount === 0) {
            $this->line("Tidak ada civitas akademika yang berulang tahun hari ini atau ucapan sudah terkirim.");
            return Command::SUCCESS;
        }

        $staggerIndex = 0;
        foreach ($allCelebrants as $user) {
            $ageStr = $user->age ? "({$user->age} thn)" : "";
            $roleLabel = ucfirst($user->role);

            if ($isDryRun) {
                $this->line("[SIMULASI] {$roleLabel}: {$user->name} {$ageStr} - WA: {$user->phone_number}");
                continue;
            }

            try {
                // Jeda antar pengiriman (6 detik per orang) untuk keamanan gateway WhatsApp Fonnte
                $delaySeconds = $staggerIndex * 6;
                $notification = (new BirthdayNotification())->delay(now()->addSeconds($delaySeconds));

                $user->notify($notification);

                // Tandai tahun ucapan agar tidak terjadi duplikasi pada hari yang sama
                $user->update([
                    'last_birthday_wished_year' => $currentYear,
                ]);

                $this->info("✓ Terjadwal untuk {$roleLabel}: {$user->name} {$ageStr} (+{$delaySeconds}s delay)");
                $staggerIndex++;
            } catch (\Throwable $e) {
                $this->error("✗ Gagal mengirim ucapan ke {$user->name}: " . $e->getMessage());
            }
        }

        $this->info("Selesai memproses seluruh ucapan selamat ulang tahun.");
        return Command::SUCCESS;
    }
}
