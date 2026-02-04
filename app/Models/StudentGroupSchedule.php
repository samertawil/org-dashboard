<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentGroupSchedule extends Model
{
    protected $table = 'student_group_schedules';
    
    protected $fillable = [
    'schedule_date',
    'name',
    'day',
    'start_time',
    'end_time',
    'hours',
    'is_off_day',
    'is_half_day',
    'notes',
    'student_group_id',
    'updated_by',
    'activation',
    'status_id',
    
   ];
   
   public function status()
   {
       return $this->belongsTo(Status::class, 'status_id');
   }    

}
 