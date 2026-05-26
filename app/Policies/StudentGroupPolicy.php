<?php

namespace App\Policies;

use App\Models\StudentGroup;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\Response;

class StudentGroupPolicy
{

    public function selectAnyGroup(User $user): bool
    {
        return $user->isSuperAdmin() || Gate::allows('select.any.student');

        // if ($user->can('selectAnyGroup', \App\Models\ActivitySchedule::class)) {
        // }
    }
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, StudentGroup $studentGroup): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StudentGroup $studentGroup): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StudentGroup $studentGroup): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, StudentGroup $studentGroup): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, StudentGroup $studentGroup): bool
    {
        return false;
    }
}
