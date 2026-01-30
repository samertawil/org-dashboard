<?php

namespace App\Livewire\AppSetting\SystemNames;

use Livewire\Component;
use App\Models\SystemNames;

class Create extends Component
{
    public string $system_name='';
    public string $description='';

    public function store()
    {
        $this->validate([
            'system_name' => 'required|unique:system_names,system_name|string|max:255',
            'description' => 'nullable|string',
        ]);

      SystemNames::create([
            'system_name' => ucfirst($this->system_name),
            'description' => ucfirst($this->description),
        ]);

        session()->flash('message', 'System Name created successfully.');

        $this->reset(['system_name', 'description']);
    }

    public function render()
    {
        return view('livewire.app-setting.system-names.create');
    }
}
