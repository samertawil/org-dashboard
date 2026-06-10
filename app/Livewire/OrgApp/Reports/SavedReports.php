<?php

namespace App\Livewire\OrgApp\Reports;

use App\Models\Report;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Gate;

class SavedReports extends Component
{
    use WithPagination;

    public $search = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $isReadFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'isReadFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingIsReadFilter()
    {
        $this->resetPage();
    }

    public function deleteReport($reportId)
    {
        $report = Report::find($reportId);
        if ($report) {
            $user = auth()->user();
            if ($user->isSuperAdmin() || ($user->employee && $user->employee->id === $report->employee_id)) {
                $report->delete();
                session()->flash('message', __('Report deleted successfully.'));
                session()->flash('type', 'success');
            } else {
                session()->flash('message', __('You are not authorized to delete this report.'));
                session()->flash('type', 'error');
            }
        }
    }

    public function render()
    {

        $user = auth()->user();
        $query = Report::query()->with(['employee', 'addressedToEmployee']);

        if (!$user->isSuperAdmin()) {

            $employeeId = $user->employee?->id;

            if ($employeeId) {
                $query->where(function ($q) use ($employeeId) {
                    $q->where('employee_id', $employeeId)
                        ->orWhere('addressed_to_employees', $employeeId);
                });
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if (!empty(trim($this->search))) {
            $query->where('report_name', 'like', '%' . trim($this->search) . '%');
        }

        if ($this->dateFrom) {
            $query->whereDate('report_date', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('report_date', '<=', $this->dateTo);
        }

        if ($this->isReadFilter !== '') {
            $query->where('is_read', $this->isReadFilter === 'read');
        }

        $reports = $query->orderByDesc('id')->paginate(10);

        return view('livewire.org-app.reports.saved-reports', [
            'reports' => $reports,
        ]);
    }
}
