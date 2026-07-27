<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;

class ExaminerService
{
    /**
     * Store a revision and message.
     */
    public function storeRevision($revisionModel, $messageModel, $detail, array $data, $file = null, $actingUser = null)
    {
        $user = $actingUser ?? Auth::user();
        
        $foreignKey = str_replace('App\\Models\\', '', get_class($detail));
        $foreignKey = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $foreignKey)) . '_id';

        $revision = $revisionModel::firstOrCreate(
            [
                $foreignKey => $detail->id,
                'examiner_id' => $user->id,
            ],
            ['status' => 'completed']
        );

        $revision->update(['status' => 'completed']);

        $revisionKey = str_replace('App\\Models\\', '', $revisionModel);
        $revisionKey = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $revisionKey)) . '_id';

        $message = $messageModel::create([
            $revisionKey => $revision->id,
            'sender_id' => $user->id,
            'message' => $data['revision_notes'],
        ]);

        if ($file) {
            if ($file instanceof \Illuminate\Http\UploadedFile) {
                $path = $file->store(str_replace('_id', '', $revisionKey) . 's', config('filesystems.default'));
                $message->update(['file_path' => $path]);
            } else {
                $message->update(['file_path' => $file]);
            }
        }

        return $message;
    }

    /**
     * Store grading scores.
     */
    public function storeGrading($revisionModel, $detail, array $data, $user)
    {
        $foreignKey = str_replace('App\\Models\\', '', get_class($detail));
        $foreignKey = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $foreignKey)) . '_id';

        $oldRevision = $revisionModel::where($foreignKey, $detail->id)->where('examiner_id', $user->id)->first();
        $oldScores = $oldRevision ? [
            'presentation' => $oldRevision->score_presentation,
            'explanation' => $oldRevision->score_explanation,
            'writing' => $oldRevision->score_writing
        ] : null;

        $revision = $revisionModel::updateOrCreate(
            [
                $foreignKey => $detail->id,
                'examiner_id' => $user->id,
            ],
            [
                'score_presentation' => $data['score_presentation'],
                'score_explanation' => $data['score_explanation'],
                'score_writing' => $data['score_writing'],
                'status' => (isset($detail->thesis) && $detail->thesis->pembimbing1_id === $user->id) ? 'approved' : 'completed'
            ]
        );

        $moduleName = str_contains($revisionModel, 'ThesisDefense') ? 'Sidang Akhir' : 'Seminar';
        ActivityLog::log('Penilaian', "Dosen {$user->name} memberikan nilai {$moduleName} untuk mahasiswa {$detail->thesis->student->name}.", $moduleName, $revision, [
            'old_scores' => $oldScores,
            'new_scores' => [
                'presentation' => $data['score_presentation'],
                'explanation' => $data['score_explanation'],
                'writing' => $data['score_writing']
            ]
        ]);


        $this->checkGraduation($detail);

        return $revision;
    }

    /**
     * Approve a revision.
     */
    public function approveRevision($revision)
    {
        if ($revision->examiner_id !== Auth::id()) {
            throw new \Exception('Unauthorized access to revision.', 403);
        }

        $revision->update(['status' => 'approved']);

        $moduleName = str_contains(get_class($revision), 'ThesisDefense') ? 'Sidang Akhir' : 'Seminar';
        ActivityLog::log('Penyetujuan Revisi', "Dosen " . Auth::user()->name . " menyetujui revisi {$moduleName} mahasiswa.", $moduleName, $revision, ['status' => 'approved']);

        if (method_exists($revision, 'detail')) {
            $this->checkGraduation($revision->detail);
        }
    }

    /**
     * Check if all revisions are approved and mark thesis as completed.
     */
    public function checkGraduation($detail)
    {
        if (method_exists($detail, 'isRevisionAllApproved') && $detail->isRevisionAllApproved()) {
            $thesis = $detail->thesis;
            $thesis->update(['status' => 'completed']);
            
            $thesis->student->notify(new \App\Notifications\GeneralNotification(
                'Selamat! Anda Lulus',
                "Seluruh revisi sidang Anda telah disetujui. Anda dinyatakan LULUS.",
                route('dashboard'),
                'success'
            ));

            ActivityLog::log('Kelulusan', "Mahasiswa {$thesis->student->name} dinyatakan LULUS (Yudisium).", 'Yudisium', $thesis, ['status' => 'completed']);
        }
    }
}
