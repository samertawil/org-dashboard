<?php

namespace App\Policies;

use App\Models\ActivitySchedule;
use App\Models\User;
use App\Reposotries\EducationalActivityDetailRepo;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\Response;

class ActivitySchedulePolicy
{

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response|bool
    {

        if (
            $user->isSuperAdmin() ||
            Gate::allows('select.any.educational-activity-detail') ||
            Gate::allows('select.any.student') ||
            Gate::allows('educational-activity-schedules.index')
        ) {

            return true;
        }

        return Response::deny('You do not have permission to view this schedule.');
    }


    public function view(User $user, ActivitySchedule $activitySchedule): Response|bool
    {
        if ($user->isSuperAdmin() || $user->can('select.any.student')) {
            return Response::allow();
        }

        // Use the same filtering logic as EducationalActivityDetailRepo::getTeacherSchedulesQuery()
        // job_title 167 → group membership is enough
        // job_title 166 → group membership AND employee_id must match
        $groupIds167 = $user->teacher()->where('job_title', 167)->pluck('student_group_id')->toArray();
        $groupIds166 = $user->teacher()->where('job_title', 166)->pluck('student_group_id')->toArray();
        $employeeId  = $user->employee?->id;

        $allowed = EducationalActivityDetailRepo::getTeacherSchedulesQuery()
            ->where('id', $activitySchedule->id)
            ->exists();

        if ($allowed) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to view this schedule.');
    }

    public function duplicate(User $user): Response|bool
    {

        if ($user->isSuperAdmin() || Gate::allows('educational-activity-schedules.duplicate')) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to duplicate schedules.');
    }

    public function export(User $user): Response|bool
    {

        if ($user->isSuperAdmin() || Gate::allows('educational-activity-schedules.create')) {

            return Response::allow();
        }

        return Response::deny('You do not have permission to delete schedules.');
    }

    public function create(User $user): Response|bool
    {
        if ($user->isSuperAdmin() || Gate::allows('educational-activity-schedules.create')) {
            return Response::allow();
        }
        return Response::deny('You do not have permission to Create schedules.');
    }

    public function update(User $user, ActivitySchedule $activitySchedule): Response|bool
    {
        $isLocked = \Illuminate\Support\Facades\DB::table('reports')
            ->whereJsonContains('covered_educational_activity_schedules_ids', $activitySchedule->id)
            ->exists();
        if ($isLocked) {
            return Response::deny(__('This schedule is locked because it has been included in a supervisor consolidated report.'));
        }

        if ($user->isSuperAdmin() || Gate::allows('educational-activity-schedules.create')) {
            return Response::allow();
        }
        return Response::deny('You do not have permission to create schedules.');
    }


    public function delete(User $user, ActivitySchedule $activitySchedule): Response|bool
    {
        $isLocked = \Illuminate\Support\Facades\DB::table('reports')
            ->whereJsonContains('covered_educational_activity_schedules_ids', $activitySchedule->id)
            ->exists();
        if ($isLocked) {
            return Response::deny(__('This schedule is locked because it has been included in a supervisor consolidated report.'));
        }

        if ($user->isSuperAdmin() || Gate::allows('select.any.educational-activity-detail') || Gate::allows('educational-activity-schedules.create')) {

            return Response::allow();
        }

        return Response::deny('You do not have permission to delete this record.');
    }


    public function restore(User $user, ActivitySchedule $activitySchedule): bool
    {
        return false;
    }


    public function forceDelete(User $user, ActivitySchedule $activitySchedule): bool
    {
        return false;
    }
}
