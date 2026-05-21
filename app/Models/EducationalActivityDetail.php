<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EducationalActivityDetail extends Model
{
   protected $fillable = ['educational_activity_id', 'consistent', 'what_learned', 'teacher_report_detail', 'attchments'];

   protected $casts = [
      'attchments' => 'array',
   ];

   public function educationalActivity()
   {
      return $this->belongsTo(ActivitySchedule::class, 'educational_activity_id');
   }
}
