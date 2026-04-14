<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyAnswer extends Model
{
    protected $fillable = [
        'survey_table_id',
        'account_id',
        'survey_no',
        'question_id',
        'answer_ar_text',
        'answer_en_text',
        'created_by',
    ];

    public function surveyTable()
    {
        return $this->belongsTo(SurveyTable::class, 'survey_table_id');
    }

    public function question()
    {
        return $this->belongsTo(SurveyQuestion::class, 'question_id');
    }

    public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function surveyfor() {
        return $this->belongsTo(Status::class,'survey_no');
    }

    public function student() {
        return $this->belongsTo(Student::class,'account_id','identity_number');
    }

    public function scopeCalculateAnswer($query, $surveyNo, $account_id) {
        return $query->where('survey_no', $surveyNo)->where('account_id', $account_id)->count();
    }
}
