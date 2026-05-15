<?php

namespace App\Services;

use App\Models\User;
use App\Models\SeminarScheduleDetail;
use App\Models\ThesisDefenseScheduleDetail;
use Carbon\Carbon;

class ConflictService
{
    /**
     * Check if a list of lecturers have conflicting schedules.
     */
    public function checkDosenConflicts(array $dosenIds, $date, $startTime, $endTime, $currentScheduleId, $scheduleType)
    {
        $conflicts = [];

        foreach ($dosenIds as $dosenId) {
            $dosen = User::find($dosenId);
            $dosenConflicts = [];

            // Seminar Conflicts
            $seminarConflicts = SeminarScheduleDetail::whereHas('schedule', fn($q) => $q->where('date', $date))
                ->where(fn($q) => $q->where('examiner1_id', $dosenId)->orWhere('examiner2_id', $dosenId))
                ->when($scheduleType === 'seminar' && $currentScheduleId, fn($q) => $q->where('seminar_schedule_id', '!=', $currentScheduleId))
                ->get();

            foreach ($seminarConflicts as $d) {
                if ($this->isOverlapping($startTime, $endTime, $d->start_time, $d->end_time)) {
                    $role = $d->examiner1_id == $dosenId ? 'Penguji 1' : 'Penguji 2';
                    $student = $d->thesis ? $d->thesis->student->name : 'Kegiatan';
                    $dosenConflicts[] = "Seminar: {$d->schedule->title} ({$role} - {$student}) - {$d->start_time} s/d {$d->end_time}";
                }
            }

            // Defense Conflicts
            $defenseConflicts = ThesisDefenseScheduleDetail::whereHas('schedule', fn($q) => $q->where('date', $date))
                ->where(fn($q) => $q->where('examiner1_id', $dosenId)->orWhere('examiner2_id', $dosenId))
                ->when($scheduleType === 'defense' && $currentScheduleId, fn($q) => $q->where('thesis_defense_schedule_id', '!=', $currentScheduleId))
                ->get();

            foreach ($defenseConflicts as $d) {
                if ($this->isOverlapping($startTime, $endTime, $d->start_time, $d->end_time)) {
                    $role = $d->examiner1_id == $dosenId ? 'Penguji 1' : 'Penguji 2';
                    $student = $d->thesis ? $d->thesis->student->name : 'Kegiatan';
                    $dosenConflicts[] = "Sidang: {$d->schedule->title} ({$role} - {$student}) - {$d->start_time} s/d {$d->end_time}";
                }
            }

            if (!empty($dosenConflicts)) {
                $conflicts[$dosenId] = ['name' => $dosen->name, 'messages' => array_unique($dosenConflicts)];
            }
        }

        return $conflicts;
    }

    private function isOverlapping($start1, $end1, $start2, $end2)
    {
        return Carbon::parse($start1)->lt(Carbon::parse($end2)) && Carbon::parse($start2)->lt(Carbon::parse($end1));
    }
}
