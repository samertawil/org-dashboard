<?php

namespace App\Livewire\AppSetting\Status;

use App\Models\Status;
use Livewire\Component;
use App\Models\SystemNames;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;

class Edit extends Component
{

    public string  $status_name = '';
    public int|null  $p_id_sub = null;
    public int|null  $c_id_sub = null;
    public int|null $used_in_system_id = null;
    public string|null $description = null;
    public mixed $data='';
    public Status $status;

    public function mount(Status $status)
    {    
        $this->data = $status;
        $this->status_name = $this->data->status_name;
        $this->p_id_sub = $this->data->p_id_sub;    
        $this->c_id_sub = $this->data->c_id_sub;    
        $this->used_in_system_id = $this->data->used_in_system_id;
        $this->description = $this->data->description;
        
       
    }
    public function update(): mixed
    {
        $this->validate([
            'status_name' => 'required|string|max:255|unique:statuses,status_name,' . $this->data->id . ',id',
        ]);
       
        $this->data->update([
            'status_name' => ucfirst( $this->status_name),
            'p_id_sub' => $this->p_id_sub,  
            'used_in_system_id' => $this->used_in_system_id,
            'description' => ucfirst($this->description),
        ]);

        session()->flash('message', 'Status Updated successfully.');

       return redirect()->route('status.index');
    }

    public function getParentStatuses()
    {
        return Status::whereNull('p_id_sub')
            ->orWhere('p_id_sub', 0)
            ->orderBy('status_name')
            ->get();
    }
    public function getChildStatuses()
    {
        return Status::whereNotNull('p_id_sub')
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    

    public function getSystemNames()
    {
        return SystemNames::orderBy('system_name')->get();
    }


    public function render()
    {
        if (Gate::denies('status.create')) {
            abort(403, 'You do not have the necessary permissions');
        }
        return view('livewire.app-setting.status.edit', [

            'parentStatuses' => $this->getParentStatuses(),
            'childStatuses' => $this->getChildStatuses(),
            'systemNames' => $this->getSystemNames(),
            
        ]);
    }
}
