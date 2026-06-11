<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class RevisionService
{
    /**
     * Store student reply to a revision.
     */
    public function storeReply($revision, $messageModel, array $data, $file = null)
    {
        $user = Auth::user();

        $revision->update([
            'resubmitted_at' => now(),
            'status' => 'resubmitted',
        ]);

        $foreignKey = \Illuminate\Support\Str::snake(class_basename($revision)) . '_id';

        $message = $messageModel::create([
            $foreignKey => $revision->id,
            'sender_id' => $user->id,
            'message' => $data['student_notes'],
        ]);

        if ($file) {
            $path = $file->store(strtolower(str_replace('App\\Models\\', '', $messageModel)) . 's/replies', 'public');
            $message->update(['file_path' => $path]);
        }

        return $message;
    }
}
