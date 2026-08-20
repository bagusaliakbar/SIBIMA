<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MentoringSession;
use App\Notifications\MentoringReminderNotification;
use Carbon\Carbon;

class SendMentoringReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-mentoring-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send WhatsApp reminders for approved mentoring sessions scheduled for tomorrow (H-1)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tomorrowStart = Carbon::tomorrow()->startOfDay();
        $tomorrowEnd = Carbon::tomorrow()->endOfDay();

        $this->info("Checking for approved mentoring sessions scheduled for tomorrow ({$tomorrowStart->toDateString()})...");

        $sessions = MentoringSession::where('status', 'approved')
            ->whereBetween('scheduled_at', [$tomorrowStart, $tomorrowEnd])
            ->with(['thesis.student', 'dosen'])
            ->get();

        $this->info("Found " . $sessions->count() . " mentoring sessions.");

        $staggerIndex = 0;
        foreach ($sessions as $session) {
            $student = $session->thesis->student ?? null;
            $dosen = $session->dosen ?? null;

            if ($student) {
                try {
                    $delaySeconds = $staggerIndex * 6;
                    $notification = (new MentoringReminderNotification($session, 'student'))->delay(now()->addSeconds($delaySeconds));
                    
                    $student->notify($notification);
                    $this->line("Queued H-1 mentoring reminder for student {$student->name} with +{$delaySeconds}s delay");
                    $staggerIndex++;
                } catch (\Exception $e) {
                    $this->error("Failed to notify student {$student->name}: " . $e->getMessage());
                }
            }

            if ($dosen) {
                try {
                    $delaySeconds = $staggerIndex * 6;
                    $notification = (new MentoringReminderNotification($session, 'dosen'))->delay(now()->addSeconds($delaySeconds));
                    
                    $dosen->notify($notification);
                    $this->line("Queued H-1 mentoring reminder for dosen {$dosen->name} with +{$delaySeconds}s delay");
                    $staggerIndex++;
                } catch (\Exception $e) {
                    $this->error("Failed to notify dosen {$dosen->name}: " . $e->getMessage());
                }
            }
        }

        $this->info("Mentoring reminders completed.");
    }
}
