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
        $this->detail = $detail->load('educationalActivity');
        $this->isModal = $isModal;
    }

    public function render()
    {
        if (Gate::denies('educational-activity-detail.index')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        
        return view('livewire.org-app.educational-activity-detail.show', [
            'heading' => __('Educational Activity Detail'),
        ]);
    }
}
