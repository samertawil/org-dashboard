<?php

namespace App\Livewire\OrgApp\TeachingGroup;

use Livewire\Component;
use App\Models\Activity;
use Livewire\WithPagination;
use App\Models\TeachingGroup;
use App\Reposotries\CityRepo;
use App\Reposotries\RegionRepo;
use App\Reposotries\StatusRepo;
use Livewire\Attributes\Computed;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public $region_id = '';
    public $city_id = '';
    public $start_date = '';
    public $activity_id = '';
    public $status_id = '';

    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public int $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'region_id' => ['except' => ''],
        'city_id' => ['except' => ''],
        'start_date' => ['except' => ''],
        'activity_id' => ['except' => ''],
        'status_id' => ['except' => ''],
    ];

    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updated($property)
    {
        if (in_array($property, ['search', 'region_id', 'city_id', 'start_date', 'activity_id', 'status_id'])) {
            $this->resetPage();
        }
    }

    #[Computed()]
    public function groups()
    {
        return TeachingGroup::query()
            ->with(['region', 'city', 'status', 'activity'])
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->region_id, function ($q) {
                $q->where('region_id', $this->region_id);
            })
            ->when($this->city_id, function ($q) {
                $q->where('city_id', $this->city_id);
            })
            ->when($this->start_date, function ($q) {
                $q->whereDate('start_date', $this->start_date);
            })
            ->when($this->activity_id, function ($q) {
                $q->where('activity_id', $this->activity_id);
            })
            ->when($this->status_id, function ($q) {
                $q->where('status', $this->status_id);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function delete($id)
    {
        $group = TeachingGroup::findOrFail($id);
        $group->delete();
        session()->flash('message', __('Teaching Group successfully deleted.'));
    }

    public function render()
    {
        return view('livewire.org-app.teaching-group.index', [
            'regions' => RegionRepo::regions(),
            'cities' => $this->region_id ? CityRepo::cities()->where('region_id', $this->region_id) : CityRepo::cities(),
            'activities' => Activity::all(),
            'statuses' => StatusRepo::statuses(),
        ]);
    }
}
