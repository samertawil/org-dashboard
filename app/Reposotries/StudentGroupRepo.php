<?php

namespace App\Reposotries;

use App\Models\StudentGroup;
use App\Enums\GlobalSystemConstant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class StudentGroupRepo
{
    public static function studentGroups()
    {
        return Cache::rememberForever('StudentGroup-all', function () {
            return StudentGroup::select('id', 'name', 'batch_no')->get();
        });
    }
    public static function activeToday()
    {
        return Cache::rememberForever('StudentGroup-activeToday', function () {
            return StudentGroup::select('id', 'name')->where('activation', GlobalSystemConstant::ACTIVE)->where('start_date', '<=', Carbon::today())
                ->where('end_date', '>=', Carbon::today()->subDays(15))->get();
        });
    }
    public static function educationPoints()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin() || Gate::allows('select.any.student')) {
            return self::studentGroups();
        }

        $employee = $user->employee;
        if ($employee) {
            return Cache::rememberForever("employee-groups-{$employee->id}", function () use ($employee) {
                return $employee->studentGroups()->get();
            });
        }

        return collect();
    }

    public static function activateEducationPointsWithEmployee()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin() || Gate::allows('select.any.student')) {
            return self::activeToday();
        }

        $employee = $user->employee;
        if ($employee) {
            return Cache::rememberForever("employee-groups-{$employee->id}", function () use ($employee) {
                return $employee->studentGroups()->get();
            });
        }

        return collect();
    }
}
