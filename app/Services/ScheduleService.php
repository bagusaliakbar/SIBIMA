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
                'chairman_id' => $data['chairman_id'] ?? null,
                'moderator_id' => $data['moderator_id'] ?? null,
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
     * Update an existing schedule and its details safely without cascading deletion of existing grades/revisions.
     */
    public function updateSchedule($schedule, $detailModel, array $data)
    {
        return DB::transaction(function () use ($schedule, $detailModel, $data) {
            $schedule->update([
                'title' => $data['title'],
                'date' => $data['date'],
                'chairman_id' => $data['chairman_id'] ?? null,
                'moderator_id' => $data['moderator_id'] ?? null,
                'location' => $data['location'] ?? null,
                'meeting_link' => $data['meeting_link'] ?? null,
            ]);

            $this->reconcileDetails($detailModel, $schedule, $data['details'] ?? []);

            return $schedule;
        });
    }

    private function reconcileDetails($detailModel, $schedule, array $details)
    {
        $foreignKey = str_replace('App\\Models\\', '', get_class($schedule));
        $foreignKey = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $foreignKey)) . '_id';

        $existingDetails = $schedule->details()->orderBy('order')->get();
        $existingByThesis = $existingDetails->whereNotNull('thesis_id')->keyBy('thesis_id');
        $existingNonThesis = $existingDetails->whereNull('thesis_id')->values();
        $usedNonThesisIds = [];
        $keptDetailIds = [];

        foreach ($details as $index => $detail) {
            $thesisId = !empty($detail['thesis_id']) ? (int) $detail['thesis_id'] : null;
            $payload = [
                $foreignKey => $schedule->id,
                'thesis_id' => $thesisId,
                'activity_name' => $detail['activity_name'] ?? null,
                'start_time' => $detail['start_time'],
                'end_time' => $detail['end_time'],
                'examiner1_id' => $detail['examiner1_id'] ?? null,
                'examiner2_id' => $detail['examiner2_id'] ?? null,
                'order' => $index,
            ];

            if ($thesisId && $existingByThesis->has($thesisId)) {
                $existingRow = $existingByThesis->get($thesisId);
                $existingRow->update($payload);
                $keptDetailIds[] = $existingRow->id;
            } elseif (!$thesisId) {
                $matchedNonThesis = $existingNonThesis->first(function ($row) use ($detail, $usedNonThesisIds) {
                    return !in_array($row->id, $usedNonThesisIds, true)
                        && (string) $row->activity_name === (string) ($detail['activity_name'] ?? '');
                });

                if ($matchedNonThesis) {
                    $matchedNonThesis->update($payload);
                    $usedNonThesisIds[] = $matchedNonThesis->id;
                    $keptDetailIds[] = $matchedNonThesis->id;
                } else {
                    $created = $detailModel::create($payload);
                    $keptDetailIds[] = $created->id;
                }
            } else {
                $created = $detailModel::create($payload);
                $keptDetailIds[] = $created->id;
            }
        }

        // Only delete detail rows that were genuinely removed from this schedule
        $schedule->details()->whereNotIn('id', $keptDetailIds)->delete();
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
        $userIds = collect([$schedule->chairman_id, $schedule->moderator_id])->filter();

        foreach ($details as $detail) {
            if (isset($detail['examiner1_id'])) $userIds->push($detail['examiner1_id']);
            if (isset($detail['examiner2_id'])) $userIds->push($detail['examiner2_id']);
            
            if (isset($detail['thesis_id'])) {
                $thesis = \App\Models\Thesis::find($detail['thesis_id']);
                if ($thesis) {
                    $userIds->push($thesis->student_id);
                    if ($thesis->pembimbing1_id) {
                        $userIds->push($thesis->pembimbing1_id);
                    }
                    if ($thesis->pembimbing2_id) {
                        $userIds->push($thesis->pembimbing2_id);
                    }
                }
            }
        }

        $userIds = $userIds->filter()->unique();
        $title = "Jadwal Baru Terbit";
        $message = "Jadwal " . $schedule->title . " telah dipublikasikan.";

        $type = str_contains(get_class($schedule), 'ThesisDefense') ? 'Sidang Akhir' : 'Seminar';

        foreach ($userIds as $userId) {
            if ($userId != Auth::id()) {
                broadcast(new NewNotification($userId, $title, $message, 'info'));
                
                $user = \App\Models\User::find($userId);
                if ($user) {
                    $user->notify(new \App\Notifications\SchedulePublished($schedule, $type));
                }
            }
        }
    }
}
