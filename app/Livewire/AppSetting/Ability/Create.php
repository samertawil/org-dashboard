<?php

namespace App\Livewire\AppSetting\Ability;

use App\Models\Ability;
use Livewire\Component;
use App\Models\ModuleName;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use App\Rules\ValidStatusConstant;
use App\Enums\GlobalSystemConstant;
use Illuminate\Database\Eloquent\Collection;

class Create extends Component
{

    #[Validate(['required', 'string', 'unique:abilities,ability_name'])]
    public string $ability_name = '';
    #[Validate(['required', 'string', 'unique:abilities,ability_description'])]
    public string $ability_description = '';
    #[Validate(['required'])]
    public int|null $module_id = null;
    public string|null $description = null;
    #[Validate(['required', new ValidStatusConstant()])]
    public int $activation = GlobalSystemConstant::ACTIVE->value;

    protected $listeners = ['refresh-module' => '$refresh'];

    public function store(): void
    {
     
        $this->validate();

        Ability::create([
            'ability_name' => $this->ability_name,
            'ability_description' => $this->ability_description,
            'module_id' => $this->module_id,            
            'description' => $this->description,
            'activation' => $this->activation,
        ]);
            
        $this->dispatch('closeModel');
        $this->dispatch('Refresh_Ability_Index');


        $this->reset('ability_name', 'ability_description', 'module_id',  'description');
    }

    #[Computed]
    public function ModuleNames(): Collection {
        return ModuleName::get();
        
    }
    public function render()
    {
        return view('livewire.app-setting.ability.ability-create');
    }
}
