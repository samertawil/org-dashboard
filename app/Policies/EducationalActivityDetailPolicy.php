<?php

namespace App\Policies;

use App\Models\EducationalActivityDetail;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\Response;

class EducationalActivityDetailPolicy
{

    public function viewAny(User $user): Response|bool
    {
        if ($user->isSuperAdmin() || $user->can('select.any.educational-activity-detail') || $user->can('select.any.student') || $user->can('educational-activity-detail.index') || $user->can('educational-activity-detail.create')) {
            return Response::allow();
        }
        return Response::deny('You do not have the necessary permissions to view any educational activity report.');
    }


    public function view(User $user, $detail = null): Response|bool
    {
        if ($user->isSuperAdmin() || $user->can('select.any.educational-activity-detail') || $user->can('select.any.student')) {
            return Response::allow();
        }

        if (!($detail instanceof EducationalActivityDetail)) {
            return Response::deny('Invalid report detail.');
        }

        $employeeId = $user->employee?->id;
        $groupId = $detail->educationalActivity?->group_id;
        $groupIds = $user->teacher()->pluck('student_group_id')->toArray();

        if ($detail->educationalActivity?->employee_id == $employeeId && in_array($groupId, $groupIds)) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to for this report.');
    }


    public function create(User $user): Response|bool
    {
        if ($user->isSuperAdmin() || $user->can('educational-activity-detail.create')) {
            return Response::allow();
        }
        return Response::deny('You do not have permission to create educational activity report.');
    }


    public function update(User $user, EducationalActivityDetail $educationalActivityDetail): Response|bool
    {
        if ($user->isSuperAdmin() || $user->can('educational-activity-detail.create')) {
            return Response::allow();
        }
        return Response::deny('You do not have permission to create educational activity report.');
    }


    public function delete(User $user, EducationalActivityDetail $educationalActivityDetail): Response|bool
    {
        if ($user->isSuperAdmin() || $user->can('educational-activity-detail.create')) {
            return Response::allow();
        }
        return Response::deny('You do not have permission to create educational activity report.');
    }
}
