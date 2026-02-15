<?php

namespace App\Livewire\OrgApp\Reports;

use App\Models\StudentDailyAttendance;
use App\Models\StudentGroup;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;

class GroupsAttendance extends Component
{
    public $dateFrom='2023-10-30';
    public $dateTo;

    public function mount()
    {
        // Default to current date
        // $this->dateFrom = Carbon::now()->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
    }

    public function render()
    {
        if (Gate::denies('reports.all')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        $groups = StudentGroup::with(['region', 'city', 'neighbourhood', 'location'])
            ->get()
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

        return view('livewire.org-app.reports.groups-attendance', [
            'groups' => $groups
        ]);
    }
}
