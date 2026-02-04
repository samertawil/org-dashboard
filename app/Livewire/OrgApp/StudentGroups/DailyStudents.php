<?php

namespace App\Livewire\OrgApp\StudentGroups;

use Livewire\Component;
use App\Models\StudentGroup;
use Carbon\Carbon;
use Livewire\Attributes\Layout;

class DailyStudents extends Component
{
    public StudentGroup $group;
    public $date;
    public $formattedDate;
    public $dayName;

    public function mount(StudentGroup $group, $date)
    {
        $this->group = $group;
        $this->date = $date;
        $carbonDate = Carbon::parse($date);
        $this->formattedDate = $carbonDate->format('F j, Y');
        $this->dayName = $carbonDate->format('D'); // Mon, Tue, etc.
    }

    public function render()
    {
        // Check if date is within group start/end dates
        $currentDate = Carbon::parse($this->date)->startOfDay();
        $groupStart = $this->group->start_date ? Carbon::parse($this->group->start_date)->startOfDay() : null;
        $groupEnd = $this->group->end_date ? Carbon::parse($this->group->end_date)->startOfDay() : null;

        if (($groupStart && $currentDate->lt($groupStart)) || 
            ($groupEnd && $currentDate->gt($groupEnd))) {
            return view('livewire.org-app.student-groups.daily-students', [
                'students' => collect([])
            ]);
        }

        // Determine enrollment types to include based on day of week
        // Map day name to enrollment types
        // Sat -> sat_mon_wed
        // Sun -> sun_tue_thu
        // Mon -> sat_mon_wed, sun_tue_thu
        // Tue -> ? (Assuming full_week)
        // Wed -> sat_mon_wed
        // Thu -> sun_tue_thu
        // Fri -> ?
        
        // Always include 'full_week'
        $enrollmentTypes = ['full_week'];
        
        $dayOfWeek = Carbon::parse($this->date)->dayOfWeek; // 0=Sun, 1=Mon, ..., 6=Sat
        
        // 6 = Saturday
        if ($dayOfWeek === 6) {
            $enrollmentTypes[] = 'sat_mon_wed';
        }
        // 0 = Sunday
        if ($dayOfWeek === 0) {
            $enrollmentTypes[] = 'sun_tue_thu';
        }
        // 1 = Monday
        if ($dayOfWeek === 1) {
            $enrollmentTypes[] = 'sat_mon_wed';
            $enrollmentTypes[] = 'sun_tue_thu';
        }
        // 2 = Tuesday -> only full_week
        // 3 = Wednesday
        if ($dayOfWeek === 3) {
             $enrollmentTypes[] = 'sat_mon_wed';
        }
        // 4 = Thursday
        if ($dayOfWeek === 4) {
             $enrollmentTypes[] = 'sun_tue_thu';
        }
        // 5 = Friday -> only full_week

        $students = $this->group->students()
            ->whereIn('enrollment_type', $enrollmentTypes)
            ->get();

        return view('livewire.org-app.student-groups.daily-students', [
            'students' => $students
        ]);
    }
}
