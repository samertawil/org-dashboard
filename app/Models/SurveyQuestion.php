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
        'domain_id',
        'answer_input_type',
        'answer_options',
        'require_detail',
        'detail',
        'note',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'answer_options' => 'array',
    ];

    public function surveyForSection()
    {
        return $this->belongsTo(Status::class, 'survey_for_section');
    }

    public function domainRel()
    {
        return $this->belongsTo(Status::class, 'domain_id');
    }
}
