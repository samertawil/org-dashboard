<?php

namespace App\Livewire\OrgApp\EducationalActivitySchedules;

use App\Models\ActivitySchedule;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Show extends Component
{
    public ActivitySchedule $schedule;

    public function mount(ActivitySchedule $schedule)
    {
        $this->schedule = $schedule->load([
            'activity',
            'group',
            'activityDomain',
            'periodGroups',
            'employee',
            'createdBy',
            'updatedBy',
        ]);
    }

    public function render()
    {
        if (Gate::denies('educational-activity-schedules.index')) {
            abort(403, 'You do not have the necessary permissions.');
        }

        return view('livewire.org-app.educational-activity-schedules.show', [
            'heading' => __('Schedule Details'),
        ]);
    }
}
