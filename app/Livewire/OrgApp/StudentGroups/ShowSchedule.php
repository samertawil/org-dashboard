<?php

namespace App\Livewire\OrgApp\StudentGroups;

use Livewire\Component;
use App\Models\StudentGroup;
use Carbon\Carbon;

class ShowSchedule extends Component
{
    public StudentGroup $group;
    public $month;
    public $year;

    public function mount(StudentGroup $group)
    {
        $this->group = $group;
        // Use group start date if available, otherwise current date
        $startDate = $group->start_date ? Carbon::parse($group->start_date) : Carbon::now();
        $this->month = $startDate->month;
        $this->year = $startDate->year;
    }

    public function nextMonth()
    {
        $date = Carbon::createFromDate($this->year, $this->month, 1)->addMonth();
        $this->month = $date->month;
        $this->year = $date->year;
    }

    public function previousMonth()
    {
        $date = Carbon::createFromDate($this->year, $this->month, 1)->subMonth();
        $this->month = $date->month;
        $this->year = $date->year;
    }

    public $showEditModal = false;
    public $editingScheduleId;
    public $edit_start_time;
    public $edit_end_time;
    public $edit_is_off_day;
    public $edit_notes;
    public $edit_date;

    public function editSchedule($scheduleId)
    {
        $schedule = $this->group->studentGroupSchedules()->find($scheduleId);
        if (!$schedule) return;

        $this->editingScheduleId = $schedule->id;
        $this->edit_start_time = $schedule->start_time ? Carbon::parse($schedule->start_time)->format('H:i') : null;
        $this->edit_end_time = $schedule->end_time ? Carbon::parse($schedule->end_time)->format('H:i') : null;
        $this->edit_is_off_day = (bool) $schedule->is_off_day;
        $this->edit_notes = $schedule->notes;
        $this->edit_date = Carbon::parse($schedule->schedule_date)->format('Y-m-d');
        
        $this->showEditModal = true;
    }

    public function saveSchedule()
    {
        $rules = [
            'edit_notes' => 'nullable|string|max:255',
        ];

        // Only validate times if it is NOT an off day
        if (!$this->edit_is_off_day) {
            $rules['edit_start_time'] = 'required|date_format:H:i';
            $rules['edit_end_time'] = 'required|date_format:H:i|after:edit_start_time';
        }

        $this->validate($rules);

        $schedule = $this->group->studentGroupSchedules()->find($this->editingScheduleId);
        if (!$schedule) return;

        $hours = 0;
        if ($this->edit_start_time && $this->edit_end_time && !$this->edit_is_off_day) {
            $s = Carbon::parse($this->edit_start_time);
            $e = Carbon::parse($this->edit_end_time);
            $hours = $s->diffInHours($e);
        }

        $schedule->update([
            'start_time' => $this->edit_is_off_day ? '00:00:00' : $this->edit_start_time,
            'end_time' => $this->edit_is_off_day ? '00:00:00' : $this->edit_end_time,
            'is_off_day' => (int) $this->edit_is_off_day,
            'notes' => $this->edit_notes,
            'hours' => $hours,
        ]);

        $this->showEditModal = false;
        $this->dispatch('schedule-updated'); // Optional: for notification
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->reset(['editingScheduleId', 'edit_start_time', 'edit_end_time', 'edit_is_off_day', 'edit_notes']);
    }

    public function getEnrollmentTypesForDay($dayOfWeek)
    {
        // $dayOfWeek: 0=Sun, 1=Mon, ..., 6=Sat
        // Mappings based on EnrollmentType enum:
        // daily: 0,1,2,3,4,5,6 (All days)
        // sun_tue_thu: 0 (Sun), 2 (Tue), 4 (Thu)
        // sat_mon_wed: 6 (Sat), 1 (Mon), 3 (Wed)
        
        $types = ['daily']; // Daily students always included

        if (in_array($dayOfWeek, [0, 2, 4])) {
            $types[] = 'sun_tue_thu';
        }

        if (in_array($dayOfWeek, [6, 1, 3])) {
            $types[] = 'sat_mon_wed';
        }

        return $types;
    }

    public function render()
    {
        $startOfMonth = Carbon::createFromDate($this->year, $this->month, 1);
        $daysInMonth = $startOfMonth->daysInMonth;
        
        // Get schedules for this month
        $schedules = $this->group->studentGroupSchedules()
            ->whereYear('schedule_date', $this->year)
            ->whereMonth('schedule_date', $this->month)
            ->get()
            ->keyBy(function($item) {
                return Carbon::parse($item->schedule_date)->format('Y-m-d');
            });

        // Pre-calculate group dates
        $groupStartDate = $this->group->start_date ? Carbon::parse($this->group->start_date)->format('Y-m-d') : null;
        $groupEndDate = $this->group->end_date ? Carbon::parse($this->group->end_date)->format('Y-m-d') : null;

        // Get student counts by enrollment type for this group
        $studentCounts = $this->group->students()
            ->selectRaw('enrollment_type, count(*) as count')
            ->groupBy('enrollment_type')
            ->pluck('count', 'enrollment_type') // Returns [ "daily" => 5, "sun_tue_thu" => 10 ]
            ->toArray();

        // Generate calendar grid
        $startDayOfWeek = $startOfMonth->dayOfWeek;
        
        $calendar = [];
        $currentDate = $startOfMonth->copy();
        
        // Fill empty days at start
        for ($i = 0; $i < $startDayOfWeek; $i++) {
            $calendar[] = null;
        }

        // Fill days
        for ($i = 1; $i <= $daysInMonth; $i++) {
             $dateStr = $currentDate->format('Y-m-d');
             $dayOfWeek = $currentDate->dayOfWeek;

             // Check if date is within group start/end dates
             $inRange = true;
             if ($groupStartDate && $dateStr < $groupStartDate) {
                 $inRange = false;
             }
             if ($groupEndDate && $dateStr > $groupEndDate) {
                 $inRange = false;
             }
             
             // Check if any students exist for this day
             $enrollmentTypes = $this->getEnrollmentTypesForDay($dayOfWeek);
             $dailyStudentCount = 0;
             
             if ($inRange) {
                 foreach ($enrollmentTypes as $type) {
                     $dailyStudentCount += ($studentCounts[$type] ?? 0);
                 }
             }
             
             $calendar[] = [
                 'date' => $currentDate->copy(),
                 'day' => $i,
                 'schedule' => $schedules[$dateStr] ?? null,
                 'student_count' => $dailyStudentCount,
             ];
             $currentDate->addDay();
        }

        return view('livewire.org-app.student-groups.student-group-schedule', [
            'calendar' => $calendar,
            'currentMonthName' => $startOfMonth->format('F Y'),
        ]);
    }
}
