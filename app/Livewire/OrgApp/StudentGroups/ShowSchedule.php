<?php

namespace App\Livewire\OrgApp\StudentGroups;

use Livewire\Component;
use App\Models\StudentGroup;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ShowSchedule extends Component
{
    public StudentGroup $group;

    // Single string property: 'Y-m' (e.g. '2026-03') - simpler for Livewire serialization
    public string $currentYearMonth = '';

    // Raw group date strings stored at mount time to avoid Eloquent cast interference
    public string $groupStartDate = '';
    public string $groupEndDate   = '';

    public function mount(StudentGroup $group)
    {
        $this->group = $group;

        // Read dates raw from DB to bypass Eloquent cast interference
        // (StudentGroup has start_time/end_time cast as datetime:H:i which corrupts date parsing)
        $rawGroup = DB::table('student_groups')
            ->where('id', $group->id)
            ->select('start_date', 'end_date')
            ->first();

        $this->groupStartDate = $rawGroup->start_date ?? '';
        $this->groupEndDate   = $rawGroup->end_date   ?? '';

        // Default to current month
        $now = Carbon::now();
        $startDate = $this->groupStartDate ? Carbon::parse($this->groupStartDate) : $now;

        if ($startDate->lt($now)) {
            $this->currentYearMonth = $now->format('Y-m');
        } else {
            $this->currentYearMonth = $startDate->format('Y-m');
        }
    }

    public function nextMonth(): void
    {
        $this->currentYearMonth = Carbon::createFromFormat('Y-m', $this->currentYearMonth)
            ->addMonth()
            ->format('Y-m');
    }

    public function previousMonth(): void
    {
        $this->currentYearMonth = Carbon::createFromFormat('Y-m', $this->currentYearMonth)
            ->subMonth()
            ->format('Y-m');
    }

    public $showEditModal = false;
    public $editingScheduleId;
    public $edit_start_time;
    public $edit_end_time;
    public $edit_is_off_day;
    public $edit_notes;
    public $edit_date;
    public $edit_subjects = [];

    public function editSchedule($scheduleId)
    {
        $schedule = $this->group->studentGroupSchedules()->find($scheduleId);
        if (!$schedule) return;

        $this->editingScheduleId = $schedule->id;
        $this->edit_start_time   = $schedule->start_time ? Carbon::parse($schedule->start_time)->format('H:i') : null;
        $this->edit_end_time     = $schedule->end_time   ? Carbon::parse($schedule->end_time)->format('H:i')   : null;
        $this->edit_is_off_day   = (bool) $schedule->is_off_day;
        $this->edit_notes        = $schedule->notes;
        $this->edit_date         = Carbon::parse($schedule->schedule_date)->format('Y-m-d');
        $this->edit_subjects     = $schedule->subject_to_learn_id ?? [];
        $this->showEditModal     = true;
    }

    public function saveSchedule()
    {
        $rules = [
            'edit_notes'    => 'nullable|string|max:255',
            'edit_subjects' => 'nullable|array',
        ];

        if (!$this->edit_is_off_day) {
            $rules['edit_start_time'] = 'required|date_format:H:i';
            $rules['edit_end_time']   = 'required|date_format:H:i|after:edit_start_time';
        }

        $this->validate($rules);

        $schedule = $this->group->studentGroupSchedules()->find($this->editingScheduleId);
        if (!$schedule) return;

        $hours = 0;
        if ($this->edit_start_time && $this->edit_end_time && !$this->edit_is_off_day) {
            $hours = Carbon::parse($this->edit_start_time)->diffInHours(Carbon::parse($this->edit_end_time));
        }

        $schedule->update([
            'start_time'          => $this->edit_is_off_day ? '00:00:00' : $this->edit_start_time,
            'end_time'            => $this->edit_is_off_day ? '00:00:00' : $this->edit_end_time,
            'is_off_day'          => (int) $this->edit_is_off_day,
            'notes'               => $this->edit_notes,
            'hours'               => $hours,
            'subject_to_learn_id' => $this->edit_subjects,
        ]);

        $this->showEditModal = false;
        $this->dispatch('schedule-updated');
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->reset(['editingScheduleId', 'edit_start_time', 'edit_end_time', 'edit_is_off_day', 'edit_notes']);
    }

    public function getEnrollmentTypesForDay(int $dayOfWeek): array
    {
        $types = ['daily'];
        if (in_array($dayOfWeek, [0, 2, 4])) $types[] = 'sun_tue_thu';
        if (in_array($dayOfWeek, [6, 1, 3])) $types[] = 'sat_mon_wed';
        return $types;
    }

    public function render()
    {
        // Parse the single Y-m string into a Carbon date
        $startOfMonth = Carbon::createFromFormat('Y-m', $this->currentYearMonth)->startOfMonth();
        $endOfMonth   = $startOfMonth->copy()->endOfMonth();
        $daysInMonth  = $startOfMonth->daysInMonth;

        // Fetch schedules using LIKE for reliability (whereYear/whereMonth fail on string-stored dates)
        $schedules = $this->group->studentGroupSchedules()
            ->where('schedule_date', 'like', $this->currentYearMonth . '-%')
            ->get()
            ->keyBy(fn($item) => Carbon::parse($item->schedule_date)->format('Y-m-d'));

        // Student counts by enrollment type
        $studentCounts = $this->group->students()
            ->selectRaw('enrollment_type, count(*) as count')
            ->groupBy('enrollment_type')
            ->pluck('count', 'enrollment_type')
            ->toArray();

        // Group date boundaries (plain Y-m-d string comparison — no Carbon cast issues)
        $groupStart = $this->groupStartDate ?: null;
        $groupEnd   = $this->groupEndDate   ?: null;

        // Build calendar grid (no null padding needed for 2/4 column grid layout)
        $calendar    = [];
        $currentDate = $startOfMonth->copy();

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $dateStr   = $currentDate->format('Y-m-d');
            $dayOfWeek = $currentDate->dayOfWeek;

            $inRange = true;
            if ($groupStart && $dateStr < $groupStart) $inRange = false;
            if ($groupEnd   && $dateStr > $groupEnd)   $inRange = false;

            $dailyStudentCount = 0;
            if ($inRange) {
                foreach ($this->getEnrollmentTypesForDay($dayOfWeek) as $type) {
                    $dailyStudentCount += ($studentCounts[$type] ?? 0);
                }
            }

            $calendar[] = [
                'date'          => $currentDate->copy(),
                'day'           => $i,
                'schedule'      => $schedules->get($dateStr),
                'student_count' => $dailyStudentCount,
            ];

            $currentDate->addDay();
        }

        return view('livewire.org-app.student-groups.student-group-schedule', [
            'calendar'         => $calendar,
            'currentMonthName' => $startOfMonth->format('F Y'),
        ]);
    }
}
