<?php

namespace App\Reposotries;

use App\Models\StudentGroup;
use App\Enums\GlobalSystemConstant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class StudentGroupRepo
{
    public static function studentGroups()
    {
        return Cache::rememberForever('StudentGroup-all', function () {
            return StudentGroup::select('id', 'name')->get();
        });
    }
 public static function activeToday()
    {
         return Cache::rememberForever('StudentGroup-activeToday', function () {
            return StudentGroup::select('id', 'name')->where('activation', GlobalSystemConstant::ACTIVE)->where('start_date', '<=', Carbon::today())
            ->where('end_date', '>=', Carbon::today())->get();
        });
    }
    public static function educationPoints()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return self::studentGroups();
        }

        return $user->employee?->studentGroups()
            ->get() ?: collect();
    }
}
