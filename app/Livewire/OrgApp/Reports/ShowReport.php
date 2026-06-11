<?php

namespace App\Livewire\OrgApp\Reports;

use App\Models\Employee;
use App\Models\Report;
use App\Models\StudentGroup;
use App\Models\EducationalActivityName;
use Livewire\Component;

class ShowReport extends Component
{
    public Report $report;

    // Resolved names for arrays of IDs
    public array $coveredGroups = [];
    public array $coveredActivities = [];
    public array $ccEmployees = [];

    public function mount(Report $report): void
    {
        $user = auth()->user();
        $isAuthorized = false;
        $employeeId = $user->employee?->id;

        if ($user->isSuperAdmin()) {
            $isAuthorized = true;
        } else {
            if ($employeeId && ($report->employee_id === $employeeId || $report->addressed_to_employees === $employeeId)) {
                $isAuthorized = true;
            }
        }

        if (!$isAuthorized) {
            abort(403, __('You are not authorized to view this report.'));
        }

        // Eager load relations
        $this->report = $report->load(['bodies', 'employee', 'addressedToEmployee', 'periodType', 'mainType', 'requiredFrom']);

        // Mark report as read if it is not already read and current user is the addressed employee
        if (!$this->report->is_read && $employeeId && $this->report->addressed_to_employees === $employeeId) {
            $this->report->update(['is_read' => true]);
        }

        // Resolve student group names
        if (!empty($this->report->student_group_ids)) {
            $this->coveredGroups = StudentGroup::whereIn('id', $this->report->student_group_ids)
                ->pluck('name')
                ->toArray();
        }

        // Resolve covered educational activity names
        if (!empty($this->report->covered_educational_activities_ids)) {
            $this->coveredActivities = EducationalActivityName::whereIn('id', $this->report->covered_educational_activities_ids)
                ->pluck('activity_name')
                ->toArray();
        }

        // Resolve CC follow up employee names
        if (!empty($this->report->follow_up_by)) {
            $this->ccEmployees = Employee::whereIn('id', $this->report->follow_up_by)
                ->pluck('full_name')
                ->toArray();
        }
    }

    public function render()
    {
        return view('livewire.org-app.reports.show-report');
    }
}
