<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyQuestion extends Model
{
    protected $fillable = [
        'survey_table_id',
        'survey_for_section',
        'question_order',
        'question_ar_text',
        'question_en_text',
        'domain_id',
        'answer_input_type',
        'answer_options',
        'require_detail',
        'required_answer',
        'detail',
        'note',
        'created_by',
        'updated_by',
        'batch_no',
        'min_score',
        'max_score',
    ];

    protected $casts = [
        'answer_options' => 'array',
    ];

    public function surveyTable()
    {
        return $this->belongsTo(SurveyTable::class, 'survey_table_id');
    }

    public function surveyForSection()
    {
        return $this->belongsTo(Status::class, 'survey_for_section');
    }

    public function domainRel()
    {
        return $this->belongsTo(Status::class, 'domain_id');
    }

    public function batchs()
    {
        return $this->belongsTo(StudentGroup::class, 'batch_no', 'batch_no');
    }
}
