<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SeminarSchedule;
use App\Models\SeminarScheduleDetail;
use App\Models\ThesisDefenseSchedule;
use App\Models\ThesisDefenseScheduleDetail;
use App\Models\User;
use Carbon\Carbon;

class ScheduleConflictController extends Controller
{
    public function checkDosenAvailability(Request $request)
    {
        $request->validate([
            'dosen_ids' => 'required|array',
            'dosen_ids.*' => 'exists:users,id',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'current_schedule_id' => 'nullable',
            'schedule_type' => 'required|in:seminar,defense'
        ]);

        $dosenIds = $request->dosen_ids;
        $date = $request->date;
        $startTime = $request->start_time;
        $endTime = $request->end_time;
        $currentScheduleId = $request->current_schedule_id;
        $scheduleType = $request->schedule_type;

        $conflicts = [];

        foreach ($dosenIds as $dosenId) {
            $dosen = User::find($dosenId);
            $dosenConflicts = [];

            // 1. Check Seminar Details (Examiners) - ONLY check where they are examiners
            $seminarDetailConflicts = SeminarScheduleDetail::whereHas('schedule', function($q) use ($date) {
                    $q->where('date', $date);
                })
                ->where(function($q) use ($dosenId) {
                    $q->where('examiner1_id', $dosenId)
                      ->orWhere('examiner2_id', $dosenId);
                })
                ->when($scheduleType === 'seminar' && $currentScheduleId, function($q) use ($currentScheduleId) {
                    $q->where('seminar_schedule_id', '!=', $currentScheduleId);
                })
                ->get();

            foreach ($seminarDetailConflicts as $d) {
                if ($this->isOverlapping($startTime, $endTime, $d->start_time, $d->end_time)) {
                    $role = $d->examiner1_id == $dosenId ? 'Penguji 1' : 'Penguji 2';
                    $student = $d->thesis ? $d->thesis->student->name : 'Kegiatan';
                    $dosenConflicts[] = "Seminar: {$d->schedule->title} ({$role} - {$student}) - {$d->start_time} s/d {$d->end_time}";
                }
            }

            // 2. Check Defense Details (Examiners) - ONLY check where they are examiners
            $defenseDetailConflicts = ThesisDefenseScheduleDetail::whereHas('schedule', function($q) use ($date) {
                    $q->where('date', $date);
                })
                ->where(function($q) use ($dosenId) {
                    $q->where('examiner1_id', $dosenId)
                      ->orWhere('examiner2_id', $dosenId);
                })
                ->when($scheduleType === 'defense' && $currentScheduleId, function($q) use ($currentScheduleId) {
                    $q->where('thesis_defense_schedule_id', '!=', $currentScheduleId);
                })
                ->get();

            foreach ($defenseDetailConflicts as $d) {
                if ($this->isOverlapping($startTime, $endTime, $d->start_time, $d->end_time)) {
                    $role = $d->examiner1_id == $dosenId ? 'Penguji 1' : 'Penguji 2';
                    $student = $d->thesis ? $d->thesis->student->name : 'Kegiatan';
                    $dosenConflicts[] = "Sidang: {$d->schedule->title} ({$role} - {$student}) - {$d->start_time} s/d {$d->end_time}";
                }
            }

            if (!empty($dosenConflicts)) {
                $conflicts[$dosenId] = [
                    'name' => $dosen->name,
                    'messages' => array_unique($dosenConflicts)
                ];
            }
        }

        return response()->json([
            'has_conflict' => !empty($conflicts),
            'conflicts' => $conflicts
        ]);
    }

    private function isOverlapping($start1, $end1, $start2, $end2)
    {
        // Convert to Carbon for easy comparison if they are strings
        $s1 = Carbon::parse($start1);
        $e1 = Carbon::parse($end1);
        $s2 = Carbon::parse($start2);
        $e2 = Carbon::parse($end2);

        return $s1->lt($e2) && $s2->lt($e1);
    }
}
