<?php

namespace App\Livewire\OrgApp\StudentGroups;

use Livewire\Component;
use App\Models\StudentGroup;
use App\Models\StudentGroupSchedule;
use App\Models\StudentDailyAttendance;
use App\Models\StudentSubjectForLearn;
use Carbon\Carbon;
use Livewire\Attributes\Layout;

class Report extends Component
{
    public StudentGroup $group;
    public $dateFrom;
    public $dateTo;

    public function mount(StudentGroup $group)
    {
        $this->group = $group;
        // Default to current month
        $this->dateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function render()
    {
        $query = $this->group->studentGroupSchedules()
            ->orderBy('schedule_date');

        if ($this->dateFrom) {
            $query->whereDate('schedule_date', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('schedule_date', '<=', $this->dateTo);
        }

        $schedules = $query->get();

        // Enhance schedules with attendance data and subject names
        $reportData = $schedules->map(function ($schedule) {
            $formattedDate = Carbon::parse($schedule->schedule_date)->format('Y-m-d');

            // Count attendance
            // We can batch query this outside the loop for performance, 
            // but for a single group report (usually manageable size), simple queries might suffice for V1.
            // Let's do a slightly optimized approach:
            
            $attendances = StudentDailyAttendance::where('student_group_id', $this->group->id)
                ->where('attendance_date', $formattedDate)
                ->get();

            $presentCount = $attendances->where('status', 'present')->count();
            $absentCount = $attendances->where('status', 'absent')->count();

            // Resolve subject names
            $subjectIds = $schedule->subject_to_learn_id ?? [];
            $subjectNames = [];
            if (!empty($subjectIds)) {
                $subjectNames = StudentSubjectForLearn::whereIn('id', $subjectIds)->pluck('name')->toArray();
            }

            return [
                'date' => Carbon::parse($schedule->schedule_date)->format('Y-m-d'),
                'day' => Carbon::parse($schedule->schedule_date)->format('l'),
                'is_off_day' => (bool)$schedule->is_off_day,
                'subjects' => implode(', ', $subjectNames),
                'present_count' => $presentCount,
                'absent_count' => $absentCount,
                'notes' => $schedule->notes,
            ];
        });

        return view('livewire.org-app.student-groups.report', [
            'reportData' => $reportData
        ]);
    }
}
