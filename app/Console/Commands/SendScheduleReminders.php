<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SeminarScheduleDetail;
use App\Models\ThesisDefenseScheduleDetail;
use App\Notifications\ScheduleReminderNotification;
use Carbon\Carbon;

class SendScheduleReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-schedule-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send WhatsApp and Email reminders for schedules occurring tomorrow';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tomorrow = Carbon::tomorrow()->toDateString();
        $this->info("Checking for schedules on {$tomorrow}...");

        // 1. Seminar Reminders
        $seminarDetails = SeminarScheduleDetail::whereHas('schedule', function($q) use ($tomorrow) {
            $q->where('date', $tomorrow);
        })->with(['schedule.chairman', 'schedule.moderator', 'thesis.student', 'examiner1', 'examiner2'])->get();

        $this->info("Found " . $seminarDetails->count() . " seminar entries.");
        foreach ($seminarDetails as $detail) {
            $this->sendReminders($detail, 'Seminar Hasil');
        }

        // 2. Defense (Sidang) Reminders
        $defenseDetails = ThesisDefenseScheduleDetail::whereHas('schedule', function($q) use ($tomorrow) {
            $q->where('date', $tomorrow);
        })->with(['schedule.chairman', 'schedule.moderator', 'thesis.student', 'examiner1', 'examiner2'])->get();

        $this->info("Found " . $defenseDetails->count() . " defense entries.");
        foreach ($defenseDetails as $detail) {
            $this->sendReminders($detail, 'Sidang Skripsi');
        }

        $this->info("All reminders processed.");
    }

    protected function sendReminders($detail, $type)
    {
        $date = Carbon::parse($detail->schedule->date)->locale('id')->translatedFormat('d F Y');
        $time = substr($detail->start_time, 0, 5) . ' - ' . substr($detail->end_time, 0, 5);
        $studentName = $detail->thesis->student->name;

        $scheduleData = [
            'date' => $date,
            'time' => $time,
            'student' => $studentName,
            'type' => $type
        ];

        $title = "Pengingat Jadwal {$type}";
        $message = "Anda memiliki jadwal {$type} besok.";

        // Recipients: Student, Examiners, Chairman, Moderator
        $recipients = collect([
            $detail->thesis->student,
            $detail->examiner1,
            $detail->examiner2,
            $detail->schedule->chairman,
            $detail->schedule->moderator
        ])->filter()->unique('id');

        foreach ($recipients as $user) {
            try {
                $user->notify(new ScheduleReminderNotification($title, $message, $scheduleData));
                $this->line("Sent to: {$user->name} ({$user->role})");
            } catch (\Exception $e) {
                $this->error("Failed to notify {$user->name}: " . $e->getMessage());
            }
        }
    }
}
