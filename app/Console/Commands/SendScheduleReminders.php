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
    protected $description = 'Send WhatsApp and Email reminders for H-1 and H-3 schedules';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dates = [
            'H-1' => Carbon::tomorrow()->toDateString(),
            'H-3' => Carbon::today()->addDays(3)->toDateString(),
        ];

        foreach ($dates as $label => $date) {
            $this->info("Checking for schedules on {$date} ({$label})...");

            // 1. Seminar Reminders
            $seminarDetails = SeminarScheduleDetail::whereHas('schedule', function($q) use ($date) {
                $q->whereDate('date', $date);
            })->with(['schedule.chairman', 'schedule.moderator', 'thesis.student', 'thesis.pembimbing1', 'thesis.pembimbing2', 'examiner1', 'examiner2'])->get();

            $this->info("Found " . $seminarDetails->count() . " seminar entries for {$label}.");
            foreach ($seminarDetails as $detail) {
                $this->sendReminders($detail, 'Seminar Proposal', $label);
            }

            // 2. Defense (Sidang) Reminders
            $defenseDetails = ThesisDefenseScheduleDetail::whereHas('schedule', function($q) use ($date) {
                $q->whereDate('date', $date);
            })->with(['schedule.chairman', 'schedule.moderator', 'thesis.student', 'thesis.pembimbing1', 'thesis.pembimbing2', 'examiner1', 'examiner2'])->get();

            $this->info("Found " . $defenseDetails->count() . " defense entries for {$label}.");
            foreach ($defenseDetails as $detail) {
                $this->sendReminders($detail, 'Sidang Skripsi', $label);
            }
        }

        $this->info("All schedule reminders processed.");
    }

    protected function sendReminders($detail, $type, $label)
    {
        $date = Carbon::parse($detail->schedule->date)->locale('id')->translatedFormat('d F Y');
        $startTime = $detail->start_time ? Carbon::parse($detail->start_time)->format('H:i') : '-';
        $endTime = $detail->end_time ? Carbon::parse($detail->end_time)->format('H:i') : '-';
        $time = ($startTime !== '-' && $endTime !== '-') ? "{$startTime} - {$endTime} WIB" : "{$startTime} WIB";
        $studentName = $detail->thesis->student->name ?? ($detail->activity_name ?? 'Mahasiswa');

        $scheduleData = [
            'date' => $date,
            'time' => $time,
            'student' => $studentName,
            'type' => $type,
            'label' => $label,
            'location' => $detail->schedule->location ?? 'Ruangan belum ditentukan'
        ];

        $timeWord = $label === 'H-1' ? 'besok' : '3 hari lagi';
        $title = "Pengingat Jadwal {$type} ({$label})";
        $message = "Anda memiliki jadwal {$type} {$timeWord}.";

        // Recipients: Student, Advisors, Examiners, Chairman, Moderator
        $recipients = collect([
            $detail->thesis->student ?? null,
            $detail->thesis->pembimbing1 ?? null,
            $detail->thesis->pembimbing2 ?? null,
            $detail->examiner1 ?? null,
            $detail->examiner2 ?? null,
            $detail->schedule->chairman ?? null,
            $detail->schedule->moderator ?? null
        ])->filter()->unique('id');

        $staggerIndex = 0;
        foreach ($recipients as $user) {
            try {
                // Stagger queued jobs with a 6-second interval to avoid burst spam detection
                $delaySeconds = $staggerIndex * 6;
                $notification = (new ScheduleReminderNotification($title, $message, $scheduleData))->delay(now()->addSeconds($delaySeconds));
                
                $user->notify($notification);
                $this->line("Queued {$label} for {$user->name} ({$user->role}) with +{$delaySeconds}s delay");
                $staggerIndex++;
            } catch (\Exception $e) {
                $this->error("Failed to notify {$user->name}: " . $e->getMessage());
            }
        }
    }
}
