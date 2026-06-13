<?php

namespace App\Livewire\OrgApp\Reports;

use App\Models\StudentDailyAttendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use App\Reposotries\StudentGroupRepo;
use Livewire\Component;

class GroupsAttendance extends Component
{
    public $dateFrom;
    public $dateTo;
    public bool $isLazy = false;
    public bool $loadData = false;

    public function mount($dateFrom = null, $dateTo = null)
    {
        $this->dateFrom = $dateFrom ?? Carbon::now()->format('Y-m-d');
        $this->dateTo = $dateTo ?? Carbon::now()->format('Y-m-d');
    }

    public function render()
    {
        if (Gate::allows('reports.all') || Gate::allows('reports.groups.attendance') || Gate::allows('student.group.date.students')) {
            $groups = collect();

            if (!($this->isLazy && !$this->loadData)) {
                $groups = StudentGroupRepo::activateEducationPointsWithEmployee()
                    ->map(function ($group) {
                        // Calculate attendance within the date range
                        $attendanceQuery = StudentDailyAttendance::where('student_group_id', $group->id);

                        if ($this->dateFrom) {
                            $attendanceQuery->whereDate('attendance_date', '>=', $this->dateFrom);
                        }
                        if ($this->dateTo) {
                            $attendanceQuery->whereDate('attendance_date', '<=', $this->dateTo);
                        }

                        $attendances = $attendanceQuery->get();

                        return [
                            'name' => $group->name,
                            'region' => $group->region?->region_name ?? '-',
                            'city' => $group->city?->city_name ?? '-',
                            'neighbourhood' => $group->neighbourhood?->neighbourhood_name ?? '-',
                            'present_count' => $attendances->where('status', 'present')->count(),
                            'absent_count' => $attendances->where('status', 'absent')->count(),
                        ];
                    });
            }

            return view('livewire.org-app.reports.groups-attendance', [
                'groups' => $groups
            ]);
        }

        abort(403, 'You do not have the necessary permissions.');
    }
}
