<?php

namespace App\Http\Controllers;

use App\Services\ConflictService;
use Illuminate\Http\Request;

class ScheduleConflictController extends Controller
{
    protected $conflictService;

    public function __construct(ConflictService $conflictService)
    {
        $this->conflictService = $conflictService;
    }

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

        $conflicts = $this->conflictService->checkDosenConflicts(
            $request->dosen_ids,
            $request->date,
            $request->start_time,
            $request->end_time,
            $request->current_schedule_id,
            $request->schedule_type
        );

        return response()->json([
            'has_conflict' => !empty($conflicts),
            'conflicts' => $conflicts
        ]);
    }
}
