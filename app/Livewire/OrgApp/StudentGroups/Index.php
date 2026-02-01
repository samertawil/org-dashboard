<?php

namespace App\Livewire\OrgApp\StudentGroups;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use App\Models\StudentGroup;
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
    public function groups()
    {
     return  StudentGroup::query()
            ->with(['region', 'city', 'status'])
            ->where('name', 'like', '%' . $this->search . '%')
            ->orWhere('Moderator', 'like', '%' . $this->search . '%')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);      
    }

    public function delete($id)
    {
        $group = StudentGroup::findOrFail($id);
        $group->delete();
        session()->flash('message', __('Student Group successfully deleted.'));
    }

    public function render()
    {
        return view('livewire.org-app.student-groups.index');
    }
}
