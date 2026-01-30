<?php

namespace App\Livewire\AppSetting\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Illuminate\Pagination\LengthAwarePaginator;

class Index extends Component
{

    use WithPagination; 

    
    // Search properties
    public string $search = '';
    public string $searchEmail = '';
    public int|null $searchActivation = null;

    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Pagination
    public int $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'searchEmail' => ['except' => ''],
        'searchActivation' => ['except' => ''],
       
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

    public function updatingSearchEmail(): void
    {    
        $this->resetPage();
    }

    public function updatingSearchActivation(): void
    {
      
        $this->resetPage();
    }
  
    #[Computed()]
    public function users(): LengthAwarePaginator
    {
        return User::query()
           
            ->SearchName($this->search)
            ->SearchEmail($this->searchEmail) 
            ->SearchActivation($this->searchActivation) 
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function resetPass(int $userId): void
    {
        $user = User::findOrFail($userId);
        dd($user);
        $user->password = 'password'; // Set default password
        $user->save();

        session()->flash('message', 'Password has been reset to default.');
    }

    public function render()
    {
        return view('livewire.app-setting.users.index');
    }
}
