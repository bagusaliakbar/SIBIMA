<?php

namespace App\Console\Commands;

use App\Models\Thesis;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanDummyStudents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sibima:clean-dummy {--force : Force deletion without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanly delete dummy students (Mahasiswa Skripsi 1, 2, 3) and all their associated records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dummyUsers = User::where('name', 'LIKE', '%Mahasiswa Skripsi%')
            ->orWhereIn('email', ['mahasiswa1@gmail.com', 'mahasiswa2@gmail.com', 'mahasiswa3@gmail.com'])
            ->get();

        if ($dummyUsers->isEmpty()) {
            $this->info('Tidak ditemukan data dummy mahasiswa (database sudah bersih).');
            return Command::SUCCESS;
        }

        $this->warn("Ditemukan {$dummyUsers->count()} akun mahasiswa dummy:");
        foreach ($dummyUsers as $u) {
            $this->line("- [ID: {$u->id}] {$u->name} ({$u->email})");
        }

        if (!$this->option('force') && !$this->confirm('Apakah Anda yakin ingin menghapus seluruh akun dummy beserta riwayat skripsinya?')) {
            $this->info('Pembersihan dibatalkan.');
            return Command::SUCCESS;
        }

        $userIds = $dummyUsers->pluck('id')->toArray();
        $thesisIds = Thesis::whereIn('student_id', $userIds)->pluck('id')->toArray();

        DB::beginTransaction();
        try {
            // 1. Seminar revisions & messages
            $semDetailIds = DB::table('seminar_schedule_details')->whereIn('thesis_id', $thesisIds)->pluck('id')->toArray();
            if (!empty($semDetailIds)) {
                $semRevIds = DB::table('seminar_revisions')->whereIn('seminar_schedule_detail_id', $semDetailIds)->pluck('id')->toArray();
                if (!empty($semRevIds)) {
                    DB::table('seminar_revision_messages')->whereIn('seminar_revision_id', $semRevIds)->delete();
                    DB::table('seminar_revisions')->whereIn('id', $semRevIds)->delete();
                }
                DB::table('seminar_schedule_details')->whereIn('id', $semDetailIds)->delete();
            }

            // 2. Thesis Defense revisions & messages
            $defDetailIds = DB::table('thesis_defense_schedule_details')->whereIn('thesis_id', $thesisIds)->pluck('id')->toArray();
            if (!empty($defDetailIds)) {
                $defRevIds = DB::table('thesis_defense_revisions')->whereIn('thesis_defense_schedule_detail_id', $defDetailIds)->pluck('id')->toArray();
                if (!empty($defRevIds)) {
                    DB::table('thesis_defense_revision_messages')->whereIn('thesis_defense_revision_id', $defRevIds)->delete();
                    DB::table('thesis_defense_revisions')->whereIn('id', $defRevIds)->delete();
                }
                DB::table('thesis_defense_schedule_details')->whereIn('id', $defDetailIds)->delete();
            }

            // 3. Applications
            DB::table('seminar_applications')->whereIn('thesis_id', $thesisIds)->delete();
            DB::table('thesis_defense_applications')->whereIn('thesis_id', $thesisIds)->delete();

            // 4. Mentoring Sessions & Logbooks
            DB::table('mentoring_sessions')->whereIn('thesis_id', $thesisIds)->delete();
            if (DB::getSchemaBuilder()->hasTable('logbooks')) {
                DB::table('logbooks')->whereIn('thesis_id', $thesisIds)->delete();
            }

            // 5. Notifications & Activity Logs
            if (DB::getSchemaBuilder()->hasTable('notifications')) {
                DB::table('notifications')->where(function($q) use ($userIds) {
                    $q->where('notifiable_type', User::class)
                      ->whereIn('notifiable_id', $userIds);
                })->delete();
            }

            if (DB::getSchemaBuilder()->hasTable('activity_logs')) {
                DB::table('activity_logs')->whereIn('user_id', $userIds)->delete();
            }

            // 6. Messages
            if (DB::getSchemaBuilder()->hasTable('messages')) {
                DB::table('messages')->whereIn('sender_id', $userIds)->orWhereIn('receiver_id', $userIds)->delete();
            }

            // 7. Delete Theses
            DB::table('theses')->whereIn('id', $thesisIds)->delete();

            // 8. Delete Users
            DB::table('users')->whereIn('id', $userIds)->delete();

            DB::commit();
            $this->info('✓ Sukses! Seluruh data dummy mahasiswa dan riwayatnya berhasil dihapus secara tuntas.');
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Gagal menghapus data: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
