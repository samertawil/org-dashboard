<?php

namespace App\Livewire\OrgApp\ActivityBeneficiaryName;

use App\Concerns\ActivityBeneficiaryName\ActivityBeneficiaryNameTrait;
use App\Models\activityBeneficiaryName;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Create extends Component
{
    use ActivityBeneficiaryNameTrait;

    public $identity_number = '';

    public function rules()
    {
        return [
        
            'identity_number' => [
                'min_digits:9',
                'max_digits:9',
                'required',
                'integer',
                Rule::unique('activity_beneficiary_names')->where(function ($query) {
                    return $query->where('activity_id', $this->activity_id);
                }),
            ],
        ];
    }

    public function save()
    {
        $this->validate();

        activityBeneficiaryName::create([
            'activity_id' => $this->activity_id,
            'displacement_camps_id' => $this->displacement_camps_id ?: null,
            'identity_number' => $this->identity_number,
            'full_name' => $this->full_name,
            'phone' => $this->phone,
            'receipt_date' => $this->receipt_date,
            'receive_method' => $this->receive_method ?: null,
            'receive_by_name' => $this->receive_by_name,
        ]);

        session()->flash('message', __('Activity Beneficiary successfully created.'));

        return $this->redirect(route('activity.beneficiaries.index'), navigate: true);
    }

    public function render()
    {     
        if (Gate::denies('activity.beneficiaries.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        return view('livewire.org-app.activity-beneficiary-name.create', [
            'heading' => __('Create Activity Beneficiary'),
            'type' => 'save',
        ]);
    }
}
