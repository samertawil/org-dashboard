<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyQuestion extends Model
{
    protected $fillable = [
        'survey_for_section',
        'question_order',
        'question_ar_text',
        'question_en_text',
        'answer_input_type',
        'require_detail',
        'detail',
        'note',
    ];

    public function surveyForSection()
    {
        return $this->belongsTo(Status::class, 'survey_for_section');
    }
}
