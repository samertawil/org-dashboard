<?php

namespace App\Livewire\OrgApp\EducationalActivitySchedules;

use Livewire\Component;
use App\Models\ActivitySchedule;
use Livewire\Attributes\On;

class ShowScheduleModal extends Component
{
    public ?ActivitySchedule $schedule = null;

    #[On('open-schedule-details')]
    public function openModal($id)
    {
        $this->schedule = ActivitySchedule::withShowDetails()->find($id);
        $this->dispatch('modal-show', name: 'global-schedule-show-modal');
    }

    public function render()
    {
        return view('livewire.org-app.educational-activity-schedules.show-schedule-modal');
    }
}
