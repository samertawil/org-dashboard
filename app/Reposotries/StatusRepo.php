<?php

namespace App\Reposotries;
use App\Models\Status;
use Illuminate\Support\Facades\Cache;

class StatusRepo  
{
    public static function statuses()
    {
        return Cache::rememberForever('statuses-all', function () {
            return Status::get();
        }); 
    }
}