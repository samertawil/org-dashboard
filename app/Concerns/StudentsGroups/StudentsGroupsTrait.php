<?php

namespace App\Concerns\StudentsGroups;

use App\Enums\GlobalSystemConstant;
use App\Models\Location;
use App\Models\StudentSubjectForLearn;
use App\Reposotries\CityRepo;
use App\Reposotries\LocationRepo;
use App\Reposotries\NeighbourhoodRepo;
use App\Reposotries\PartnersRepo;
use App\Reposotries\RegionRepo;
use App\Reposotries\StatusRepo;
use Livewire\Attributes\Validate;

trait StudentsGroupsTrait
{

    #[Validate('required|integer|min:0|gt:min_students', message: ['gt:min_students' => 'Max students must exceed min students.'])]
    public $max_students = 0;


    #[Validate(
        'required|integer|min:0|lt:max_students',
        message: ['lt:max_students' => 'Min students must be less than max students.']
    )]
    public $min_students = 0;
    
    #[Validate('required|integer|min:1')]
    public $batch_no = '';

    #[Validate('nullable|exists:regions,id')]
    public $region_id = '';

    #[Validate('nullable|exists:cities,id')]
    public $city_id = '';

    #[Validate('nullable|string')]
    public $Moderator = null;

    #[Validate('nullable|string')]
    public $Moderator_phone = null;

    #[Validate('nullable|email')]
    public $Moderator_email = null;

    #[Validate('nullable|string')]
    public $description = null;

    #[Validate('required|global_validation:status')]
    public $activation = GlobalSystemConstant::ACTIVE->value;

    #[Validate('nullable|exists:statuses,id')]
    public $status_id = '';

    #[Validate('nullable|array')]
    public $subject_to_learn_id = [];


    #[Validate('nullable|exists:neighbourhoods,id')]
    public $neighbourhood_id = '';

    #[Validate('nullable|exists:locations,id')]
    public $location_id = '';

    #[Validate('nullable|exists:partner_institutions,id')]
    public $partner_institutions_id = '';

    public $address_details = '';

    #[Validate('required|date')]
    public $start_date = '';

    #[Validate('required|date|after_or_equal:start_date')]
    public $end_date = '';

    #[Validate('required|date_format:H:i')]
    public $start_time = '';

    #[Validate('required|date_format:H:i')]
    public $end_time = '';



    public $activations = [];
    public $regions = [];
    public $cities = [];
    public $neighbourhoods = [];
    public $locations = [];
    public $statuses = [];
    public $subjects = [];
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
        $this->location_id = '';
        $this->locations = collect();
    }

    public function updatedCityId()
    {
        $this->neighbourhood_id = '';
        $this->neighbourhoods = $this->city_id ? NeighbourhoodRepo::neighbourhoods()->where('city_id', $this->city_id) : collect();
        $this->location_id = '';
        $this->locations = collect();
    }

    public function updatedNeighbourhoodId()
    {
        $this->location_id = '';
        $this->locations = $this->neighbourhood_id ? LocationRepo::Locations()->where('neighbourhood_id', $this->neighbourhood_id) : collect();
    }

    public function bootStudentsGroupsTrait()
    {

        $this->regions = RegionRepo::regions();
        $this->cities = $this->region_id ? CityRepo::cities()->where('region_id', $this->region_id) : collect();
        $this->neighbourhoods = $this->city_id ? NeighbourhoodRepo::neighbourhoods()->where('city_id', $this->city_id) : collect();
        $this->locations = $this->neighbourhood_id ? LocationRepo::locations()->where('neighbourhood_id', $this->neighbourhood_id) : collect();
        $this->activations = GlobalSystemConstant::options()->where('type', 'status');
        $this->statuses = StatusRepo::statuses();
        $this->subjects = StudentSubjectForLearn::select('id', 'name')->get();
        $this->partners = PartnersRepo::partners()->where('type_id',111);
    }
}
