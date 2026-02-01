<?php

namespace App\Livewire\AppSetting\Role;

use App\Models\Role;
use App\Models\Ability;
use Livewire\Component;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class Edit extends Component
{
     
    public string $name;
    public array $abilitiesId = [];
    public mixed $roles;
    public $data;
    public $editId;
    public function mount(mixed $id = ''): void
    {
        $data = Role::find($id);
        $this->editId = $data->id;

        $this->roles = $data;
        $this->name = $data->name ?? '';

        if ($id) {
            $this->abilitiesId = $data->getAttribute('abilities') ?? [];
        }
    }

    
    protected function rules()
    {
        return [
            'name' => 'required|string|unique:roles,name,' . $this->editId,      
        ];
    }
    public function toggleAll(): void
    {
        $allAbilities = Ability::where('activation', '!=', '0')->pluck('ability_name')->toArray();

        if (count(array_intersect($allAbilities, $this->abilitiesId)) === count($allAbilities)) {
            $this->abilitiesId = array_values(array_diff($this->abilitiesId, $allAbilities));
        } else {
            $this->abilitiesId = array_values(array_unique(array_merge($this->abilitiesId, $allAbilities)));
        }
    }

    public function toggleModule($moduleId): void
    {
        $moduleAbilities = Ability::where('module_id', $moduleId)
            ->where('activation', '!=', '0')
            ->pluck('ability_name')
            ->toArray();

        $allInModuleSelected = count(array_intersect($moduleAbilities, $this->abilitiesId)) === count($moduleAbilities);

        if ($allInModuleSelected) {
            $this->abilitiesId = array_values(array_diff($this->abilitiesId, $moduleAbilities));
        } else {
            $this->abilitiesId = array_values(array_unique(array_merge($this->abilitiesId, $moduleAbilities)));
        }
    }

    public function update()
    {
        $this->validate(); 
        $abilitiesDescription = [];
        $abilities = [];

        foreach ($this->abilitiesId as $ability_name) {
            $ability = Ability::select('ability_description', 'ability_name')->where('ability_name', $ability_name)->first();
            $abilitiesDescription[] = $ability->ability_description ?? null;
            $abilities[] = $ability->ability_name ?? null;
        }

        Role::where('id', $this->roles['id'])->update([
                'name' => $this->name,
                'abilities' => $abilities,
                'abilities_description' => $abilitiesDescription,
                'created_by' => Auth::id(),
            ]);

            session()->flash('message', 'Role updated successfully.');

        return $this->redirect(route('role.index'), navigate: true);
    }

   



    public function render(): View
    {
        if (Gate::denies('role.create')) {
            abort(403, 'You do not have the necessary permissions');
        }
        $pageTitle = __('Edit Role');
        $abilities_module = Ability::with('module_name')->select('module_id')->groupby('module_id')->get();

        $abilities = Ability::select('id', 'module_id', 'ability_description', 'ability_name', 'activation')->with('module_name')->withoutGlobalScope('not-active')->get();



        return view('livewire.app-setting.role.edit', compact('abilities_module', 'abilities'))->layoutData(['pageTitle' => $pageTitle, 'title' => $pageTitle]);;
    }
}
