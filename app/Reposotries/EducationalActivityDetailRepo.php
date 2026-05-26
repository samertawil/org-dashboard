<?php

namespace App\Reposotries;

use App\Models\ActivitySchedule;
use App\Models\EducationalActivityDetail;
use Illuminate\Support\Facades\Gate;

class EducationalActivityDetailRepo
{
    /**
     * Get the base query for ActivitySchedules filtered by the logged-in employee/teacher.
     * Super admins bypass the restriction.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getTeacherSchedulesQuery()
    {
        $user = auth()->user();
        $query = ActivitySchedule::query();

        if ($user->isSuperAdmin() || Gate::allows('select.any.educational-activity-detail') || Gate::allows('select.any.student')) {

            return $query;
        }

        $groupIds = $user->teacher()->pluck('student_group_id')->toArray();
        $employeeId = $user->employee?->id;

        return $query->whereIn('group_id', $groupIds)
            ->where('employee_id', $employeeId);
    }

    /**
     * Get the ActivitySchedules list assigned to the teacher with relationships.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getTeacherSchedules()
    {
        return self::getTeacherSchedulesQuery()
            ->with(['periodGroups'])
            ->latest()
            ->get();
    }

    /**
     * Get the base query for EducationalActivityDetails filtered by the logged-in employee/teacher.
     * Super admins bypass the restriction.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getTeacherDetailsQuery()
    {
        $user = auth()->user();
        $query = EducationalActivityDetail::query()->with(['educationalActivity.periodGroups']);

        if ($user->isSuperAdmin() || Gate::allows('select.any.educational-activity-detail') || Gate::allows('select.any.student')) {

            return $query;
        }

        $groupIds = $user->teacher()->pluck('student_group_id')->toArray();
        $employeeId = $user->employee?->id;

        return $query->whereHas('educationalActivity', function ($q) use ($groupIds, $employeeId) {
            $q->whereIn('group_id', $groupIds)
                ->where('employee_id', $employeeId);
        });
    }
}
