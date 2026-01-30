<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityWorkTeam extends Model
{
    protected $guarded = [];

    public function project()
    {
        return $this->belongsTo(Activity::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function missionTitle()
    {
        return $this->belongsTo(Status::class, 'employee_mission_title');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }
}
