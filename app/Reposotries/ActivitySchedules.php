<?php

namespace App\Reposotries;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class ActivitySchedules
{

    public function __construct()
    {
        //
    }

    public static function authorizeActivityScheduleView($schedule)
    {
        $user = Auth::user();
        if ($user->isSuperAdmin() || Gate::allows('select.any.educational-activity-detail') || Gate::allows('select.any.student')) {
            return null;
        }

        $groupIds = $user->teacher()->pluck('student_group_id')->toArray();
        $employeeId = $user->employee?->id;
        if (!in_array($schedule->group_id, $groupIds) || $schedule->employee_id !== $employeeId) {
            abort(403, 'You do not have permission to view this schedule.');
        }
    }
}
