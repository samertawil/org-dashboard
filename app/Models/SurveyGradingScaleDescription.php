<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyGradingScaleDescription extends Model
{
    protected $fillable = [
        'domain_id',
        'survey_grading_scale_id',
        'description',
        'need_processing',
    ];

    public function domainRel()
    {
        return $this->belongsTo(Status::class, 'domain_id');
    }

    public function gradingScale()
    {
        return $this->belongsTo(SurveyGradingScaleTable::class, 'survey_grading_scale_id');
    }
}
