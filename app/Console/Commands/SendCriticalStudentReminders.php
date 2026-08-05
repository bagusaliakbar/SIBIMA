<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Notifications\CriticalStudentReminderNotification;
use App\Notifications\KaprodiCriticalSummaryNotification;

class SendCriticalStudentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-critical-student-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send periodic WhatsApp reminders to students in critical semesters (semester 13-14+) and Kaprodi';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Searching for students in critical semester (semester >= 13)...");

        $criticalStudents = User::criticalSemester()
            ->whereHas('thesis', function($q) {
                $q->where('status', '!=', 'completed');
            })
            ->orWhere(function($q) {
                $q->where('role', 'mahasiswa')
                  ->whereNotNull('entry_year')
                  ->whereDoesntHave('thesis');
            })
            ->get()
            ->filter(function($user) {
                return $user->is_critical_semester;
            });

        $this->info("Found " . $criticalStudents->count() . " critical semester students.");

        foreach ($criticalStudents as $student) {
            try {
                $student->notify(new CriticalStudentReminderNotification());
                $this->line("Sent critical semester reminder to student: {$student->name} (Sem {$student->current_semester})");
            } catch (\Exception $e) {
                $this->error("Failed to notify student {$student->name}: " . $e->getMessage());
            }
        }

        // Notify Kaprodi / Admin
        $kaprodis = User::whereIn('role', ['kaprodi', 'admin'])->get();
        foreach ($kaprodis as $kaprodi) {
            try {
                $kaprodi->notify(new KaprodiCriticalSummaryNotification($criticalStudents));
                $this->line("Sent critical summary to Kaprodi/Admin: {$kaprodi->name}");
            } catch (\Exception $e) {
                $this->error("Failed to notify Kaprodi {$kaprodi->name}: " . $e->getMessage());
            }
        }

        $this->info("Critical student reminders completed.");
    }
}
