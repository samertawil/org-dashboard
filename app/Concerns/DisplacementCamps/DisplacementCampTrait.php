<?php

namespace App\Concerns\DisplacementCamps;

use App\Models\DisplacementCamp;
use App\Reposotries\CityRepo;
use App\Reposotries\LocationRepo;
use App\Reposotries\NeighbourhoodRepo;
use App\Reposotries\RegionRepo;
use Livewire\Attributes\Validate;

trait DisplacementCampTrait
{


    #[Validate('required|exists:regions,id')]
    public $region_id;

    #[Validate('nullable|exists:cities,id')]
    public $city_id;

    #[Validate('nullable|exists:neighbourhoods,id')]
    public $neighbourhood_id;

    #[Validate('nullable|exists:locations,id')]
    public $location_id;

    #[Validate('nullable|string|max:255')]
    public $address_details;

    #[Validate('nullable|string')]
    public $longitudes;

    #[Validate('nullable|string')]
    public $latitude;

    #[Validate('nullable|integer|min:0')]
    public $number_of_families;

    #[Validate('nullable|integer|min:0')]
    public $number_of_individuals;

    #[Validate('nullable|string|max:255')]
    public $Moderator;

    #[Validate('nullable|string|max:255')]
    public $Moderator_phone;

    #[Validate('nullable|array')]
    public $camp_main_needs = [];

    #[Validate('nullable|string')]
    public $notes;

    public $regions = [];
    public $cities = [];
    public $neighbourhoods = [];
    public $locations = [];
    public $needsList = [];



    public function updatedRegionId()
    {
        $this->city_id = '';
        $this->neighbourhood_id = '';
        $this->location_id = '';
        $this->cities = $this->region_id ? CityRepo::cities()->where('region_id', $this->region_id) : collect();
        $this->neighbourhoods = collect();
        $this->locations = collect();
    }

    public function updatedCityId()
    {
        $this->neighbourhood_id = '';
        $this->location_id = '';
        $this->neighbourhoods = $this->city_id ? NeighbourhoodRepo::neighbourhoods()->where('city_id', $this->city_id) : collect();
        $this->locations = collect();
    }

    public function updatedNeighbourhoodId()
    {
        $this->location_id = '';
        $this->locations = $this->neighbourhood_id ? LocationRepo::locations()->where('neighbourhood_id', $this->neighbourhood_id) : collect();
    }

    public function bootDisplacementCampTrait()
    {
        $this->regions = RegionRepo::regions();
     
        
        // Fetch unique camp_main_needs from the database for the datalist
        // camp_main_needs is JSON so we need to collect and flatten
        $allNeeds = DisplacementCamp::pluck('camp_main_needs')
            ->filter()
            ->flatten()
            ->unique()
            ->values()
            ->toArray();
            
        $this->needsList = collect($allNeeds)->map(fn($need) => ['need' => $need])->toArray();
    }
}
