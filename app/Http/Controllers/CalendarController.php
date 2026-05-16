<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Wave;
use App\Models\SeminarScheduleDetail;
use App\Models\ThesisDefenseScheduleDetail;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index()
    {
        return view('calendar.index');
    }

    public function events(Request $request)
    {
        $start = $request->query('start');
        $end = $request->query('end');

        $events = [];

        // 1. Waves (Deadlines)
        $waves = Wave::where(function($q) use ($start, $end) {
            $q->whereBetween('start_date', [$start, $end])
              ->orWhereBetween('end_date', [$start, $end]);
        })->get();

        foreach ($waves as $wave) {
            $events[] = [
                'id' => 'wave_' . $wave->id,
                'title' => 'GELOMBANG: ' . strtoupper($wave->name),
                'start' => $wave->start_date->toDateString(),
                'end' => $wave->end_date->addDay()->toDateString(), // Exclusive end
                'backgroundColor' => '#fef3c7',
                'textColor' => '#92400e',
                'borderColor' => '#f59e0b',
                'allDay' => true,
                'extendedProps' => [
                    'type' => 'Gelombang',
                    'description' => $wave->description
                ]
            ];
        }

        // 2. Seminars
        $seminars = SeminarScheduleDetail::with(['schedule', 'thesis.student'])
            ->whereHas('schedule', function($q) use ($start, $end) {
                $q->whereBetween('date', [
                    Carbon::parse($start)->toDateString(), 
                    Carbon::parse($end)->toDateString()
                ]);
            })
            ->get();

        foreach ($seminars as $seminar) {
            $date = $seminar->schedule->date->toDateString();
            $events[] = [
                'id' => 'seminar_' . $seminar->id,
                'title' => 'SEM: ' . $seminar->thesis->student->name,
                'start' => $date . 'T' . $seminar->start_time->format('H:i:s'),
                'end' => $date . 'T' . $seminar->end_time->format('H:i:s'),
                'backgroundColor' => '#ecfdf5',
                'textColor' => '#065f46',
                'borderColor' => '#10b981',
                'extendedProps' => [
                    'type' => 'Seminar Proposal',
                    'location' => $seminar->schedule->location,
                    'student' => $seminar->thesis->student->name,
                    'npm' => $seminar->thesis->student->identifier
                ]
            ];
        }

        // 3. Defenses
        $defenses = ThesisDefenseScheduleDetail::with(['schedule', 'thesis.student'])
            ->whereHas('schedule', function($q) use ($start, $end) {
                $q->whereBetween('date', [
                    Carbon::parse($start)->toDateString(), 
                    Carbon::parse($end)->toDateString()
                ]);
            })
            ->get();

        foreach ($defenses as $defense) {
            $date = $defense->schedule->date->toDateString();
            $events[] = [
                'id' => 'defense_' . $defense->id,
                'title' => 'SID: ' . $defense->thesis->student->name,
                'start' => $date . 'T' . $defense->start_time->format('H:i:s'),
                'end' => $date . 'T' . $defense->end_time->format('H:i:s'),
                'backgroundColor' => '#eff6ff',
                'textColor' => '#1e40af',
                'borderColor' => '#3b82f6',
                'extendedProps' => [
                    'type' => 'Sidang Skripsi',
                    'location' => $defense->schedule->location,
                    'student' => $defense->thesis->student->name,
                    'npm' => $defense->thesis->student->identifier
                ]
            ];
        }

        return response()->json($events);
    }
}
