<?php

namespace App\Reposotries;

 
use App\Models\EventAssignee;
use Illuminate\Support\Facades\Cache;

class EventAssigneeRepo
{
    public static function eventAssignees($id = null)
    {
        $userId = auth()->id();
        return Cache::rememberForever("assignees.user_{$userId}.{$id}" , function () {
            return EventAssignee::with(['employee:id,user_id,full_name'])
            ->select('id', 'event_id', 'employee_id', 'status', 'response')
            ->whereIn('status', ['pending', 'clarification_needed'])
            ->whereHas('employee', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->get();
        });
    }
}
