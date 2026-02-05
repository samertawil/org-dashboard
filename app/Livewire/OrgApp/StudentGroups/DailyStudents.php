<?php

namespace App\Livewire\OrgApp\StudentGroups;

use Livewire\Component;
use App\Models\StudentGroup;
use Carbon\Carbon;
use App\Models\StudentDailyAttendance;
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

        // We will initialize this further in render or just rely on dynamic filling
        // But to ensure all keys exist for the view, we can leave it empty and handle in view or prepopulate
        // Better to prepopulate so wire:model works cleanly
        
        // However, we need the student list first to populate defaults. 
        // Note: In Livewire mount runs before render. But obtaining students logic is in render currently.
        // We should extract student fetching logic to a private method or hydrate it.
        // For now, let's load what we have.
        foreach ($existingAttendance as $studentId => $record) {
            $this->attendance[$studentId] = ($record->status === 'present');
            $this->attendanceStatus[$studentId] = $record->status;
        }
    }

    private function getStudents()
    {
        // Check if date is within group start/end dates
        $currentDate = Carbon::parse($this->date)->startOfDay();
        $groupStart = $this->group->start_date ? Carbon::parse($this->group->start_date)->startOfDay() : null;
        $groupEnd = $this->group->end_date ? Carbon::parse($this->group->end_date)->startOfDay() : null;

        if (($groupStart && $currentDate->lt($groupStart)) || 
            ($groupEnd && $currentDate->gt($groupEnd))) {
            return collect([]);
        }

        // Determine enrollment types to include based on day of week
        $enrollmentTypes = ['full_week'];
        $dayOfWeek = Carbon::parse($this->date)->dayOfWeek; // 0=Sun, 1=Mon, ..., 6=Sat
        
        if ($dayOfWeek === 6) { $enrollmentTypes[] = 'sat_mon_wed'; } // Saturday
        if ($dayOfWeek === 0) { $enrollmentTypes[] = 'sun_tue_thu'; } // Sunday
        if ($dayOfWeek === 1) { $enrollmentTypes[] = 'sat_mon_wed'; $enrollmentTypes[] = 'sun_tue_thu'; } // Monday
        if ($dayOfWeek === 3) { $enrollmentTypes[] = 'sat_mon_wed'; } // Wednesday
        if ($dayOfWeek === 4) { $enrollmentTypes[] = 'sun_tue_thu'; } // Thursday

        return $this->group->students()
            ->whereIn('enrollment_type', $enrollmentTypes)
            ->get();
    }

    public function saveAttendance()
    {
        $students = $this->getStudents();
        
        foreach ($students as $student) {
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
                    'updated_by' => auth()->id() ?? null, // Assuming auth
                ]
            );
            
            // Update local status to reflect saved changes immediately (though dispatch usually re-renders)
            $this->attendanceStatus[$student->id] = $status;
        }

        // Flash success
        // session()->flash('message', 'Attendance saved successfully.'); 
        // Or use Flux toast if available, or just standard Livewire dispatch
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
        return view('livewire.org-app.student-groups.daily-students', [
            'students' => $this->getStudents()
        ]);
    }
}
