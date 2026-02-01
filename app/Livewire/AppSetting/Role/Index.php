<?php

namespace App\Livewire\AppSetting\Role;


use App\Models\Role;
use Livewire\Component;
use App\Traits\SortTrait;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;

class Index extends Component
{

    use WithPagination;
    use SortTrait;

    // protected string $paginationTheme = 'bootstrap';

    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';
    #[Url()]
    public string $search = '';
    #[Url()]
    public int $perPage = 5;


    public function destroy(int $id): void
    {

        Role::destroy($id);

    }

    public function edit(int $id): RedirectResponse
    {

        $roles= Role::find($id);

        session()->flash('message', 'Status created successfully.');
        return redirect()->route('dashboard.home')
        ->with( ['roles' => $roles] );

    }


    public function render(): View
    {

        if (Gate::denies('role.create')) {
            abort(403, 'You do not have the necessary permissions');
        }
        $title= __('customTrans.role group');

        $roles= Role::with('abilities')
        ->SearchName($this->search)
        ->orderBy($this->sortBy,$this->sortDir)
        ->paginate($this->perPage);

        return view('livewire.app-setting.role.index',compact('roles'))
        ->layoutData(['title' => $title, 'pageTitle'=>$title]);
    }
}
