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

        if ($user->isSuperAdmin() || Gate::allows('select.any.student') || Gate::allows('select.any.educational-activity-detail')) {

            return $query;
        }

        $teacherGroups = $user->teacher()->select('student_group_id', 'job_title')->get();
        $groupIds167 = $teacherGroups->where('job_title', 167)->pluck('student_group_id')->toArray();
        $groupIds166 = $teacherGroups->where('job_title', 166)->pluck('student_group_id')->toArray();
        $employeeId = $user->employee?->id;

        return $query->where(function ($q) use ($groupIds167, $groupIds166, $employeeId) {
            $q->whereIn('group_id', $groupIds167)
                ->orWhere(function ($sub) use ($groupIds166, $employeeId) {
                    $sub->whereIn('group_id', $groupIds166)
                        ->where('employee_id', $employeeId);
                });
        });
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
        $query = EducationalActivityDetail::query()->with(['educationalActivity.periodGroups', 'status']);

        if ($user->isSuperAdmin() || Gate::allows('select.any.educational-activity-detail') || Gate::allows('select.any.student')) {

            return $query;
        }

        $teacherGroups = $user->teacher()->select('student_group_id', 'job_title')->get();
        $groupIds167 = $teacherGroups->where('job_title', 167)->pluck('student_group_id')->toArray();
        $groupIds166 = $teacherGroups->where('job_title', 166)->pluck('student_group_id')->toArray();
        $employeeId = $user->employee?->id;

        return $query->whereHas('educationalActivity', function ($q) use ($groupIds167, $groupIds166, $employeeId) {
            $q->where(function ($subQ) use ($groupIds167, $groupIds166, $employeeId) {
                $subQ->whereIn('group_id', $groupIds167)
                    ->orWhere(function ($sub) use ($groupIds166, $employeeId) {
                        $sub->whereIn('group_id', $groupIds166)
                            ->where('employee_id', $employeeId);
                    });
            });
        });
    }
}
