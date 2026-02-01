<?php
// app/Models/ActivityReport.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class ActivityReport extends Model
{
    protected $table = 'activities';  // Source table
    public $timestamps = false;
    protected $casts = [
        'cost' => 'decimal:2',
        'start_date' => 'date',
    ];

    // Custom scope for the full report query
    public static function scopeReport(Builder $query): Builder
    {
        return $query->select([
                'activities.id',
                'activities.sector_id',
                'activities.status as real_status',
                'activities.region',
                'activities.city',
                'activities.cost',
                DB::raw("DATE_FORMAT(activities.start_date, '%m/%Y') as formatted_start_date"),
                DB::raw("DATE_FORMAT(activities.start_date, '%b/%Y') as formatted_start_date2"),
                DB::raw("DATE_FORMAT(CURDATE(), '%m/%Y') as formatted_current_date"),
                DB::raw("DATE_FORMAT(CURDATE(), '%b/%Y') as formatted_current_date2"),
                DB::raw("
                    CASE 
                        WHEN activities.status IS NULL AND EXISTS(SELECT 1 FROM activity_attchments WHERE activity_id = activities.id) THEN 27
                        WHEN activities.status IS NULL AND activities.start_date > CURDATE() THEN 25
                        WHEN activities.status IS NULL AND activities.start_date = CURDATE() THEN 26
                        WHEN activities.status IS NULL AND activities.start_date < CURDATE() THEN 28 
                        ELSE activities.status
                    END as status
                "),
                DB::raw("CASE WHEN EXISTS(SELECT 1 FROM activity_attchments WHERE activity_id = activities.id) THEN 'ADDED ATTCHMENTS' ELSE 'EMPTY ATTCHMENTS' END as attchemnts_status"),
                DB::raw("CASE WHEN EXISTS(SELECT 1 FROM activity_parcels WHERE activity_id = activities.id) THEN 'ADDED PARCELS' ELSE 'EMPTY PARCELS' END as parcels_status"),
                DB::raw("CASE WHEN EXISTS(SELECT 1 FROM activity_beneficiaries WHERE activity_id = activities.id) THEN 'ADDED BENEFICIARIES' ELSE 'EMPTY BENEFICIARIES' END as beneficiaries_status"),
                DB::raw("CASE WHEN EXISTS(SELECT 1 FROM activity_work_teams WHERE activity_id = activities.id) THEN 'ADDED WORK TEAMS' ELSE 'EMPTY WORK TEAMS' END as work_teams_status")
            ])
            ->distinct();
    }

    // Static method for convenience
    public static function getReport($paginate = false, $perPage = 15)
    {
        $query = static::report();
        return $paginate ? $query->paginate($perPage) : $query->get();
    }
}
