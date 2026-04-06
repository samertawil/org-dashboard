<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyGradingScaleTable extends Model
{
    protected $table = 'survey_grading_scale_tables';

    protected $fillable = [

        'from_percentage',
        'to_percentage',
        'evaluation',
        'description',
        'type',
        'batch_no',
        'survey_for_section',
        'question_type',
        'created_by',
        'updated_by',
        'updated_by',
    ];

    public function type()
    {
        return $this->belongsTo(Status::class, 'type');
    }

     
    public function surveyForSection()
    {
        return $this->belongsTo(Status::class, 'survey_for_section');
    }

    public function updatedBy()
    {
        return $this->belongsTo(Employee::class, 'updated_by');
    }
}
