<?php

namespace App\Livewire\OrgApp\Activity;

use App\Models\City;
use App\Models\Region;
use App\Models\Status;
use Livewire\Component;
use App\Models\Activity;
use App\Models\Employee;
use App\Models\Location;
use App\Models\Neighbourhood;
use App\Enums\GlobalSystemConstant;
use App\Concerns\Activity\FormTrait;
use Illuminate\Support\Facades\Gate;

use App\Models\PartnerInstitution;
use App\Models\StudentGroup;

class Edit extends Component
{
    public Activity $activity;
    use FormTrait;

    public $name = '';



    public function mount(Activity $activity)
    {
       
        $this->activity = $activity;
        
        $this->name = $activity->name;
        $this->description = $activity->description;
        $this->start_date = $activity->start_date;
        $this->end_date = $activity->end_date;
        $this->cost = $activity->cost;
        $this->status = $activity->status;
        $this->region = $activity->region;
        $this->city = $activity->city;
        $this->neighbourhood = $activity->neighbourhood;
        $this->location = $activity->location;
        $this->address_details = $activity->address_details;
        $this->sector_id = $activity->sector_id;
        $this->cost_nis = $activity->cost_nis;
        
        $this->parcels = $activity->parcels->toArray();
        if (empty($this->parcels)) $this->addParcel();

        $this->beneficiaries = $activity->beneficiaries->toArray();
        if (empty($this->beneficiaries)) $this->addBeneficiary();

        $this->work_teams = $activity->workTeams->toArray();
        if (empty($this->work_teams)) $this->addWorkTeam();

        $this->teaching_groups = $activity->teachingGroups->toArray();
        if (empty($this->teaching_groups)) $this->addTeachingGroup();

        $this->feedbacks = $activity->feedbacks->toArray();
        if (empty($this->feedbacks)) $this->addFeedback();
    }

    public function rules() {
        return [
            'name'=>'required|string|max:255',
            // unique:activities,name,'.$this->activity->id
        ];
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
            'cost_nis' => $this->cost_nis,
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

        $this->activity->teachingGroups()->delete();
        if ($this->sector_id == 55) {
            foreach ($this->teaching_groups as $group) {
                if ($group['name']) {
                    // Ensure dates are null if empty
                    $group['start_date'] = $group['start_date'] ?: null;
                    $group['end_date'] = $group['end_date'] ?: null;

                    $this->activity->teachingGroups()->create(array_merge($group, [
                        'updated_by' => auth()->id(),
                         // Preserve created_by if it existed in the original relation, otherwise auth()->id()
                        'created_by' => $group['created_by'] ?? auth()->id(),
                    ]));
                }
            }
        }

        $this->activity->feedbacks()->delete();
        foreach ($this->feedbacks as $feedback) {
            if ($feedback['rating'] || $feedback['comment']) {
                $this->activity->feedbacks()->create($feedback);
            }
        }

        session()->flash('message', __('Activity successfully updated.'));

        return $this->redirect(route('activity.index'), navigate: true);
    }



    public function render()
    {
        if(Gate::denies('activity.create')){
            return abort(403,'You do not have the necessary permissions');
        }
        return view('livewire.org-app.activity.edit', [
            'heading' => __('Edit Activity'),
            'type' => 'update',
            'activations' => GlobalSystemConstant::options()->where('type', 'status'),
            'statuses' => Status::whereNull('p_id_sub')->get(),
            'activityStatuses' => Status::where('p_id_sub', config('appConstant.activity_status'))->get(),
            'parcelTypes' => Status::where('p_id_sub', $this->sector_id)->get(),
            'beneficiaryTypes' => Status::where('p_id_sub', 36)->get(),
            'missionTitles' => Status::where('p_id_sub', 37)->get(),
            'regions' => \App\Reposotries\RegionRepo::regions(),
            'cities' => $this->region ? \App\Reposotries\CityRepo::cities()->where('region_id', $this->region) : collect(),
            'neighbourhoods' => $this->city ? \App\Reposotries\NeighbourhoodRepo::neighbourhoods()->where('city_id', $this->city) : collect(),
            'locations' => $this->neighbourhood ? \App\Reposotries\LocationRepo::locations()->where('neighbourhood_id', $this->neighbourhood) : collect(),
            'employees' => \App\Reposotries\employeeRepo::employees()->where('activation',GlobalSystemConstant::ACTIVE->value),
            'partners' => PartnerInstitution::all(),
            'studentGroups' => StudentGroup::all(),
        ]);
    }
}
