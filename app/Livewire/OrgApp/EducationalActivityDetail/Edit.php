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
        $user = auth()->user();
        if (!($user->isSuperAdmin() || Gate::allows('select.any.educational-activity-detail'))) {
            $hasDetail = \App\Reposotries\EducationalActivityDetailRepo::getTeacherDetailsQuery()
                ->where('id', $detail->id)
                ->exists();
            if (!$hasDetail) {
                abort(403, 'You do not have permission to view/edit this record.');
            }
        }

        $this->detail = $detail;
        $this->isModal = $isModal;

        $this->educational_activity_id = $detail->educational_activity_id;
        $this->consistent              = $detail->consistent;
        $this->what_learned            = $detail->what_learned;
        $this->teacher_report_detail   = $detail->teacher_report_detail;
        $this->status_id               = $detail->status_id;
        $this->replaced_activity       = $detail->replaced_activity;
        $this->replaced_reason         = $detail->replaced_reason;
    }

    public function save()
    {
        $this->validate();

        $user = auth()->user();
        if (!($user->isSuperAdmin() || Gate::allows('select.any.educational-activity-detail'))) {
            // check that the original detail record belongs to this employee
            $hasDetail = \App\Reposotries\EducationalActivityDetailRepo::getTeacherDetailsQuery()
                ->where('id', $this->detail->id)
                ->exists();
            if (!$hasDetail) {
                abort(403, 'You do not have permission to update this record.');
            }

            // check that the new selected schedule also belongs to this employee
            $hasSchedule = \App\Reposotries\EducationalActivityDetailRepo::getTeacherSchedulesQuery()
                ->where('id', $this->educational_activity_id)
                ->exists();
            if (!$hasSchedule) {
                abort(403, 'You do not have permission to use this educational activity.');
            }
        }

        $this->detail->update([
            'educational_activity_id' => $this->educational_activity_id,
            'consistent'              => $this->consistent !== '' ? $this->consistent : null,
            'what_learned'            => $this->what_learned ?: null,
            'teacher_report_detail'   => $this->teacher_report_detail ?: null,
            'status_id'               => $this->status_id,
            'replaced_activity'       => $this->replaced_activity ?: null,
            'replaced_reason'         => $this->replaced_reason ?: null,
        ]);

        $this->dispatch('report-saved');

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
