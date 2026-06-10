<?php

namespace App\Livewire\OrgApp\EducationalActivityNames;

use App\Models\EducationalActivityName;
use App\Models\Status;
use App\Models\Employee;
use App\Rules\UniqueActivityCoreName;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class Create extends Component
{
    public $activity_name = '';
    public $activity_domain = null;
    public $available_in_active_groups = true;
    public $description = '';
    public $teachers = [];
    public $activation = 1;

    public function rules()
    {
        return [
            'activity_name' => ['required', 'string', 'max:255', 'unique:educational_activity_names,activity_name', new UniqueActivityCoreName()],
            'activity_domain' => 'nullable|exists:statuses,id',
            'available_in_active_groups' => 'boolean',
            'description' => 'nullable|string',
            'teachers' => 'nullable|array',
            'teachers.*' => 'exists:employees,id',
            'activation' => 'required|integer|in:0,1',
        ];
    }

    /**
     * Normalize the activity name in real-time as the user types.
     */
    public function updatedActivityName(string $value): void
    {
        $this->activity_name = EducationalActivityName::normalizeName($value);
    }

    public function save()
    {
        if (Gate::denies('educational-activity-names.create') && !auth()->user()->isSuperAdmin()) {
            abort(403, 'You do not have the necessary permissions.');
        }

        // Normalize before validation so the unique check uses the canonical form
        $this->activity_name = EducationalActivityName::normalizeName($this->activity_name);

        $this->validate();

        EducationalActivityName::create([
            'activity_name' => trim($this->activity_name),
            'activity_domain' => $this->activity_domain ?: null,
            'available_in_active_groups' => (bool) $this->available_in_active_groups,
            'description' => $this->description ?: null,
            'teachers' => $this->teachers ?: [],
            'activation' => $this->activation,
        ]);

        session()->flash('message', __('Activity name successfully created.'));

        return $this->redirect(route('educational-activity-names.index'), navigate: true);
    }

    public function render()
    {
        if (Gate::denies('educational-activity-names.create') && !auth()->user()->isSuperAdmin()) {
            abort(403, 'You do not have the necessary permissions.');
        }

        $domains = Status::where('p_id_sub', config('appConstant.educational_activity_domains', 185))
            ->orderBy('status_name')
            ->get();

        $employees = Employee::where('activation', 1)
            ->orderBy('full_name')
            ->get();

        return view('livewire.org-app.educational-activity-names.create', [
            'domains' => $domains,
            'employees' => $employees,
        ]);
    }
}
