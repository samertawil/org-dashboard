<?php

namespace App\Livewire\OrgApp\Activity;

use App\Models\City;
use App\Models\Region;
use App\Models\Status;
use App\Models\Activity;
use Livewire\Component;
use App\Models\Employee;
use App\Models\Location;
use App\Models\Neighbourhood;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use App\Enums\GlobalSystemConstant;

class Edit extends Component
{
    public Activity $activity;


    public $name = '';

    #[Validate('nullable|string')]
    public $description = '';

    #[Validate('required|date')]
    public $start_date = '';

    #[Validate('nullable|date|after_or_equal:start_date')]
    public $end_date = '';

    #[Validate('nullable|numeric|min:0')]
    public $cost = 0;

    #[Validate('required|exists:statuses,id')]
    public $status = '';

    #[Validate('required|integer')]
    public $activation = '';

    #[Validate('nullable|exists:regions,id')]
    public $region = '';

    #[Validate('nullable|exists:cities,id')]
    public $city = '';

    #[Validate('nullable|exists:neighbourhoods,id')]
    public $neighbourhood = '';

    #[Validate('nullable|exists:locations,id')]
    public $location = '';

    #[Validate('nullable|string|max:255')]
    public $address_details = '';

    #[Validate('required|exists:statuses,id')]
    public $sector_id = '';

    public $parcels = [];
    public $beneficiaries = [];
    public $work_teams = [];

    public function mount(Activity $activity)
    {
       
        $this->activity = $activity;
        
        $this->name = $activity->name;
        $this->description = $activity->description;
        $this->start_date = $activity->start_date;
        $this->end_date = $activity->end_date;
        $this->cost = $activity->cost;
        $this->status = $activity->status;
        $this->activation = $activity->activation;
        $this->region = $activity->region;
        $this->city = $activity->city;
        $this->neighbourhood = $activity->neighbourhood;
        $this->location = $activity->location;
        $this->address_details = $activity->address_details;
        $this->sector_id = $activity->sector_id;
        
        $this->parcels = $activity->parcels->toArray();
        if (empty($this->parcels)) $this->addParcel();

        $this->beneficiaries = $activity->beneficiaries->toArray();
        if (empty($this->beneficiaries)) $this->addBeneficiary();

        $this->work_teams = $activity->workTeams->toArray();
        if (empty($this->work_teams)) $this->addWorkTeam();
    }

    public function rules() {
        return [
            'name'=>'required|string|max:255',
            // unique:activities,name,'.$this->activity->id
        ];
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
            'notes' => '',
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
            'employee_mission_title' => '',
            'employee_id' => '',
            'status_id' => '',
            'notes' => '',
        ];
    }

    public function removeWorkTeam($index)
    {
        unset($this->work_teams[$index]);
        $this->work_teams = array_values($this->work_teams);
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

    public function update()
    {
        $this->validate();

        $statusModel = Status::find($this->status);


        $this->activity->update([
            'name' => $this->name,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date ?: null,
            'cost' => $this->cost,
            'status' => $this->status,
            'status_name' => $statusModel ? $statusModel->status_name : null,
            'region' => $this->region ?: null,
            'city' => $this->city ?: null,
            'neighbourhood' => $this->neighbourhood ?: null,
            'location' => $this->location ?: null,
            'address_details' => $this->address_details,
            'sector_id' => $this->sector_id,
        ]);

        $this->activity->parcels()->delete();
        foreach ($this->parcels as $parcel) {
            if ($parcel['parcel_type'] || $parcel['distributed_parcels_count']) {
                $this->activity->parcels()->create($parcel);
            }
        }

        $this->activity->beneficiaries()->delete();
        foreach ($this->beneficiaries as $beneficiary) {
            if ($beneficiary['beneficiary_type'] || $beneficiary['beneficiaries_count']) {
                $this->activity->beneficiaries()->create($beneficiary);
            }
        }

        $this->activity->workTeams()->delete();
        foreach ($this->work_teams as $team) {
            if ($team['employee_id'] || $team['employee_mission_title']) {
                $this->activity->workTeams()->create($team);
            }
        }

        session()->flash('message', __('Activity successfully updated.'));

        return $this->redirect(route('activity.index'), navigate: true);
    }

    #[Computed()]
    public function allStatuses()
    {
        return Status::get();
    }

    public function render()
    {
        return view('livewire.org-app.activity.edit', [
            'heading' => __('Edit Activity'),
            'type' => 'update',
            'activations' => GlobalSystemConstant::options()->where('type', 'status'),
            'statuses' => Status::whereNull('p_id_sub')->get(),
            'activityStatuses' => Status::where('p_id_sub', config('appConstant.activity_status'))->get(),
            'parcelTypes' => Status::where('p_id_sub', $this->sector_id)->get(),
            'beneficiaryTypes' => Status::where('p_id_sub', 36)->get(),
            'missionTitles' => Status::where('p_id_sub', 37)->get(),
            'regions' => Region::get(),
            'cities' => $this->region ? City::where('region_id', $this->region)->get() : collect(),
            'neighbourhoods' => $this->city ? Neighbourhood::where('city_id', $this->city)->get() : collect(),
            'locations' => $this->neighbourhood ? Location::where('neighbourhood_id', $this->neighbourhood)->get() : collect(),
            'employees' => Employee::get(),
        ]);
    }
}
