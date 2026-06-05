<?php

namespace App\Livewire\AppSetting\Users;

use App\Models\User;
use App\Models\Employee;
use App\Models\Role;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
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

    public bool $showEmployeeModal = false;
    public ?int $selectedEmployeeId = null;

    public string|int|null $searchRole = '';
    public bool $showRolesModal = false;
    public ?int $selectedUserIdForRoles = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'searchEmail' => ['except' => ''],
        'searchActivation' => ['except' => ''],
        'searchRole' => ['except' => ''],
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

    public function updatingSearchRole(): void
    {
        $this->resetPage();
    }

    #[Computed()]
    public function users(): LengthAwarePaginator
    {
        return User::query()
            ->with(['employee', 'rolesRelation'])
            ->SearchName($this->search)
            ->SearchEmail($this->searchEmail)
            ->SearchActivation($this->searchActivation)
            ->when($this->searchRole, function ($query) {
                $query->whereHas('rolesRelation', function ($q) {
                    $q->where('roles.id', $this->searchRole);
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function resetPass(int $userId): void
    {
        $user = User::findOrFail($userId);

        $user->password = 'password'; // Set default password
        $user->needs_password_reset = 1;
        $user->save();

        if (Auth::id() === $user->id) {
            Auth::guard('web')->logout();
            session()->invalidate();
            session()->regenerateToken();
            $this->redirect('/', navigate: true);
            return;
        }

        session()->flash('message', 'Password has been reset to default.');
    }

    // public function test(User $user ) {
    //     dd($user);
    // }
    public function switchActivation(User $user)
    {

        if ($user->activation == 0) {
            $user->activation = 1;
        } elseif ($user->activation == 1) {
            $user->activation = 0;
        }

        $user->save();

        session()->flash('message', 'User has been switched.');
    }

    public function showEmployee(int $userId): void
    {
        $user = User::with('employee')->findOrFail($userId);
        if ($user->employee) {
            $this->selectedEmployeeId = $user->employee->id;
            $this->showEmployeeModal = true;
        } else {
            session()->flash('message', __('This user does not have employee details.'));
        }
    }

    #[Computed()]
    public function selectedEmployee(): ?Employee
    {
        if (!$this->selectedEmployeeId) {
            return null;
        }
        return Employee::with([
            'department',
            'positionStatus',
            'maritalStatus',
            'region',
            'hiringType',
            'partner'
        ])->find($this->selectedEmployeeId);
    }

    public function closeEmployeeModal(): void
    {
        $this->showEmployeeModal = false;
        $this->selectedEmployeeId = null;
    }

    public function showUserRoles(int $userId): void
    {
        $this->selectedUserIdForRoles = $userId;
        $this->showRolesModal = true;
    }

    public function closeRolesModal(): void
    {
        $this->showRolesModal = false;
        $this->selectedUserIdForRoles = null;
    }

    #[Computed()]
    public function roles()
    {
        return Role::all();
    }

    #[Computed()]
    public function selectedUserRoles()
    {
        if (!$this->selectedUserIdForRoles) {
            return collect();
        }
        $user = User::with('rolesRelation')->find($this->selectedUserIdForRoles);
        return $user ? $user->rolesRelation : collect();
    }

    #[Computed()]
    public function selectedUserForRoles(): ?User
    {
        if (!$this->selectedUserIdForRoles) {
            return null;
        }
        return User::find($this->selectedUserIdForRoles);
    }

    public function render()
    {
        if (Gate::denies('user.index')) {
            abort(403, 'You do not have the necessary permissions');
        }

        return view('livewire.app-setting.users.index', [
            'users' => $this->users(),
        ]);
    }
}
