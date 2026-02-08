<?php

namespace App\Livewire\AppSetting\Status;

use App\Models\Status;
use Livewire\Component;
use App\Models\SystemNames;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\LengthAwarePaginator;

class Index extends Component
{
    use WithPagination; 
    
    // Search properties
    public string $search = '';
    public string $searchParentStatus = '';
    public string $searchSystemName = '';

    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    public $parentStatuses = [];
    public $systemNames = [];

    // Pagination
    public int $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'searchParentStatus' => ['except' => ''],
        'searchSystemName' => ['except' => ''],
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


    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSearchParentStatus(): void
    {
        $this->resetPage();
    }

    public function updatingSearchSystemName(): void
    {
        $this->resetPage();
    }

    
    public function getStatuses(): LengthAwarePaginator
    {
        return Status::query()
            ->with(['status_p_id_sub', 'systemname'])
            ->searchName($this->search)
            ->searchpId($this->searchParentStatus)
            ->searchSystemName($this->searchSystemName)
            
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function getParentStatuses()
    {
        return Status::whereNull('p_id_sub')
            ->orWhere('p_id_sub', 0)
            ->orderBy('status_name')
            ->get();
    }

    public function getSystemNames()
    {
        return SystemNames::orderBy('system_name')->get();
    }
    
    public function render()
    {
        if (Gate::denies('status.create')) {
            abort(403, 'You do not have the necessary permissions');
        }
        return view('livewire.app-setting.status.index', [
            'statuses' => $this->getStatuses(),
            'parentStatuses' => $this->getParentStatuses(),
            'systemNames' => $this->getSystemNames(),
        ]);
    }
}
