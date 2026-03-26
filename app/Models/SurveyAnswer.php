<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyAnswer extends Model
{
   protected $fillable= [
    'account_id',
    'survey_no',
    'question_id',
    'answer_ar_text',
    'answer_en_text',
    'created_by',
   ];

    public function question()
    {
        return $this->belongsTo(SurveyQuestion::class, 'question_id');
    }

    public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }
}
