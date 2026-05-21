<?php

namespace App\Livewire\OrgApp\EducationalActivityDetail;

use App\Models\EducationalActivityDetail;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use App\Concerns\EducationalActivityDetail\FormTrait;

class Create extends Component
{
    use FormTrait;

    public $isModal = false;

    public function mount($educational_activity_id = null, $isModal = false)
    {
        if ($educational_activity_id) {
            $this->educational_activity_id = $educational_activity_id;
        }
        $this->isModal = $isModal;
    }

    public function save()
    {
        $this->validate();

        EducationalActivityDetail::create([
            'educational_activity_id' => $this->educational_activity_id,
            'consistent'              => $this->consistent !== '' ? $this->consistent : null,
            'what_learned'            => $this->what_learned ?: null,
            'teacher_report_detail'   => $this->teacher_report_detail ?: null,
        ]);

        if ($this->isModal) {
            $this->dispatch('modal-close', name: 'report-modal');
            $this->dispatch('flux-toast', variant: 'success', title: __('Created successfully.'));
        } else {
            session()->flash('message', __('Created successfully.'));
            return $this->redirect(route('educational-activity-detail.index'), navigate: true);
        }
    }

    public function render()
    {
        if (Gate::denies('educational-activity-detail.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }

        return view('livewire.org-app.educational-activity-detail.create', [
            'heading' => __('Create Educational Activity Detail'),
            'type'    => 'save',
        ]);
    }
}
