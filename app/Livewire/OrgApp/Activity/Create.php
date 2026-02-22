<?php

namespace App\Livewire\OrgApp\Activity;

use App\Concerns\Activity\FormTrait;
use App\Enums\GlobalSystemConstant;
use App\Models\Activity;
use App\Models\CurrancyValue;
use App\Reposotries\CityRepo;
use App\Reposotries\employeeRepo;
use App\Reposotries\LocationRepo;
use App\Reposotries\NeighbourhoodRepo;
use App\Reposotries\RegionRepo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Validate;
use Livewire\Component;


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
        $this->addActivityPartner();
        $this->addTeachingGroup();
        $this->addFeedback();
    }

    public function updatedCost()
    {
        $currencyValue = CurrancyValue::latest('exchange_date')->first();
        $this->cost_nis = $this->cost * $currencyValue->currency_value;
    }

    public function updatedCostNis()
    {
        $currencyValue = CurrancyValue::latest('exchange_date')->first();
        $this->cost = $this->cost_nis / $currencyValue->currency_value;
        $this->cost = round($this->cost, 2);
    }

    public function save()
    {

        $this->validate();

        $activityName =  Activity::where('sector_id', $this->sector_id)
            ->whereMonth('start_date', Carbon::parse($this->start_date)->month)
            ->whereYear('start_date', Carbon::parse($this->start_date)->year)
            ->count();


        if (DB::transactionLevel() == 0) {
            DB::beginTransaction();
        }

        try {
            $project = Activity::create([
                'name' => str_contains($this->name, 'ACTIVITY #') ? 'ACTIVITY #' . ($activityName + 1) : $this->name,
                'description' => ucfirst($this->description) ?: null,
                'start_date' => $this->start_date,
                'end_date' => filled($this->end_date) ? $this->end_date : $this->start_date,
                'cost' => $this->cost ?? null,
                'cost_nis' => $this->cost_nis ?? null,
                'status' => $this->status ?:null,
            
                'region' => $this->region ?: null,
                'city' => $this->city ?: null,
                'neighbourhood' => $this->neighbourhood ?: null,
                'location' => $this->location ?: null,
                'address_details' => $this->address_details ?: null,
                'created_by' => auth()->id(),
                'sector_id' => $this->sector_id,
                'activation' => GlobalSystemConstant::ACTIVE->value,

            ]);


            if ($this->sector_id != 55) {
                        
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
                if ($team['employee_id']) {
                    $project->workTeams()->create($team);
                }
            }

            foreach ($this->activity_partners as $partner) {
                if ($partner['partner_id']) {
                    $project->activityPartners()->create($partner);
                }
            }

            if ($this->sector_id == 55) {
                foreach ($this->teaching_groups as $group) {
                    if ($group['name']) {
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

            if (DB::transactionLevel() == 1) {
                DB::commit();
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            if (DB::transactionLevel() == 1) {
                DB::rollBack();
            }
            throw $e; // Critical for Livewire to show errors
        } catch (\Throwable $th) {
            if (DB::transactionLevel() == 1) {
                DB::rollBack();
            }
            session()->flash('message', $th->getMessage());
            return redirect()->back()->with('message', $th->getMessage())->with('type', 'error');
        }


        session()->flash('message', __('Activity successfully created.'));

        return $this->redirect(route('activity.index'), navigate: true);
    }


    public function render()
    {

        if (Gate::denies('activity.create')) {
            return abort(403, 'You do not have the necessary permissions');
        }
        return view('livewire.org-app.activity.create', [
            'heading' => __('Create Activity'),
            'type' => 'save',
            'regions' => RegionRepo::regions(),
            'cities' => $this->region ? CityRepo::cities()->where('region_id', $this->region) : collect(),
            'neighbourhoods' => $this->city ? NeighbourhoodRepo::neighbourhoods()->where('city_id', $this->city) : collect(),
            'locations' => $this->neighbourhood ? LocationRepo::locations()->where('neighbourhood_id', $this->neighbourhood) : collect(),
            'employees' => employeeRepo::employees()->where('activation', GlobalSystemConstant::ACTIVE->value),
        ]);
    }
}
