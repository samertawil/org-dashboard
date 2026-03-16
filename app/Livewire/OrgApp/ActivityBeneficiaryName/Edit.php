<?php

namespace App\Livewire\OrgApp\ActivityBeneficiaryName;

use App\Concerns\ActivityBeneficiaryName\ActivityBeneficiaryNameTrait;
use App\Models\activityBeneficiaryName;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Edit extends Component
{
    use ActivityBeneficiaryNameTrait;
    
    public activityBeneficiaryName $activityBeneficiaryName;

    public $identity_number = '';

    public function mount(activityBeneficiaryName $activityBeneficiaryName)
    {
        $this->activityBeneficiaryName = $activityBeneficiaryName;


        $this->fill([
            'activity_id' => $activityBeneficiaryName->activity_id,
            'displacement_camps_id' => $activityBeneficiaryName->displacement_camps_id,
            'identity_number' => $activityBeneficiaryName->identity_number,
            'full_name' => $activityBeneficiaryName->full_name,
            'phone' => $activityBeneficiaryName->phone,
            'receipt_date' => $activityBeneficiaryName->receipt_date, // Note: Livewire date might need ->format('Y-m-d') if it is a Carbon instance in Model
            'receive_method' => $activityBeneficiaryName->receive_method,
            'receive_by_name' => $activityBeneficiaryName->receive_by_name,
        ]);
        
        if ($this->receipt_date instanceof \Carbon\Carbon) {
            $this->receipt_date = $this->receipt_date->format('Y-m-d');
        }
    }

    public function rules() {
        return [
      
            'identity_number' => [
                'min_digits:9',
                'min_digits:9',
                'required',
                'integer',
                Rule::unique('activity_beneficiary_names')
                    ->ignore($this->activityBeneficiaryName->id)
                    ->where(function ($query) {
                        return $query->where('activity_id', $this->activity_id);
                    }),
            ],
        ];
    }
    
    public function update()
    {
        
        $this->validate();

        $this->activityBeneficiaryName->fill([
            'activity_id' => $this->activity_id,
            'displacement_camps_id' => $this->displacement_camps_id ?: null,
            'identity_number' => $this->identity_number,
            'full_name' => $this->full_name,
            'phone' => $this->phone,
            'receipt_date' => $this->receipt_date,
            'receive_method' => $this->receive_method ?: null,
            'receive_by_name' => $this->receive_by_name,
        ]);

        if ($this->activityBeneficiaryName->isDirty()) {       
            $this->activityBeneficiaryName->save(); 
            session()->flash('message', __('Activity Beneficiary successfully updated.'));
            session()->flash('type', 'success');
        } else {
            session()->flash('message', __('No changes were made!'));
            session()->flash('type', 'warning');
        }

        return $this->redirect(route('activity.beneficiaries.index'), navigate: true);
    }

    public function render()
    {
        if (Gate::denies('activity.beneficiaries.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        
        return view('livewire.org-app.activity-beneficiary-name.edit', [
            'heading' => __('Edit Activity Beneficiary'),
            'type' => 'update',
        ]);
    }
}
