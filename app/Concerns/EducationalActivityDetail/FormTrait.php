<?php

namespace App\Concerns\EducationalActivityDetail;

use App\Models\ActivitySchedule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;

trait FormTrait
{
    #[Validate('required|exists:educational_activity_schedules,id')]
    public $educational_activity_id = '';

    #[Validate('nullable|integer')]
    public $consistent = '';

    #[Validate('nullable|string')]
    public $what_learned = '';

    #[Validate('nullable|string')]
    public $teacher_report_detail = '';

    public function bootFormTrait()
    {
    }

    #[Computed()]
    public function activitySchedules()
    {
        // For simplicity, returning all. In a real scenario, you might filter by active ones or ones belonging to the user.
        return ActivitySchedule::latest()->get();
    }
}
