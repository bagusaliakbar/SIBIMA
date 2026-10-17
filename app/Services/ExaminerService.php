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

        if (isset($detail->thesis) && isset($detail->thesis->student)) {
            $type = str_contains(get_class($detail), 'ThesisDefense') ? 'Sidang Akhir' : 'Seminar';
            $detail->thesis->student->notify(new \App\Notifications\RevisionRequested($detail->thesis, $type, $revision->id));
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
        if (!in_array(Auth::user()->role, ['admin', 'kaprodi']) && $revision->examiner_id !== Auth::id()) {
            throw new \Exception('Unauthorized access to revision.', 403);
        }

        $revision->update(['status' => 'approved']);

        $moduleName = str_contains(get_class($revision), 'ThesisDefense') ? 'Sidang Akhir' : 'Seminar';
        $actorName = Auth::user()->name . (in_array(Auth::user()->role, ['admin', 'kaprodi']) ? ' (' . ucfirst(Auth::user()->role) . ')' : '');
        ActivityLog::log('Penyetujuan Revisi', "{$actorName} menyetujui revisi {$moduleName} mahasiswa.", $moduleName, $revision, ['status' => 'approved']);

        if (method_exists($revision, 'detail')) {
            $this->checkGraduation($revision->detail);
        }
    }

    /**
     * Unapprove / Revert an approved revision.
     */
    public function unapproveRevision($revision)
    {
        if (!in_array(Auth::user()->role, ['admin', 'kaprodi']) && $revision->examiner_id !== Auth::id()) {
            throw new \Exception('Unauthorized access to revision.', 403);
        }

        $newStatus = 'revision';
        if ($revision->student_notes || $revision->student_file || $revision->resubmitted_at) {
            $newStatus = 'resubmitted';
        }

        $revision->update(['status' => $newStatus]);

        if (method_exists($revision, 'detail') && $revision->detail?->thesis) {
            $thesis = $revision->detail->thesis;
            if ($thesis->status === 'completed') {
                $thesis->update(['status' => 'active']);
            }
        }

        $moduleName = str_contains(get_class($revision), 'ThesisDefense') ? 'Sidang Akhir' : 'Seminar';
        $actorName = Auth::user()->name . (in_array(Auth::user()->role, ['admin', 'kaprodi']) ? ' (' . ucfirst(Auth::user()->role) . ')' : '');
        ActivityLog::log('Pembatalan Penyetujuan Revisi', "{$actorName} membatalkan status selesai revisi {$moduleName} mahasiswa untuk ditinjau kembali.", $moduleName, $revision, ['status' => $newStatus]);
    }

    /**
     * Check if all revisions are approved and mark thesis as completed.
     */
    public function checkGraduation($detail)
    {
        if (method_exists($detail, 'isRevisionAllApproved') && $detail->isRevisionAllApproved()) {
            $thesis = $detail->thesis;
            $thesis->update(['status' => 'completed']);

            // Auto-archive completed thesis to digital library catalog (Katalog Pustaka)
            try {
                \App\Models\ThesisRepository::archiveFromThesis($thesis);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Auto-archive to repository failed for thesis {$thesis->id}: " . $e->getMessage());
            }
            
            $thesis->student->notify(new \App\Notifications\GeneralNotification(
                'Selamat! Anda Lulus',
                "Seluruh revisi sidang Anda telah disetujui. Anda dinyatakan LULUS.",
                route('dashboard'),
                'success'
            ));

            $thesis->student->notify(new \App\Notifications\ThesisCompleted($thesis));

            ActivityLog::log('Kelulusan', "Mahasiswa {$thesis->student->name} dinyatakan LULUS (Yudisium).", 'Yudisium', $thesis, ['status' => 'completed']);
        }
    }

    /**
     * Update an examiner revision message.
     */
    public function updateRevisionMessage($message, array $data, $file = null, $actingUser = null)
    {
        $user = $actingUser ?? Auth::user();
        $revision = $message->revision;

        // Check authorization: only the message sender, or admin/kaprodi can update
        $isAuthorized = in_array($user->role, ['admin', 'kaprodi']) || $message->sender_id === $user->id;
        if (!$isAuthorized) {
            throw new \Exception('Anda tidak memiliki izin untuk mengedit catatan revisi ini.', 403);
        }

        // Check if revision is already approved (final)
        if ($revision && $revision->status === 'approved') {
            throw new \Exception('Catatan revisi tidak dapat diedit karena status revisi sudah disetujui (Final).', 422);
        }

        $oldMessage = $message->message;
        $message->message = $data['revision_notes'];

        if ($file !== null) {
            if ($file instanceof \Illuminate\Http\UploadedFile) {
                $revisionKey = str_contains(get_class($message), 'ThesisDefense') ? 'thesisdefenserevisionmessages' : 'seminarrevisionmessages';
                $path = $file->store($revisionKey, config('filesystems.default'));
                $message->file_path = $path;
            } else {
                $message->file_path = $file;
            }
        } elseif (array_key_exists('clear_file', $data) && $data['clear_file']) {
            $message->file_path = null;
        }

        $message->save();

        // Also sync revision_notes on the parent revision if it exists
        if ($revision && isset($revision->revision_notes)) {
            $revision->update(['revision_notes' => $data['revision_notes']]);
        }

        $moduleName = str_contains(get_class($message), 'ThesisDefense') ? 'Sidang Akhir' : 'Seminar';
        $actorName = $user->name . (in_array($user->role, ['admin', 'kaprodi']) ? ' (' . ucfirst($user->role) . ')' : '');
        ActivityLog::log(
            'Edit Catatan Revisi', 
            "{$actorName} memperbarui butir catatan revisi {$moduleName}.", 
            $moduleName, 
            $revision, 
            [
                'message_id' => $message->id,
                'old_message' => $oldMessage,
                'new_message' => $message->message,
            ]
        );

        return $message;
    }
}

