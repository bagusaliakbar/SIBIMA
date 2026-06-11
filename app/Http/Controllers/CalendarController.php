<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Wave;
use App\Models\SeminarScheduleDetail;
use App\Models\ThesisDefenseScheduleDetail;
use App\Models\MentoringSession;
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

        $startDate = Carbon::parse($start)->toDateString();
        $endDate = Carbon::parse($end)->toDateString();

        $events = [];

        // 1. Waves (Deadlines)
        $waves = Wave::where(function($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate]);
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
            ->whereHas('schedule', function($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate]);
            })
            ->get();

        foreach ($seminars as $seminar) {
            $date = $seminar->schedule->date->toDateString();
            $title = $seminar->thesis ? 'SEM: ' . $seminar->thesis->student->name : ($seminar->activity_name ?? 'Persiapan/Istirahat');
            $events[] = [
                'id' => 'seminar_' . $seminar->id,
                'title' => $title,
                'start' => $date . 'T' . $seminar->start_time->format('H:i:s'),
                'end' => $date . 'T' . $seminar->end_time->format('H:i:s'),
                'backgroundColor' => '#ecfdf5',
                'textColor' => '#065f46',
                'borderColor' => '#10b981',
                'extendedProps' => [
                    'type' => 'Seminar Proposal',
                    'location' => $seminar->schedule->location,
                    'student' => $seminar->thesis ? $seminar->thesis->student->name : null,
                    'npm' => $seminar->thesis ? $seminar->thesis->student->identifier : null,
                    'description' => !$seminar->thesis ? ($seminar->activity_name ?? 'Persiapan/Istirahat') : null
                ]
            ];
        }

        // 3. Defenses
        $defenses = ThesisDefenseScheduleDetail::with(['schedule', 'thesis.student'])
            ->whereHas('schedule', function($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate]);
            })
            ->get();

        foreach ($defenses as $defense) {
            $date = $defense->schedule->date->toDateString();
            $title = $defense->thesis ? 'SID: ' . $defense->thesis->student->name : ($defense->activity_name ?? 'Persiapan/Istirahat');
            $events[] = [
                'id' => 'defense_' . $defense->id,
                'title' => $title,
                'start' => $date . 'T' . $defense->start_time->format('H:i:s'),
                'end' => $date . 'T' . $defense->end_time->format('H:i:s'),
                'backgroundColor' => '#eff6ff',
                'textColor' => '#1e40af',
                'borderColor' => '#3b82f6',
                'extendedProps' => [
                    'type' => 'Sidang Skripsi',
                    'location' => $defense->schedule->location,
                    'student' => $defense->thesis ? $defense->thesis->student->name : null,
                    'npm' => $defense->thesis ? $defense->thesis->student->identifier : null,
                    'description' => !$defense->thesis ? ($defense->activity_name ?? 'Persiapan/Istirahat') : null
                ]
            ];
        }

        // 4. Mentoring Sessions
        $user = auth()->user();
        $mentoringSessions = MentoringSession::forUser($user)
            ->with(['thesis.student', 'dosen'])
            ->whereBetween('scheduled_at', [
                Carbon::parse($start)->startOfDay(),
                Carbon::parse($end)->endOfDay()
            ])
            ->get();

        foreach ($mentoringSessions as $session) {
            $statusLabel = match($session->status) {
                'pending' => 'Menunggu Persetujuan',
                'approved' => 'Disetujui',
                'rejected' => 'Ditolak',
                'completed' => $session->is_absent ? 'Tidak Hadir' : 'Hadir',
                default => ucfirst($session->status),
            };

            if ($session->status === 'pending') {
                $bgColor = '#fef3c7';
                $textColor = '#92400e';
                $borderColor = '#f59e0b';
            } elseif ($session->status === 'rejected') {
                $bgColor = '#fff1f2';
                $textColor = '#9f1239';
                $borderColor = '#f43f5e';
            } elseif ($session->status === 'completed') {
                if ($session->is_absent) {
                    $bgColor = '#f1f5f9';
                    $textColor = '#475569';
                    $borderColor = '#cbd5e1';
                } else {
                    $bgColor = '#f5f3ff';
                    $textColor = '#5b21b6';
                    $borderColor = '#8b5cf6';
                }
            } else {
                $bgColor = '#f5f3ff';
                $textColor = '#5b21b6';
                $borderColor = '#8b5cf6';
            }

            $events[] = [
                'id' => 'mentoring_' . $session->id,
                'title' => 'BIM: ' . ($session->thesis->student->name ?? 'Mahasiswa') . ' (' . ($session->dosen->name ?? 'Dosen') . ')',
                'start' => $session->scheduled_at->toIso8601String(),
                'end' => $session->scheduled_at->copy()->addHour()->toIso8601String(),
                'backgroundColor' => $bgColor,
                'textColor' => $textColor,
                'borderColor' => $borderColor,
                'extendedProps' => [
                    'type' => 'Bimbingan (' . $statusLabel . ')',
                    'location' => $session->location ?? 'Tidak Ditentukan',
                    'student' => $session->thesis->student->name ?? 'Mahasiswa',
                    'npm' => $session->thesis->student->identifier ?? '-',
                    'dosen' => $session->dosen->name ?? 'Dosen',
                    'topic' => $session->topic,
                    'status' => $session->status,
                    'description' => 'Topik: ' . e($session->topic) . '<br>Dosen: ' . e($session->dosen->name)
                ]
            ];
        }

        return response()->json($events);
    }
}
