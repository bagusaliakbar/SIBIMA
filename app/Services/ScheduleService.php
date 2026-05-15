<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Events\NewNotification;

class ScheduleService
{
    /**
     * Store a new schedule with its details.
     */
    public function storeSchedule($model, $detailModel, array $data, $waveId)
    {
        return DB::transaction(function () use ($model, $detailModel, $data, $waveId) {
            $schedule = $model::create([
                'title' => $data['title'],
                'date' => $data['date'],
                'chairman_id' => $data['chairman_id'],
                'moderator_id' => $data['moderator_id'],
                'location' => $data['location'] ?? null,
                'meeting_link' => $data['meeting_link'] ?? null,
                'created_by' => Auth::id(),
                'wave_id' => $waveId,
            ]);

            $this->saveDetails($detailModel, $schedule, $data['details']);

            $this->notifyParticipants($schedule, $data['details']);

            return $schedule;
        });
    }

    /**
     * Update an existing schedule and its details.
     */
    public function updateSchedule($schedule, $detailModel, array $data)
    {
        return DB::transaction(function () use ($schedule, $detailModel, $data) {
            $schedule->update([
                'title' => $data['title'],
                'date' => $data['date'],
                'chairman_id' => $data['chairman_id'],
                'moderator_id' => $data['moderator_id'],
                'location' => $data['location'] ?? null,
                'meeting_link' => $data['meeting_link'] ?? null,
            ]);

            $schedule->details()->delete();
            $this->saveDetails($detailModel, $schedule, $data['details']);

            return $schedule;
        });
    }

    private function saveDetails($detailModel, $schedule, array $details)
    {
        $foreignKey = str_replace('App\\Models\\', '', get_class($schedule));
        $foreignKey = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $foreignKey)) . '_id';

        foreach ($details as $index => $detail) {
            $detailModel::create([
                $foreignKey => $schedule->id,
                'thesis_id' => $detail['thesis_id'] ?? null,
                'activity_name' => $detail['activity_name'] ?? null,
                'start_time' => $detail['start_time'],
                'end_time' => $detail['end_time'],
                'examiner1_id' => $detail['examiner1_id'] ?? null,
                'examiner2_id' => $detail['examiner2_id'] ?? null,
                'order' => $index,
            ]);
        }
    }

    private function notifyParticipants($schedule, array $details)
    {
        $userIds = collect([$schedule->chairman_id, $schedule->moderator_id]);

        foreach ($details as $detail) {
            if (isset($detail['examiner1_id'])) $userIds->push($detail['examiner1_id']);
            if (isset($detail['examiner2_id'])) $userIds->push($detail['examiner2_id']);
            
            if (isset($detail['thesis_id'])) {
                $thesis = \App\Models\Thesis::find($detail['thesis_id']);
                if ($thesis) $userIds->push($thesis->student_id);
            }
        }

        $userIds = $userIds->filter()->unique();
        $title = "Jadwal Baru Terbit";
        $message = "Jadwal " . $schedule->title . " telah dipublikasikan.";

        foreach ($userIds as $userId) {
            if ($userId != Auth::id()) {
                broadcast(new NewNotification($userId, $title, $message, 'info'));
            }
        }
    }
}
