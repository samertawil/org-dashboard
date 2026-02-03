<?php

namespace App\Concerns\TeachingGroup;

use App\Models\Activity;
use App\Models\StudentGroup;
use App\Reposotries\CityRepo;
use App\Reposotries\RegionRepo;
use App\Reposotries\StatusRepo;
use App\Reposotries\LocationRepo;
use Livewire\Attributes\Validate;
use App\Models\PartnerInstitution;
use App\Enums\GlobalSystemConstant;
use App\Reposotries\NeighbourhoodRepo;

trait TeachingGroupTrait
{
    #[Validate('required|string|unique:teaching_groups,name')]
    public $name = '';

    #[Validate('required|exists:activities,id')]
    public $activity_id = '';

    #[Validate('nullable|exists:student_groups,id')]
    public $student_groups_id = '';

    #[Validate('nullable|exists:regions,id')]
    public $region_id = '';

    #[Validate('nullable|exists:cities,id')]
    public $city_id = '';

    #[Validate('nullable|exists:neighbourhoods,id')]
    public $neighbourhood_id = '';

    #[Validate('nullable|exists:locations,id')]
    public $location_id = '';

    #[Validate('nullable|string')]
    public $address_details = '';

    #[Validate('nullable|date')]
    public $start_date = '';

    #[Validate('nullable|date|after_or_equal:start_date')]
    public $end_date = '';

    #[Validate('nullable|string')]
    public $Moderator = '';

    #[Validate('nullable|string')]
    public $Moderator_phone = '';

    #[Validate('nullable|email')]
    public $Moderator_email = '';

    #[Validate('nullable|exists:statuses,id')]
    public $status = ''; 

    #[Validate('required|integer')]
    public $activation = GlobalSystemConstant::ACTIVE->value;

    #[Validate('required|numeric|min:0')]
    public $cost_usd = 0;

    #[Validate('required|numeric|min:0')]
    public $cost_nis = 0;

    #[Validate('nullable|exists:partner_institutions,id')]
    public $partner_id = '';

    #[Validate('nullable|string')]
    public $notes = '';

    public $activities = [];
    public $student_groups = [];
    public $regions = [];
    public $cities = [];
    public $neighbourhoods = [];
    public $locations = [];
    public $statuses = [];
    public $activations = [];
    public $partners = [];

    public function updated($property, $value)
    {
        if ($property === 'name' || $property === 'Moderator') {
            $this->$property = ucfirst($value);
        }
    }

    public function updatedRegionId()
    {
        $this->city_id = '';
        $this->cities = $this->region_id ? CityRepo::cities()->where('region_id', $this->region_id) : collect();
        $this->neighbourhood_id = '';
        $this->neighbourhoods = collect();
    }
    
     public function updatedCityId()
    {
        $this->neighbourhood_id = '';
         $this->neighbourhoods = $this->city_id ? NeighbourhoodRepo::neighbourhoods()->where('city_id', $this->city_id) : collect();
    }

    public function bootTeachingGroupTrait() {
        $this->regions = RegionRepo::regions();
        $this->cities = $this->region_id ? CityRepo::cities()->where('region_id', $this->region_id) : collect();
        $this->neighbourhoods = $this->city_id ? NeighbourhoodRepo::neighbourhoods()->where('city_id', $this->city_id) : collect();
        
        $this->locations = LocationRepo::locations();
        $this->statuses = StatusRepo::statuses();
        $this->activations = GlobalSystemConstant::options()->where('type', 'status');
        
        $this->activities = Activity::all(); 
        $this->student_groups = StudentGroup::all();
        $this->partners = PartnerInstitution::all();
    }

}
