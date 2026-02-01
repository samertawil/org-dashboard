<?php

namespace App\Livewire\AppSetting\Status;

use App\Models\Status;
use Livewire\Component;
use App\Models\SystemNames;
use Illuminate\Support\Facades\Gate;

class Create extends Component
{
    // Form properties
    public string  $status_name = '';
    public int|null  $p_id_sub = null;
    public int|null  $c_id_sub = null;
    public int|null $used_in_system_id = null;
    public string|null $description = null;


    public function store(): void
    {

        $this->validate([
            'status_name' => 'required|string|max:255|unique:statuses,status_name',
        ]);


        Status::create([
            'status_name' => ucfirst($this->status_name),
            'p_id_sub' => $this->p_id_sub,
            'used_in_system_id' => $this->used_in_system_id,
            'description' => ucfirst($this->description),
            'c_id_sub' => $this->c_id_sub,
        ]);

        session()->flash('message', 'Status created successfully.');

        $this->reset(['status_name', 'p_id_sub', 'used_in_system_id', 'description', 'c_id_sub']);
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
        return Status::whereNotNull('p_id_sub')->whereNull('c_id_sub')
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
        return view('livewire.app-setting.status.create', [

            'parentStatuses' => $this->getParentStatuses(),
            'childStatuses' => $this->getChildStatuses(),
            'systemNames' => $this->getSystemNames(),
        ]);
    }
}
