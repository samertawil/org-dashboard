<?php

namespace App\Livewire\AppSetting\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Gate;
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
         
        $user->password = 'password'; // Set default password
        $user->save();

        session()->flash('message', 'Password has been reset to default.');
    }

    // public function test(User $user ) {
    //     dd($user);
    // }
    public function switchActivation(User $user) {
        
        if ($user->activation == 0) {
            $user->activation = 1;
        } elseif ($user->activation == 1) {
            $user->activation = 0;
        }
        
        $user->save();

        session()->flash('message', 'User has been switched.');
    }

    public function render()
    {
        if (Gate::denies('user.index')) {
            abort(403, 'You do not have the necessary permissions');
        }
        return view('livewire.app-setting.users.index');
    }
}
