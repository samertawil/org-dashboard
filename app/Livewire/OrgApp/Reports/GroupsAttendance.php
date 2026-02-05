<?php

namespace App\Livewire\OrgApp\Reports;

use Livewire\Component;
use App\Models\StudentGroup;
use App\Models\StudentDailyAttendance;
use Carbon\Carbon;
use Livewire\Attributes\Layout;

class GroupsAttendance extends Component
{
    public $dateFrom;
    public $dateTo;

    public function mount()
    {
        // Default to current date
        $this->dateFrom = Carbon::now()->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
    }

    public function render()
    {
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
