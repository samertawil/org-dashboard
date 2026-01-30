<?php

namespace App\Livewire\OrgApp\Activity;

use Carbon\Carbon;
use App\Models\City;
use App\Models\Region;
use App\Models\Status;
use Livewire\Component;
use App\Models\Activity;
use App\Models\Employee;
use App\Models\Location;
use App\Models\Neighbourhood;
use App\Reposotries\StatusRepo;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;


class Create extends Component
{
    #[Validate('required|string|max:255|unique:activities,name')]
    public string $name = 'ACTIVITY #';

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

    // #[Validate('required|integer')]
    // public $activation =  GlobalSystemConstant::ACTIVE->value;

    #[Validate('nullable|exists:regions,id')]
    public int|null $region = null;

    #[Validate('nullable|exists:cities,id')]
    public int|null $city = null;

    #[Validate('nullable|exists:neighbourhoods,id')]
    public int|null $neighbourhood = null;

    #[Validate('nullable|exists:locations,id')]
    public int|null $location = null;

    #[Validate('nullable|string|max:255')]
    public string|null$address_details = null;

    #[Validate('required|exists:statuses,id')]
    public int|null $sector_id = null;

    public $parcels = [];
    public $beneficiaries = [];
    public $work_teams = [];

    public function mount()
    {
        $this->start_date = Carbon::now()->toDateString();
        $this->addParcel();
        $this->addBeneficiary();
        $this->addWorkTeam();

    
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

    public function save()
    {

        $this->validate();

        $activityName=  Activity::where('sector_id', $this->sector_id)
        ->whereMonth('start_date', Carbon::parse($this->start_date)->month)
        ->whereYear('start_date', Carbon::parse($this->start_date)->year)
        ->count();

        $statusModel = Status::find($this->status);

        DB::beginTransaction();

        try {
            $project = Activity::create([
                'name' =>  'ACTIVITY #' . ($activityName + 1),
                'description' => ucfirst($this->description),
                'start_date' => $this->start_date,
                'end_date' => filled($this->end_date) ? $this->end_date : $this->start_date,
                'cost' => $this->cost ?? 0,
                'status' => $this->status,
                'status_name' => $statusModel ? $statusModel->status_name : null,
                'region' => $this->region ?: null,
                'city' => $this->city ?: null,
                'neighbourhood' => $this->neighbourhood ?: null,
                'location' => $this->location ?: null,
                'address_details' => $this->address_details,
                'created_by' => auth()->id(),
                'sector_id' => $this->sector_id

            ]);

            foreach ($this->parcels as $parcel) {
                if ($parcel['parcel_type'] || $parcel['distributed_parcels_count']) {
                    $project->parcels()->create($parcel);
                }
            }

            foreach ($this->beneficiaries as $beneficiary) {
                if ($beneficiary['beneficiary_type'] || $beneficiary['beneficiaries_count']) {
                    $project->beneficiaries()->create($beneficiary);
                }
            }

            foreach ($this->work_teams as $team) {
                if ($team['employee_id'] || $team['employee_mission_title']) {
                    $project->workTeams()->create($team);
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
          
            return redirect()->back()->with('message', $th->getMessage())->with('type', 'error');
            DB::rollBack();
            return;
        }


        session()->flash('message', __('Activity successfully created.'));

        return $this->redirect(route('activity.index'), navigate: true);
    }

    // #[Computed()]
    // public function allStatuses()
    // {
    //     return Status::get();
    // }

    #[Computed()]
    public function allStatuses()
    {
        return StatusRepo::statuses();
    }

 
    public function render()
    {
        return view('livewire.org-app.activity.create', [
            'heading' => __('Create Activity'),
            'type' => 'save',
            'statuses' => Status::whereNull('p_id_sub')->get(),
            'activityStatuses' => Status::where('p_id_sub', config('appConstant.activity_status'))->get(),
            'parcelTypes' => Status::where('p_id_sub', $this->sector_id)->get(),
            'regions' => Region::get(),
            'cities' => $this->region ? City::where('region_id', $this->region)->get() : collect(),
            'neighbourhoods' => $this->city ? Neighbourhood::where('city_id', $this->city)->get() : collect(),
            'locations' => $this->neighbourhood ? Location::where('neighbourhood_id', $this->neighbourhood)->get() : collect(),
            'employees' => Employee::get(),
        ]);
    }
}
