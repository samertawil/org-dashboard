<?php

namespace App\Livewire\OrgApp\Activity;

use Carbon\Carbon;
use App\Models\Status;
use Livewire\Component;
use App\Models\Activity;
use App\Reposotries\CityRepo;
use App\Reposotries\RegionRepo;
use App\Reposotries\employeeRepo;
use App\Reposotries\LocationRepo;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use App\Enums\GlobalSystemConstant;
use App\Concerns\Activity\FormTrait;
use Illuminate\Support\Facades\Gate;
use App\Reposotries\NeighbourhoodRepo;
use App\Models\PartnerInstitution;
use App\Models\StudentGroup;


class Create extends Component
{
    use FormTrait;
    
    #[Validate('required|string|max:255|unique:activities,name')]
    public string $name = 'ACTIVITY #';

    
    public function mount()
    {
        $this->start_date = Carbon::now()->toDateString();
        $this->addParcel();
        $this->addBeneficiary();
        $this->addWorkTeam();
        $this->addTeachingGroup();
        $this->addFeedback();
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
                'cost' => $this->cost ?? null,
                'cost_nis' => $this->cost_nis ??null ,
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

           
            if ($this->sector_id != 55) {
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
            }

            foreach ($this->work_teams as $team) {
                if ($team['employee_id'] || $team['employee_mission_title']) {
                    $project->workTeams()->create($team);
                }
            }

            if ($this->sector_id == 55) {
                foreach ($this->teaching_groups as $group) {
                    if ($group['name']) {
                        // Ensure dates are null if empty
                        $group['start_date'] = $group['start_date'] ?: null;
                        $group['end_date'] = $group['end_date'] ?: null;

                        $project->teachingGroups()->create(array_merge($group, [
                            'created_by' => auth()->id(),
                            'updated_by' => auth()->id(),
                        ]));
                    }
                }
            }

            foreach ($this->feedbacks as $feedback) {
                if ($feedback['rating'] || $feedback['comment']) {
                    $project->feedbacks()->create($feedback);
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

 
    public function render()
    {
        if(Gate::denies('activity.create')){
            return abort(403,'You do not have the necessary permissions');
        }
        return view('livewire.org-app.activity.create', [
            'heading' => __('Create Activity'),
            'type' => 'save',

            'regions' => RegionRepo::regions(),
            'cities' => $this->region ? CityRepo::cities()->where('region_id', $this->region):collect(),
            'neighbourhoods' => $this->city ? NeighbourhoodRepo::neighbourhoods()->where('city_id', $this->city):collect(),
            'locations' => $this->neighbourhood ? LocationRepo::locations()->where('neighbourhood_id', $this->neighbourhood):collect(), 
            'employees' => employeeRepo::employees()->where('activation',GlobalSystemConstant::ACTIVE->value),
            'partners' => PartnerInstitution::all(),
            'studentGroups' => StudentGroup::all(),
        ]);
    }
}
