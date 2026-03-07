<?php

namespace App\Livewire\OrgApp\DisplacementCamps;

use App\Models\DisplacementCamp;
use App\Reposotries\CityRepo;
use App\Reposotries\RegionRepo;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search_name = '';
    public $search_moderator = '';
    public $search_region_id = '';
    public $search_city_id = '';
    public $search_address_details = '';
    public $search_camp_main_needs = '';
   
    // NOTE: The activity_id search is not implemented here because the relationship
    // or column does not exist on the DisplacementCamp model/migration.

    public function updatingSearchName() { $this->resetPage(); }
    public function updatingSearchModerator() { $this->resetPage(); }
    public function updatingSearchRegionId() { $this->resetPage(); }
    public function updatingSearchCityId() { $this->resetPage(); }
    public function updatingSearchAddressDetails() { $this->resetPage(); }
    public function updatingSearchCampMainNeeds() { $this->resetPage(); }

    #[Computed]
    public function displacementCamps()
    {
        // For the requested logic on mapping or counting attachments,
        // we map over the paginated items slightly differently or just rely on the model.
        // We will return the paginated result, and the blade view will handle `$camp->attachments`.
        
        return DisplacementCamp::with(['region', 'city'])
            ->when($this->search_name, fn($q) => $q->where('name', 'like', '%' . $this->search_name . '%'))
            ->when($this->search_moderator, fn($q) => $q->where('Moderator', 'like', '%' . $this->search_moderator . '%'))
            ->when($this->search_region_id, fn($q) => $q->where('region_id', $this->search_region_id))
            ->when($this->search_city_id, fn($q) => $q->where('city_id', $this->search_city_id))
            ->when($this->search_address_details, fn($q) => $q->where('address_details', 'like', '%' . $this->search_address_details . '%'))
            ->when($this->search_camp_main_needs, fn($q) => $q->whereJsonContains('camp_main_needs', $this->search_camp_main_needs))
            ->latest()
            ->paginate(10);
    }

    
    #[Computed]
    public function needsList()
    {
        $allNeeds = DisplacementCamp::pluck('camp_main_needs')
            ->filter()
            ->flatten()
            ->unique()
            ->values()
            ->toArray();
            
        return collect($allNeeds)->map(fn($need) => ['need' => $need])->toArray();
    }

    public function delete($id)
    {
        if(Gate::denies('displacement.camps.create')) { // assuming same rule
            abort(403, 'You do not have the necessary permissions.');
        }
        $record = DisplacementCamp::findOrFail($id);
        $record->delete();
        session()->flash('message', __('Displacement Camp deleted successfully.'));
    }

    public function render()
    {
        return view('livewire.org-app.displacement-camps.index', [
            'regions' => RegionRepo::regions(),
            'cities' => CityRepo::cities()->where('region_id', $this->search_region_id),
        ]);
    }
}
