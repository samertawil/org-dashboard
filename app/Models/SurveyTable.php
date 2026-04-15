<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyTable extends Model
{
    protected $table = 'survey_table';
    public $timestamps = true;

    protected $fillable = [
        'for_id',
        'from_age',
        'to_age',
        'survey_for_section',
        'survey_target',
        'survey_name',
        'semester',
        'survey_target',
        'is_active',
        'conditions',
        'notes',
    ];

    public function targetRel()
    {
        return $this->belongsTo(Status::class, 'survey_target');
    }

    public function sectionRel()
    {
        return $this->belongsTo(Status::class, 'survey_for_section');
    }

    public function questions()
    {
        return $this->hasMany(SurveyQuestion::class, 'survey_table_id');
    }

    public function answers()
    {
        return $this->hasMany(SurveyAnswer::class, 'survey_table_id');
    }
}
