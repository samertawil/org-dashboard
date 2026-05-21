<?php

namespace App\Livewire\OrgApp\EducationalActivityDetail;

use App\Models\EducationalActivityDetail;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use App\Concerns\EducationalActivityDetail\FormTrait;

class Edit extends Component
{
    use FormTrait;

    public EducationalActivityDetail $detail;
    public $isModal = false;

    public function mount(EducationalActivityDetail $detail, $isModal = false)
    {
        $this->detail = $detail;
        $this->isModal = $isModal;
        
        $this->educational_activity_id = $detail->educational_activity_id;
        $this->consistent              = $detail->consistent;
        $this->what_learned            = $detail->what_learned;
        $this->teacher_report_detail   = $detail->teacher_report_detail;
    }

    public function save()
    {
        $this->validate();

        $this->detail->update([
            'educational_activity_id' => $this->educational_activity_id,
            'consistent'              => $this->consistent !== '' ? $this->consistent : null,
            'what_learned'            => $this->what_learned ?: null,
            'teacher_report_detail'   => $this->teacher_report_detail ?: null,
        ]);

        if ($this->isModal) {
            $this->dispatch('modal-close', name: 'report-modal');
            $this->dispatch('flux-toast', variant: 'success', title: __('Updated successfully.'));
        } else {
            session()->flash('message', __('Updated successfully.'));
            return $this->redirect(route('educational-activity-detail.index'), navigate: true);
        }
    }

    public function render()
    {
        if (Gate::denies('educational-activity-detail.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        return view('livewire.org-app.educational-activity-detail.edit', [
            'heading' => __('Edit Educational Activity Detail'),
            'type'    => 'save',
        ]);
    }
}
