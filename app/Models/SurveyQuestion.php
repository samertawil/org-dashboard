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
        'question_from_age',
        'question_to_age',
    ];

    protected $casts = [
        'answer_options' => 'array',
        'question_order' => 'integer',
        'domain_id' => 'integer',
        'require_detail' => 'boolean',
        'required_answer' => 'boolean',
        'min_score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'question_from_age' => 'integer',
        'question_to_age' => 'integer',
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
