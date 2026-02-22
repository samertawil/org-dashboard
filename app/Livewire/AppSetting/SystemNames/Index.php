<?php

namespace App\Livewire\AppSetting\SystemNames;

use Livewire\Component;
use App\Models\SystemNames;
use Illuminate\Support\Facades\Gate;

class Index extends Component
{

    public string $system_name;
    public string $description;
    public string $search;
    
    public function index(){
        return SystemNames::get();
    }


    public function render()
    {
        if (Gate::denies('status.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        return view('livewire.app-setting.system-names.index');
    }
}
