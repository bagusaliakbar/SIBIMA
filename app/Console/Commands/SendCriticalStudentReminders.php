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

        $staggerIndex = 0;
        foreach ($criticalStudents as $student) {
            try {
                $delaySeconds = $staggerIndex * 7;
                $notification = (new CriticalStudentReminderNotification())->delay(now()->addSeconds($delaySeconds));
                
                $student->notify($notification);
                $this->line("Queued critical reminder for {$student->name} with +{$delaySeconds}s delay");
                $staggerIndex++;
            } catch (\Exception $e) {
                $this->error("Failed to notify student {$student->name}: " . $e->getMessage());
            }
        }

        // Notify Kaprodi / Admin
        $kaprodis = User::whereIn('role', ['kaprodi', 'admin'])->get();
        foreach ($kaprodis as $kaprodi) {
            try {
                $delaySeconds = $staggerIndex * 7;
                $notification = (new KaprodiCriticalSummaryNotification($criticalStudents))->delay(now()->addSeconds($delaySeconds));
                
                $kaprodi->notify($notification);
                $this->line("Queued critical summary for Kaprodi/Admin {$kaprodi->name} with +{$delaySeconds}s delay");
                $staggerIndex++;
            } catch (\Exception $e) {
                $this->error("Failed to notify Kaprodi {$kaprodi->name}: " . $e->getMessage());
            }
        }

        $this->info("Critical student reminders completed.");
    }
}
