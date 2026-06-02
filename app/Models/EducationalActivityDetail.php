<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EducationalActivityDetail extends Model
{
   protected $fillable = ['educational_activity_id', 'consistent', 'what_learned', 'teacher_report_detail', 'attchments', 'status_id', 'replaced_activity', 'replaced_reason'];

   protected $casts = [
      'attchments' => 'array',
   ];

   public function educationalActivity()
   {
      return $this->belongsTo(ActivitySchedule::class, 'educational_activity_id');
   }

   public function status()
   {
      return $this->belongsTo(Status::class, 'status_id');
   }
}
