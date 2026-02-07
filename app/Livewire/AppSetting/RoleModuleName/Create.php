<?php

namespace App\Livewire\AppSetting\RoleModuleName;

use Livewire\Component;
use App\Models\ModuleName;
use App\Enums\GlobalSystemConstant;
use Illuminate\Support\Facades\Gate;
 

class Create extends Component
{

    public string $name = '';
    public string $description = '';
    public int $active= GlobalSystemConstant::ACTIVE->value ;
 
    public function rules(): mixed {
        return [
            'name'=>['required','unique:module_names,name','string'],
            'active' => [
                'required',
                function ($value, $fail) {
                    $enum = GlobalSystemConstant::tryFrom((int)$value);
                    if (! $enum || $enum->getType() !== 'status') {
                        $fail('The selected module name is invalid.');
                    }
                },
            ],
        ];
    }

    public function store(): void {
       
        $this->validate();
       
        ModuleName::create([
            'name'=>$this->name,
            'description'=>$this->description,
            'active'=>$this->active,
        ]);
 
        session()->flash('message', 'Status created successfully.');
        $this->dispatch('reload-module');
        $this->dispatch('refresh-module');
        $this->reset();

    }

    public function render()
    {
        if (Gate::denies('ability.create')) {
            abort(403, 'You do not have the necessary permissions');
        }
        return view('livewire.app-setting.role-module-name.create');
    }
}
