<?php

namespace App\Concerns\EducationalActivityDetail;

use Livewire\Attributes\Computed;

trait FormTrait
{
    public $educational_activity_id = '';

    public $consistent = '';

    public $what_learned = '';

    public $teacher_report_detail = '';

    public $status_id = '';

    public $replaced_activity = '';

    public $replaced_reason = '';

    public function bootFormTrait() {}

    public function rules()
    {
        return [
            'educational_activity_id' => 'required|exists:educational_activity_schedules,id',
            'consistent'              => 'nullable|integer',
            'what_learned'            => 'nullable|string',
            'teacher_report_detail'   => 'nullable|string',
            'status_id'               => 'required|exists:statuses,id',
            'replaced_activity'       => $this->status_id != 193 ? 'required|string' : 'nullable|string',
            'replaced_reason'         => $this->status_id != 193 ? 'required|string' : 'nullable|string',
        ];
    }

    #[Computed()]
    public function activitySchedules()
    {
        return \App\Reposotries\EducationalActivityDetailRepo::getTeacherSchedules();
    }

    #[Computed()]
    public function activityReportStatuses()
    {
        return \App\Reposotries\StatusRepo::statuses()->where('p_id_sub', config('appConstant.activity_report_statuses'));
    }

    #[Computed()]
    public function selectedActivitySchedule()
    {
        if (empty($this->educational_activity_id)) {
            return null;
        }
        return \App\Models\ActivitySchedule::find($this->educational_activity_id);
    }
}
