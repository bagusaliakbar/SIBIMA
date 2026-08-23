<?php

namespace App\Policies;

use App\Models\MentoringSession;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MentoringSessionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Intercept all checks to allow admin full access.
     */
    public function before(User $user, $ability)
    {
        if ($user->role === 'admin' || $user->role === 'kaprodi') {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MentoringSession $mentoringSession): bool
    {
        if ($user->role === 'mahasiswa') {
            return $user->id === $mentoringSession->thesis->student_id;
        }

        if ($user->role === 'dosen') {
            return $user->id === $mentoringSession->thesis->pembimbing1_id || $user->id === $mentoringSession->thesis->pembimbing2_id;
        }

        return false;
    }

    /**
     * Determine whether the user can update / reschedule the session.
     */
    public function update(User $user, MentoringSession $mentoringSession): bool
    {
        if ($mentoringSession->status === 'completed') {
            return false;
        }

        if ($user->role === 'dosen') {
            return $user->id === $mentoringSession->thesis->pembimbing1_id || $user->id === $mentoringSession->thesis->pembimbing2_id;
        }

        return false;
    }

    /**
     * Determine whether the user can update the status (Dosen only).
     */
    public function updateStatus(User $user, MentoringSession $mentoringSession): bool
    {
        if ($user->role === 'dosen') {
            return $user->id === $mentoringSession->thesis->pembimbing1_id || $user->id === $mentoringSession->thesis->pembimbing2_id;
        }

        return false;
    }

    /**
     * Determine whether the user can upload a document (Student only).
     */
    public function uploadDocument(User $user, MentoringSession $mentoringSession): bool
    {
        if ($user->role === 'mahasiswa') {
            return $user->id === $mentoringSession->thesis->student_id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete a document (Student only).
     */
    public function deleteDocument(User $user, MentoringSession $mentoringSession): bool
    {
        if ($user->role === 'mahasiswa') {
            return $user->id === $mentoringSession->thesis->student_id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can confirm attendance (Student only).
     */
    public function confirmAttendance(User $user, MentoringSession $mentoringSession): bool
    {
        if ($user->role === 'mahasiswa') {
            return $user->id === $mentoringSession->thesis->student_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MentoringSession $mentoringSession): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MentoringSession $mentoringSession): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MentoringSession $mentoringSession): bool
    {
        return false;
    }
}
