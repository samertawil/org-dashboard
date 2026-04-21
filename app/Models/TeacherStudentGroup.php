<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherStudentGroup extends Model
{
   protected $table='teacher_student_group';

   protected $fillable = [
       'teacher_id',
       'student_group_id',
       'job_title',
   ];

   public function teacher()
   {
       return $this->belongsTo(Employee::class, 'teacher_id', 'user_id');
   }

   public function studentGroup()
   {
       return $this->belongsTo(StudentGroup::class);
   }

   public function jobTitle()
   {
       return $this->belongsTo(Status::class,'job_title');
   }
}
