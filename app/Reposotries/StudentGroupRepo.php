<?php

namespace App\Reposotries;

use App\Models\StudentGroup;
use Illuminate\Support\Facades\Cache;

class StudentGroupRepo
{
    public static function studentGroups()
    {
        return Cache::rememberForever('StudentGroup-all', function () {
            return StudentGroup::select('id', 'name')->get();
        });
    }
}
