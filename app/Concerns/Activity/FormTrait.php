<?php

namespace App\Concerns\Activity;

use App\Models\CurrancyValue;
use App\Models\PurchaseRequisition;
use App\Models\TeachingGroup;
use App\Reposotries\PartnersRepo;
use App\Reposotries\StatusRepo;
use App\Reposotries\StudentGroupRepo;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;

trait FormTrait
{
    public $selectedParcelIndex = null;
    #[Validate('nullable|string')]
    public $description = null;

    #[Validate('required|date')]
    public $start_date = '';

    #[Validate('nullable|date|after_or_equal:start_date')]
    public $end_date = '';

    #[Validate('required|numeric|min:1')]
    public $cost = null;

    #[Validate('required|numeric|min:1')]
    public $cost_nis = null;

    #[Validate('nullable|exists:statuses,id')]
    public $status = null;

    
    #[Validate('nullable|exists:regions,id')]
    public int|null $region = null;

    #[Validate('nullable|exists:cities,id')]
    public   $city = '';

    #[Validate('nullable|exists:neighbourhoods,id')]
    public   $neighbourhood = '';

    #[Validate('nullable|exists:locations,id')]
    public  $location = '';

    #[Validate('nullable|string|max:50')]
    public $latitude = null;

    #[Validate('nullable|string|max:50')]
    public $longitudes = null;
    // #[Validate('required|integer')]
    // public $activation =  GlobalSystemConstant::ACTIVE->value;

    #[Validate('required|exists:statuses,id')]
    public $sector_id = '';

    #[Validate('nullable|exists:statuses,id')]
    public $unit_id ;

    
    #[Validate('nullable|string|max:255')]
    public  $address_details = null;

    public $beneficiaryTypes=[];
    public $missionTitles=[];
    public $partners=[];
    public $studentGroups=[];
    public $activity_partners = [];
    public $units = [];
 
    public $parcels = [];
    public $beneficiaries = [];
    public $work_teams = [];
    public $exchange_rate = null;

    #[Computed()]
    public function allStatuses()
    {
        return StatusRepo::statuses();
    }
    
    public function bootFormTrait()
    {
      
        $this->beneficiaryTypes = $this->allStatuses()->where('p_id_sub', config('appConstant.beneficiaries_types')) ;
        $this->missionTitles =  $this->allStatuses()->where('p_id_sub', config('appConstant.aids_primary_missions')) ;
        $this->units =  $this->allStatuses()->where('p_id_sub', config('appConstant.units_statuses')) ;
        $this->partners = PartnersRepo::partners();
        $this->studentGroups = StudentGroupRepo::studentGroups();
        $this->exchange_rate = CurrancyValue::latest('exchange_date')->first()?->currency_value;
    }

    public function addParcel()
    {
        $this->parcels[] = [
            'parcel_type' => '',
            'distributed_parcels_count' => 0,
            'cost_for_each_parcel' => 0.00,
            'status_id' => '',
            'notes' => '',
            'unit_id' => '',
            'purchase_requisition_id' => null,
            'purchase_requisition_number' => null,
        ];
    }

     

    public function removeParcel($index)
    {
        unset($this->parcels[$index]);
        $this->parcels = array_values($this->parcels);
    }

    public function addBeneficiary()
    {
        $this->beneficiaries[] = [
            'beneficiary_type' => '',
            'beneficiaries_count' => 0,
            'cost_for_each_beneficiary' => 0.00,
            'status_id' => '',
            'notes' => null,
        ];
    }

    public function removeBeneficiary($index)
    {
        unset($this->beneficiaries[$index]);
        $this->beneficiaries = array_values($this->beneficiaries);
    }

    public function addWorkTeam()
    {
        $this->work_teams[] = [
            'employee_mission_title' => null,
            'employee_id' => '',
            'status_id' => '',
            'notes' => null,
        ];
    }

    public function removeWorkTeam($index)
    {
        unset($this->work_teams[$index]);
        $this->work_teams = array_values($this->work_teams);
    }

    public function addActivityPartner()
    {
        $this->activity_partners[] = [
            'partner_id' => '',
            'notes' => null,
        ];
    }

    public function removeActivityPartner($index)
    {
        unset($this->activity_partners[$index]);
        $this->activity_partners = array_values($this->activity_partners);
    }

    public $feedbacks = [];

    public function addFeedback()
    {
        $this->feedbacks[] = [
            'rating' => null,
            'comment' => '',
            'client_name' => '',
        ];
    }

    public function removeFeedback($index)
    {
        unset($this->feedbacks[$index]);
        $this->feedbacks = array_values($this->feedbacks);
    }

    public function updatedRegion()
    {
        $this->city = '';
        $this->neighbourhood = '';
        $this->location = '';
    }

    public function updatedCity()
    {
        $this->neighbourhood = '';
        $this->location = '';
    }

    public function updatedNeighbourhood()
    {
        $this->location = '';
    }

    public $teaching_groups = [];

    public function addTeachingGroup()
    {
        $count = count(TeachingGroup::get()) + 1;
        
        $this->teaching_groups[] = [
            'name' => "Teaching Point #{$count}",    
            'activation' => 1,
            'cost_usd' => 0.00,
            'cost_nis' => 0.00,
            'partner_id' => null,
            'notes' => '',
            'student_groups_id' => null,
          
        ];
    }

    public function removeTeachingGroup($index)
    {
        unset($this->teaching_groups[$index]);
        $this->teaching_groups = array_values($this->teaching_groups);
    }

    public function updatedTeachingGroups($value, $key)
    {
        $parts = explode('.', $key);
        if (count($parts) < 2) return;
        
        $index = $parts[0];
        $field = $parts[1];

        
    }

    #[Computed()]
    public function approvedPurchaseRequisitions()
    {
        return PurchaseRequisition::where('status_id', 109)->get();
    }

    public function openPRModal($index)
    {
        $this->selectedParcelIndex = $index;
        $this->dispatch('modal-show', name: 'select-pr-modal');
    }

    public function selectPR($prId)
    {
        if ($this->selectedParcelIndex !== null && isset($this->parcels[$this->selectedParcelIndex])) {
            $pr = PurchaseRequisition::find($prId);
            if ($pr) {
                $this->parcels[$this->selectedParcelIndex]['purchase_requisition_id'] = $pr->id;
                $this->parcels[$this->selectedParcelIndex]['purchase_requisition_number'] = $pr->request_number;
            }
        }
        $this->dispatch('modal-close', name: 'select-pr-modal');
    }
}
