<?php

namespace App\Livewire\OrgApp\StudentGroups;

use Livewire\Component;
use App\Models\StudentGroup;
use App\Models\ActivitySchedule;
use App\Models\TeacherStudentGroup;
use Carbon\Carbon;
use App\Models\StudentDailyAttendance;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;

class DailyStudents extends Component
{
    public StudentGroup $group;
    public $date;
    public $formattedDate;
    public $dayName;
    public $attendance = []; // Array to store attendance status: [student_id => status_bool]
    public $attendanceStatus = []; // Array to store actual status string: [student_id => 'present'|'absent'|null]

    public function mount(StudentGroup $group, $date)
    {
        $this->group = $group;
        $this->date = $date;
        $carbonDate = Carbon::parse($date);
        $this->formattedDate = $carbonDate->format('F j, Y');
        $this->dayName = $carbonDate->format('D'); // Mon, Tue, etc.

        // Load existing attendance
        $existingAttendance = StudentDailyAttendance::where('student_group_id', $this->group->id)
            ->where('attendance_date', $this->date)
            ->get()
            ->keyBy('student_id');


        // Initialize attendance keys for all scheduled students so Alpine entangle works properly
        $students = $this->getStudents();
        foreach ($students as $student) {
            $record = $existingAttendance->get($student->id);
            if ($record) {
                $this->attendance[$student->id] = ($record->status === 'present');
                $this->attendanceStatus[$student->id] = $record->status;
            } else {
                $this->attendance[$student->id] = false;
                $this->attendanceStatus[$student->id] = null;
            }
        }
    }

    private function getStudents()
    {
        // Check if date is within group start/end dates
        $currentDate = Carbon::parse($this->date)->startOfDay();
        $groupStart = $this->group->start_date ? Carbon::parse($this->group->start_date)->startOfDay() : null;
        $groupEnd = $this->group->end_date ? Carbon::parse($this->group->end_date)->startOfDay() : null;

        if (($groupStart && $currentDate->lt($groupStart)) ||
            ($groupEnd && $currentDate->gt($groupEnd))
        ) {
            return collect([]);
        }

        // Determine enrollment types to include based on day of week
        $enrollmentTypes = ['full_week'];
        $dayOfWeek = Carbon::parse($this->date)->dayOfWeek; // 0=Sun, 1=Mon, ..., 6=Sat

        if ($dayOfWeek === 6) $enrollmentTypes[] = 'sat_mon_wed'; // Saturday
        if ($dayOfWeek === 0) $enrollmentTypes[] = 'sun_tue_thu'; // Sunday
        if ($dayOfWeek === 1) $enrollmentTypes[] = 'sat_mon_wed'; // Monday
        if ($dayOfWeek === 2) $enrollmentTypes[] = 'sun_tue_thu'; // Tuesday
        if ($dayOfWeek === 3) $enrollmentTypes[] = 'sat_mon_wed'; // Wednesday
        if ($dayOfWeek === 4) $enrollmentTypes[] = 'sun_tue_thu'; // Thursday

        $user = auth()->user();

        $isGroupSupervisor = TeacherStudentGroup::isGroupSupervisor($user, $this->group);

        // --- Base student query builder ---
        $baseQuery = fn() => $this->group->students()
            ->select('students.id', 'full_name', 'identity_number', 'enrollment_type', 'students.activation', 'students.status_id')
            ->with('status:id,status_name')
            ->whereIn('enrollment_type', $enrollmentTypes);

        // Super admin or full-access permission → return all students in the group
        if ($user->isSuperAdmin() || Gate::allows('select.any.student')) {
            return $baseQuery()->get();
        }

        // --- Build schedule query for this group and date ---
        $schedulesQuery = ActivitySchedule::query()
            ->where('group_id', $this->group->id)
            ->whereDate('period_start', $this->date)
            ->whereNotNull('educational_period_groups');

        if ($isGroupSupervisor) {
            // Supervisor for this group: sees all students whose status_id matches
            // any educational_period_groups scheduled for ANY teacher in this group today.
            $periodGroupIds = (clone $schedulesQuery)
                ->pluck('educational_period_groups')
                ->unique()
                ->filter()
                ->values()
                ->toArray();
        } else {
            // Regular teacher / facilitator: sees only the period group(s) assigned to THEM today.
            $employee = $user->employee;
            $periodGroupIds = (clone $schedulesQuery)
                ->where('employee_id', $employee?->id)
                ->pluck('educational_period_groups')
                ->unique()
                ->filter()
                ->values()
                ->toArray();
        }

        // No schedules found for today → show empty list
        if (empty($periodGroupIds)) {
            return collect([]);
        }

        return $baseQuery()
            ->whereIn('students.status_id', $periodGroupIds)
            ->get();
    }

    public function saveAttendance()
    {
        $students = $this->getStudents();

        // 1. Gather all status IDs of students who are marked as present in the form
        $activeStatusIds = [];
        foreach ($students as $student) {
            $isPresent = $this->attendance[$student->id] ?? false;
            if ($isPresent) {
                $activeStatusIds[] = $student->status_id;
            }
        }

        // 2. Also include status IDs that already have attendance records for today
        $existingStudentIds = StudentDailyAttendance::where('student_group_id', $this->group->id)
            ->where('attendance_date', $this->date)
            ->pluck('student_id')
            ->toArray();

        if (!empty($existingStudentIds)) {
            $existingStatusIds = $students->whereIn('id', $existingStudentIds)
                ->pluck('status_id')
                ->unique()
                ->toArray();
            $activeStatusIds = array_merge($activeStatusIds, $existingStatusIds);
        }

        $activeStatusIds = array_unique($activeStatusIds);

        // 3. Save attendance only for students in the active status groups
        foreach ($students as $student) {
            if (!in_array($student->status_id, $activeStatusIds)) {
                continue;
            }

            $isPresent = $this->attendance[$student->id] ?? false;
            $status = $isPresent ? 'present' : 'absent';

            StudentDailyAttendance::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'student_group_id' => $this->group->id,
                    'attendance_date' => $this->date,
                ],
                [
                    'status' => $status,
                    'updated_by' => auth()->id() ?? null,
                ]
            );

            $this->attendanceStatus[$student->id] = $status;
        }

        $this->dispatch('attendance-saved');
    }

    public function markAllPresent()
    {
        $students = $this->getStudents();
        foreach ($students as $student) {
            $this->attendance[$student->id] = true;
            // Note: We don't update attendanceStatus here because it's not saved yet.
            // Status update happens on save.
        }
    }

    public function render()
    {
        if (Gate::denies('student.group.date.students')) {
            abort(403, 'You do not have the necessary permissions');
        }
        $students = $this->getStudents();
        $groupedStudents = $students->groupBy('status_id');

        // Load status names for group headers
        $statusNames = \App\Models\Status::whereIn('id', $groupedStudents->keys()->filter())
            ->pluck('description', 'id');

        return view('livewire.org-app.student-groups.daily-students', [
            'students' => $students,
            'groupedStudents' => $groupedStudents,
            'statusNames' => $statusNames,
        ]);
    }
}
