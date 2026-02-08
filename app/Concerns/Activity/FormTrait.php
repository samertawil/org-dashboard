<?php

namespace App\Concerns\Activity;

use App\Models\TeachingGroup;
use App\Reposotries\StatusRepo;
use App\Reposotries\PartnersRepo;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use App\Reposotries\StudentGroupRepo;
 
 

trait FormTrait
{
    #[Validate('nullable|string')]
    public $description = null;

    #[Validate('required|date')]
    public $start_date = '';

    #[Validate('nullable|date|after_or_equal:start_date')]
    public $end_date = '';

    #[Validate('nullable|numeric|min:0')]
    public $cost = 0;

    #[Validate('nullable|numeric|min:0')]
    public $cost_nis = 0;

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
    // #[Validate('required|integer')]
    // public $activation =  GlobalSystemConstant::ACTIVE->value;

    #[Validate('required|exists:statuses,id')]
    public $sector_id = '';

    #[Validate('nullable|string|max:255')]
    public  $address_details = null;

    public $beneficiaryTypes=[];
    public $missionTitles=[];
    public $partners=[];
    public $studentGroups=[];
    public $activity_partners = [];
 


    public $parcels = [];
    public $beneficiaries = [];
    public $work_teams = [];

    #[Computed()]
    public function allStatuses()
    {
        return StatusRepo::statuses();
    }
    
    public function bootFormTrait()
    {
      
        $this->beneficiaryTypes = $this->allStatuses()->where('p_id_sub', config('appConstant.beneficiaries_types')) ;
        $this->missionTitles =  $this->allStatuses()->where('p_id_sub', config('appConstant.aids_primary_missions')) ;
        $this->partners = PartnersRepo::partners();
        $this->studentGroups = StudentGroupRepo::studentGroups();
        
    }

    public function addParcel()
    {
        $this->parcels[] = [
            'parcel_type' => '',
            'distributed_parcels_count' => 0,
            'cost_for_each_parcel' => 0.00,
            'status_id' => '',
            'notes' => '',
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
          
            'Moderator' => null,
            'Moderator_phone' => null,
            'Moderator_email' => null,
            'status' => null,
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

    
}
