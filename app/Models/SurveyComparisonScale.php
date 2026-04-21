<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyComparisonScale extends Model
{
    protected $fillable = [
        'from_percentage',
        'to_percentage',
        'evaluation',
        'description',
        'color',
        'domain_id',
        'batch_no',
        'survey_for_section',
        'created_by',
        'updated_by',
    ];

    public function domain()
    {
        return $this->belongsTo(Status::class, 'domain_id');
    }

    public function surveyForSection()
    {
        return $this->belongsTo(Status::class, 'survey_for_section');
    }

    public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function updator()
    {
        return $this->belongsTo(Employee::class, 'updated_by');
    }
}
