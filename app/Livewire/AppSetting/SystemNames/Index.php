<?php

namespace App\Livewire\AppSetting\SystemNames;

use App\Models\SystemNames;
use Livewire\Component;

class Index extends Component
{

    public string $system_name;
    public string $description;

    
    public function index(){
        return SystemNames::get();
    }


    public function render()
    {
        return view('livewire.app-setting.system-names.index');
    }
}
