<?php

namespace App\Livewire\OrgApp\Partner;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use App\Models\PartnerInstitution;
use Illuminate\Support\Facades\Gate;

class Index extends Component
{
    use WithPagination;
    
    // Search properties
    public string $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Pagination
    public int $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
       
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


    public function updatingSearch()
    {
        $this->resetPage();
    }

    #[Computed()]
    public function partners()
    {
     return  PartnerInstitution::query()
            ->where('name', 'like', '%' . $this->search . '%')
            ->orWhere('manager_name', 'like', '%' . $this->search . '%')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);      
    }

    public function delete($id)
    {
        if (Gate::denies('partner.create')) 
        { 
            abort(403, 'You do not have the necessary permissions.');
        }
        $partner = PartnerInstitution::findOrFail($id);
        $partner->delete();
        session()->flash('message', __('Partner Institution successfully deleted.'));
    }

    public function render()
    {
        if (Gate::denies('partner.index')) 
        { 
            abort(403, 'You do not have the necessary permissions.');
        }
        return view('livewire.org-app.partner.index', );
    }
}
