<?php

namespace App\Reposotries;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ActivitySchedules
{


    public static function authorizeActivityScheduleView($schedule)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Unauthorized');
        }

        if ($user->isSuperAdmin() || Gate::allows('select.any.student')) {
            return null;
        }

        $groupIds = $user->teacher()->pluck('student_group_id')->toArray();
        $employeeId = $user->employee?->id;

        if (!in_array($schedule->group_id, $groupIds) || $schedule->employee_id !== $employeeId) {
            abort(403, 'You do not have permission to view this schedule.');
        }
    }

    public static function clearCache()
    {
        Cache::forever('educationalTasksQueryCache_version', time());
    }

    public static function educationalTasksQuery()
    {

        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $employeeId = $user?->employee?->id;

        $version = Cache::rememberForever('educationalTasksQueryCache_version', fn() => time());
        $cacheKey = "educationalTasksQueryCache_{$version}_" . ($user?->id ?? 'guest');

        return Cache::rememberForever($cacheKey, function () use ($user, $employeeId) {
            $educationalTasksQuery = \App\Models\ActivitySchedule::query()
                ->with(['activityDetail', 'employee', 'activityDomain', 'activityNameStatus', 'periodGroups', 'group'])
                ->active()
                ->where(function ($q) {
                    $q->delayed()
                        ->orWhere(fn($sub) => $sub->requireToday())
                        ->orWhere(fn($sub) => $sub->happenNow());
                });

            if (!($user && ($user->isSuperAdmin() || Gate::allows('select.any.student')))) {
                $educationalTasksQuery->where('employee_id', $employeeId);
            }

            return $educationalTasksQuery->ordered()->take(5)->get();
        });
    }
}
