<?php

namespace App\Livewire\OrgApp\Activity;


use App\Concerns\Activity\FormTrait;
use App\Models\Activity;
use App\Reposotries\CityRepo;
use App\Reposotries\employeeRepo;
use App\Reposotries\LocationRepo;
use App\Reposotries\NeighbourhoodRepo;
use App\Reposotries\RegionRepo;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

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

        $this->activity_partners = $activity->activityPartners->toArray();
        if (empty($this->activity_partners)) $this->addActivityPartner();

        $this->teaching_groups = $activity->teachingGroups->toArray();
        if (empty($this->teaching_groups)) $this->addTeachingGroup();

        $this->feedbacks = $activity->feedbacks->toArray();
        if (empty($this->feedbacks)) $this->addFeedback();
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            // unique:activities,name,'.$this->activity->id
        ];
    }

    public function update()
    {
        $this->validate();


        $this->activity->update([
            'name' => $this->name,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date ?: null,
            'cost' => $this->cost,
            'cost_nis' => $this->cost_nis,
            'status' => $this->status,
            'region' => $this->region ?: null,
            'city' => $this->city ?: null,
            'neighbourhood' => $this->neighbourhood ?: null,
            'location' => $this->location ?: null,
            'address_details' => $this->address_details,
            'sector_id' => $this->sector_id,
           
        ]);

        $this->activity->parcels()->delete();
        foreach ($this->parcels as $parcel) {
            if ($parcel['parcel_type'] ) {
                $this->validate([
                    'parcels.*.parcel_type' => 'required_with:parcels.*.distributed_parcels_count,parcels.*.unit_id',
                    'parcels.*.distributed_parcels_count' => 'required_with:parcels.*.parcel_type,parcels.*.unit_id|nullable|integer|min:1',
                    'parcels.*.unit_id' => 'required_with:parcels.*.parcel_type,parcels.*.distributed_parcels_count',
                ], [
                    'parcels.*.parcel_type.required_with' => 'The parcel type is required.',
                    'parcels.*.unit_id.required_with' => 'The unit type is required.',
                    'parcels.*.distributed_parcels_count.required_with' => 'The count is required.',
                    'parcels.*.distributed_parcels_count.integer' => 'The count must be an integer.',
                    'parcels.*.distributed_parcels_count.min' => 'The count must be at least 1.',
                ]);
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
            if ($team['employee_id']) {
                $this->activity->workTeams()->create($team);
            }
        }

        $this->activity->activityPartners()->delete();
        foreach ($this->activity_partners as $partner) {
            if ($partner['partner_id']) {
                $this->activity->activityPartners()->create($partner);
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

        $this->dispatch('refresh-data');
        session()->flash('message', __('Activity successfully updated.'));

        return $this->redirect(route('activity.index'), navigate: true);
    }



    public function render()
    {
        if (Gate::denies('activity.create')) {
            return abort(403, 'You do not have the necessary permissions');
        }
        return view('livewire.org-app.activity.edit', [
            'heading' => __('Edit Activity'),
            'type' => 'update',
            'regions' => RegionRepo::regions(),
            'cities' => $this->region ? CityRepo::cities()->where('region_id', $this->region) : collect(),
            'neighbourhoods' => $this->city ? NeighbourhoodRepo::neighbourhoods()->where('city_id', $this->city) : collect(),
            'locations' => $this->neighbourhood ? LocationRepo::locations()->where('neighbourhood_id', $this->neighbourhood) : collect(),
            'employees' => employeeRepo::employees(),
        ]);
    }
}
