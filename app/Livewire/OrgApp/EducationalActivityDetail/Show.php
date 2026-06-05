<?php

namespace App\Livewire\OrgApp\EducationalActivityDetail;

use App\Models\EducationalActivityDetail;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Show extends Component
{
    public EducationalActivityDetail $detail;
    public $isModal = false;

    public function mount(EducationalActivityDetail $detail, $isModal = false)
    {
        $user = auth()->user();
        if (!($user->isSuperAdmin() || Gate::allows('select.any.student'))) {
            $hasDetail = \App\Reposotries\EducationalActivityDetailRepo::getTeacherDetailsQuery()
                ->where('id', $detail->id)
                ->exists();
            if (!$hasDetail) {
                abort(403, 'You do not have permission to view this record.');
            }
        }

        $this->detail = $detail->load('educationalActivity');
        $this->isModal = $isModal;
    }

    public function render()
    {

        Gate::authorize('view', $this->detail);


        return view('livewire.org-app.educational-activity-detail.show', [
            'heading' => __('Educational Activity Detail'),
        ]);
    }
}
