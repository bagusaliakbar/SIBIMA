<?php

namespace App\Policies;

use App\Models\Thesis;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ThesisPolicy
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
    public function view(User $user, Thesis $thesis): bool
    {
        if ($user->role === 'mahasiswa') {
            return $user->id === $thesis->student_id;
        }

        if ($user->role === 'dosen') {
            return $user->id === $thesis->pembimbing1_id || $user->id === $thesis->pembimbing2_id;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Thesis $thesis): bool
    {
        if ($user->role === 'mahasiswa') {
            return $user->id === $thesis->student_id;
        }

        if ($user->role === 'dosen') {
            return $user->id === $thesis->pembimbing1_id || $user->id === $thesis->pembimbing2_id;
        }

        return false;
    }

    /**
     * Determine whether the user can toggle ACC for the thesis.
     */
    public function toggleAcc(User $user, Thesis $thesis): bool
    {
        if (in_array($user->role, ['admin', 'kaprodi'])) {
            return true;
        }

        if ($user->role === 'dosen') {
            return $user->id === $thesis->pembimbing1_id || $user->id === $thesis->pembimbing2_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Thesis $thesis): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Thesis $thesis): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Thesis $thesis): bool
    {
        return false;
    }
}
