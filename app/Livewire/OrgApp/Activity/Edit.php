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


        $this->activity->fill([
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

        if ($this->activity->isDirty()) {
            $this->activity->save();
            $this->dispatch('refresh-data');
            session()->flash('type', 'success');
            session()->flash('message', __('Activity successfully updated.'));
        } else {
            session()->flash('message', __('No changes were made!'));
            session()->flash('type', 'warning');
        }



        // Get all ID's submitted for parcels
        $submittedParcelIds = collect($this->parcels)->filter(fn($p) => !empty($p['id']))->pluck('id')->toArray();
        // Delete parcels that are no longer in the array
       $isDeleted =  $this->activity->parcels()->whereNotIn('id', $submittedParcelIds)->delete();
      if ($isDeleted > 0) {
          $this->dispatch('refresh-data');
          session()->flash('type', 'success');
          session()->flash('message', __('Parcels successfully updated.'));
      }

        foreach ($this->parcels as $parcel) {
            if ($parcel['parcel_type']) {
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

                if (!empty($parcel['id'])) {
                    // Update existing
                    $existingParcel = $this->activity->parcels()->where('id', $parcel['id'])->first();

                    if ($existingParcel) {

                        $existingParcel->fill([
                            'parcel_type' => $parcel['parcel_type'],
                            'unit_id' => $parcel['unit_id'] ?? null,
                            'distributed_parcels_count' => $parcel['distributed_parcels_count'] ?? null,
                            'cost_for_each_parcel' => $parcel['cost_for_each_parcel'] ?? null,

                        ]);

                        if ($existingParcel->isDirty()) {
                            $existingParcel->save();
                            $this->dispatch('refresh-data');
                            session()->flash('type', 'success');
                            session()->flash('message', __('Parcels successfully updated.'));
                        }
                    }
                } else {
                    // Create new
                    $this->activity->parcels()->create($parcel);
                    $this->dispatch('refresh-data');
                    session()->flash('type', 'success');
                    session()->flash('message', __('Parcels successfully created.'));
                }
            }
        }
 

        // Get all ID's submitted for beneficiaries
        $submittedBeneficiaryIds = collect($this->beneficiaries)->filter(fn($b) => !empty($b['id']))->pluck('id')->toArray();
        // Delete beneficiaries that are no longer in the array
        $isDeletedBeneficiaries = $this->activity->beneficiaries()->whereNotIn('id', $submittedBeneficiaryIds)->delete();
        if ($isDeletedBeneficiaries > 0) {
            $this->dispatch('refresh-data');
            session()->flash('type', 'success');
            session()->flash('message', __('Beneficiaries successfully updated.'));
        }

        foreach ($this->beneficiaries as $beneficiary) {
            if ($beneficiary['beneficiary_type'] || $beneficiary['beneficiaries_count']) {
                
                if (!empty($beneficiary['id'])) {
                    // Update existing
                    $existingBeneficiary = $this->activity->beneficiaries()->where('id', $beneficiary['id'])->first();
                    
                    if ($existingBeneficiary) {
                        $existingBeneficiary->fill([
                            'beneficiary_type' => $beneficiary['beneficiary_type'],
                            'beneficiaries_count' => $beneficiary['beneficiaries_count'] ?? null,
                            'total_cost_nis' => $beneficiary['total_cost_nis'] ?? null,
                            'total_cost_usd' => $beneficiary['total_cost_usd'] ?? null,
                        ]);
                        
                        if ($existingBeneficiary->isDirty()) {
                            $existingBeneficiary->save();
                            $this->dispatch('refresh-data');
                            session()->flash('type', 'success');
                            session()->flash('message', __('Beneficiaries successfully updated.'));
                        }
                    }
                } else {
                    // Create new
                    $this->activity->beneficiaries()->create($beneficiary);
                    $this->dispatch('refresh-data');
                    session()->flash('type', 'success');
                    session()->flash('message', __('Beneficiaries successfully created.'));
                }
            }
        }

        $submittedWorkTeamIds = collect($this->work_teams)->filter(fn($wt) => !empty($wt['id']))->pluck('id')->toArray();
        $isDeletedWorkTeams = $this->activity->workTeams()->whereNotIn('id', $submittedWorkTeamIds)->delete();
        if ($isDeletedWorkTeams > 0) {
            $this->dispatch('refresh-data');
            session()->flash('type', 'success');
            session()->flash('message', __('Work Teams successfully updated.'));
        }

        foreach ($this->work_teams as $team) {
            if ($team['employee_id']) {
                if (!empty($team['id'])) {
                    $existingTeam = $this->activity->workTeams()->where('id', $team['id'])->first();
                    if ($existingTeam) {
                        $existingTeam->fill([
                            'employee_id' => $team['employee_id']
                        ]);

                        if ($existingTeam->isDirty()) {
                            $existingTeam->save();
                            $this->dispatch('refresh-data');
                            session()->flash('type', 'success');
                            session()->flash('message', __('Work Teams successfully updated.'));
                        }
                    }
                } else {
                    $this->activity->workTeams()->create($team);
                    $this->dispatch('refresh-data');
                    session()->flash('type', 'success');
                    session()->flash('message', __('Work Teams successfully created.'));
                }
            }
        }

        $submittedPartnerIds = collect($this->activity_partners)->filter(fn($ap) => !empty($ap['id']))->pluck('id')->toArray();
        $isDeletedPartners = $this->activity->activityPartners()->whereNotIn('id', $submittedPartnerIds)->delete();
        if ($isDeletedPartners > 0) {
            $this->dispatch('refresh-data');
            session()->flash('type', 'success');
            session()->flash('message', __('Activity Partners successfully updated.'));
        }

        foreach ($this->activity_partners as $partner) {
            if ($partner['partner_id']) {
                if (!empty($partner['id'])) {
                    $existingPartner = $this->activity->activityPartners()->where('id', $partner['id'])->first();
                    if ($existingPartner) {
                        $existingPartner->fill([
                            'partner_id' => $partner['partner_id']
                        ]);

                        if ($existingPartner->isDirty()) {
                            $existingPartner->save();
                            $this->dispatch('refresh-data');
                            session()->flash('type', 'success');
                            session()->flash('message', __('Activity Partners successfully updated.'));
                        }
                    }
                } else {
                    $this->activity->activityPartners()->create($partner);
                    $this->dispatch('refresh-data');
                    session()->flash('type', 'success');
                    session()->flash('message', __('Activity Partners successfully created.'));
                }
            }
        }

        $submittedTeachingGroupIds = collect($this->teaching_groups)->filter(fn($tg) => !empty($tg['id']))->pluck('id')->toArray();
        $isDeletedTeachingGroups = $this->activity->teachingGroups()->whereNotIn('id', $submittedTeachingGroupIds)->delete();
        if ($isDeletedTeachingGroups > 0) {
            $this->dispatch('refresh-data');
            session()->flash('type', 'success');
            session()->flash('message', __('Teaching Groups successfully updated.'));
        }

        if ($this->sector_id == 55) {
            foreach ($this->teaching_groups as $group) {
                if (($group['name'] ?? null)) {
                    $group['cost_usd'] = $group['cost_usd'] ?? null;
                    $group['cost_nis'] = $group['cost_nis'] ?? null;
                    $group['partner_id'] = $group['partner_id'] ?? null;
                    $group['notes'] = $group['notes'] ?? null;
                    $group['student_groups_id'] = $group['student_groups_id'] ?? null;

                    if (!empty($group['id'])) {
                        $existingGroup = $this->activity->teachingGroups()->where('id', $group['id'])->first();
                        if ($existingGroup) {
                            $existingGroup->fill([
                                'name' => $group['name'],
                                'cost_usd' => $group['cost_usd'],
                                'cost_nis' => $group['cost_nis'],
                                'partner_id' => $group['partner_id'],
                                'notes' => $group['notes'],
                                'student_groups_id' => $group['student_groups_id'],
                                'updated_by' => auth()->id(),
                            ]);

                            if ($existingGroup->isDirty()) {
                                $existingGroup->save();
                                $this->dispatch('refresh-data');
                                session()->flash('type', 'success');
                                session()->flash('message', __('Teaching Groups successfully updated.'));
                            }
                        }
                    } else {
                        $this->activity->teachingGroups()->create(array_merge($group, [
                            'updated_by' => auth()->id(),
                            'created_by' => $group['created_by'] ?? auth()->id(),
                        ]));
                        $this->dispatch('refresh-data');
                        session()->flash('type', 'success');
                        session()->flash('message', __('Teaching Groups successfully created.'));
                    }
                }
            }
        }

        $submittedFeedbackIds = collect($this->feedbacks)->filter(fn($fb) => !empty($fb['id']))->pluck('id')->toArray();
        $isDeletedFeedbacks = $this->activity->feedbacks()->whereNotIn('id', $submittedFeedbackIds)->delete();
        if ($isDeletedFeedbacks > 0) {
            $this->dispatch('refresh-data');
            session()->flash('type', 'success');
            session()->flash('message', __('Feedbacks successfully updated.'));
        }

        foreach ($this->feedbacks as $feedback) {
            if ($feedback['rating'] || $feedback['comment']) {
                if (!empty($feedback['id'])) {
                    $existingFeedback = $this->activity->feedbacks()->where('id', $feedback['id'])->first();
                    if ($existingFeedback) {
                        $existingFeedback->fill([
                            'rating' => $feedback['rating'],
                            'comment' => $feedback['comment']
                        ]);

                        if ($existingFeedback->isDirty()) {
                            $existingFeedback->save();
                            $this->dispatch('refresh-data');
                            session()->flash('type', 'success');
                            session()->flash('message', __('Feedbacks successfully updated.'));
                        }
                    }
                } else {
                    $this->activity->feedbacks()->create($feedback);
                    $this->dispatch('refresh-data');
                    session()->flash('type', 'success');
                    session()->flash('message', __('Feedbacks successfully created.'));
                }
            }
        }

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
