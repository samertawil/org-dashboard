<?php

namespace App\Concerns\StudentsGroups;

use App\Reposotries\CityRepo;
use App\Reposotries\RegionRepo;
use App\Reposotries\StatusRepo;
use Livewire\Attributes\Validate;
use App\Enums\GlobalSystemConstant;

trait StudentsGroupsTrait
{
    
    #[Validate('required|integer|min:0|gt:min_students', message: ['gt:min_students' => 'Max students must exceed min students.'])]
    public $max_students = 0;
    

    #[Validate('required|integer|min:0|lt:max_students', 
    message: ['lt:max_students' => 'Min students must be less than max students.']
)]
public $min_students = 0;

    #[Validate('nullable|exists:regions,id')]
    public $region_id = '';

    #[Validate('nullable|exists:cities,id')]
    public $city_id = '';

    #[Validate('nullable|string')]
    public $Moderator = '';

    #[Validate('nullable|string')]
    public $Moderator_phone = '';

    #[Validate('nullable|email')]
    public $Moderator_email = '';

    #[Validate('nullable|string')]
    public $description = '';

    #[Validate('required|integer')]
    public $activation = GlobalSystemConstant::ACTIVE->value;

    #[Validate('nullable|exists:statuses,id')]
    public $status_id = '';

    public $activations = [];
    public $regions = [];
    public $cities = [];
    public $statuses = [];

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
    }
    public function bootStudentsGroupsTrait() {
       
        $this->regions = RegionRepo::regions();
        $this->cities = $this->region_id ? CityRepo::cities()->where('region_id', $this->region_id):collect();
        $this->activations = GlobalSystemConstant::options()->where('type', 'status');   
        $this->statuses = StatusRepo::statuses();
    }
   

}
