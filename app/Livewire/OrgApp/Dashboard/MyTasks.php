<?php

namespace App\Livewire\OrgApp\Dashboard;

use Livewire\Component;
use App\Models\Employee;
use App\Models\EventAssignee;
use Illuminate\Support\Facades\Auth;

class MyTasks extends Component
{
    public $tasks = [];
    public $responses = [];

    public function mount()
    {
        $this->loadTasks();
    }

    public function loadTasks()
    {
        $employee = Auth::user()->employee;

        if ($employee) {
            $this->tasks = EventAssignee::with(['event:id,name,start,end', 'assigner:id,name'])
                ->select('id', 'event_id', 'employee_id', 'created_by', 'status', 'response', 'created_at')
                ->where('employee_id', $employee->id)
                ->whereIn('status', ['pending', 'postponed', 'clarification_needed'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            $this->responses = $this->tasks->pluck('response', 'id')->toArray();
        } else {
            $this->tasks = collect([]);
        }
    }

    public function updateStatus($assigneeId, $newStatus)
    {
        $employee = Auth::user()->employee;
        $assignee = EventAssignee::find($assigneeId);

        if ($assignee && $employee && $assignee->employee_id == $employee->id) {
            $assignee->update([
                'status' => $newStatus,
                'response' => $this->responses[$assigneeId] ?? null
            ]);
            $this->loadTasks();
            $this->dispatch('task-updated'); 
        }
    }

    public function render()
    {
        return view('livewire.org-app.dashboard.my-tasks');
    }
}
