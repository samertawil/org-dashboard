<?php

namespace App\Livewire\AppSetting\Ability;

use App\Models\Ability;
use Livewire\Component;
use App\Models\ModuleName;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use App\Rules\ValidStatusConstant;
use Illuminate\Database\Eloquent\Collection;


class Edit extends Component
{
    public Ability $ability;

    public string|null $ability_name = '';
    public string $ability_description = '';
    #[Validate(['required'])]
    public int|null $module_id = null;
    public string|null $description = null;
    #[Validate(['required', new ValidStatusConstant()])]
    public  $activation ;

    protected $listeners = ['refresh-module' => '$refresh'];

    protected function rules()
    {
        return [
            'ability_name' => 'required|string|unique:abilities,ability_name,' . $this->ability->id,
            'ability_description' => 'required|string|unique:abilities,ability_description,' . $this->ability->id,
        ];
    }


    public function mount(Ability $ability)
    {
        $this->ability = $ability;
        $this->ability_name = $ability->ability_name;
        $this->ability_description = $ability->ability_description;
        $this->activation = $ability->activation;
        $this->module_id = $ability->module_id;
        $this->description = $ability->description;
    }

    public function update(): mixed
    {

        $this->validate();

        $this->ability->update([
            'ability_description' => $this->ability_description,
            'module_id' => $this->module_id,
            'description' => $this->description,
            'activation' => $this->activation,
        ]);
 
        $this->dispatch('closeModel');
        $this->dispatch('Refresh_Ability_Index');

        return redirect()->route('ability.index');
    }


    #[Computed]
    public function ModuleNames(): Collection
    {
        return ModuleName::get();
    }

    public function render()
    {
        return view('livewire.app-setting.ability.edit');
    }
}
