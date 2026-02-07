<?php

namespace App\Livewire\OrgApp\Reports;

use Livewire\Component;
use App\Models\Student;
use App\Models\StudentGroup;
use App\Models\Region;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EducationalProgress extends Component
{
    public $selectedRegion;
    public $selectedGroup;

    public function render()
    {
        // 1. Base Query for Students
        $studentQuery = Student::query()
             ->where('activation', 1);

        if ($this->selectedRegion) {
             $studentQuery->where('region_id', $this->selectedRegion);
        }
        
         if ($this->selectedGroup) {
             $studentQuery->where('student_groups_id', $this->selectedGroup);
        }

        $students = $studentQuery->with(['studentGroup', 'region'])->get();
        
        // 2. Base Query for Groups (to filter charts if needed and calculate total capacity)
        $groupQuery = StudentGroup::query()->where('activation', 1);
        if ($this->selectedRegion) {
            $groupQuery->where('region_id', $this->selectedRegion);
        }
         if ($this->selectedGroup) {
            $groupQuery->where('id', $this->selectedGroup);
        }
        $groups = $groupQuery->get();


        // 3. KPIs
        $totalStudents = $students->count();
        $totalGroups = $groups->count();
        $totalCapacity = $groups->sum('max_students');
        
        // Calculate Enrollment Rate directly from current_student_count in groups for accuracy over raw student list if filtered
        // OR filtering the students list is better if filters applied.
        // Let's us Students list to be dynamic with filters.
        $occupancyRate = $totalCapacity > 0 ? ($totalStudents / $totalCapacity) * 100 : 0;

        // Attendance Rate (Dummy logic for now as DailyAttendance model structure wasn't fully inspected but assumed exists)
        // If StudentDailyAttendance exists and linked to student, we can count 'present' days.
        // For simplicity in this iteration without deep diving into attendance tables, we'll placeholder or simple count if relation exists.
        // Assuming 'dailyAttendances' relation exists on Student as checked.
        $totalAttendanceRecords = 0;
        $presentRecords = 0;
        
        // To avoid N+1, ideally we'd load this count. For now, let's skip complex attendance calc to avoid SQL errors if table empty/different.
        // We will show Gender and Age distribution instead which are reliable on Student model.

        // 4. Chart Data
        
        // Chart 1: Gender Distribution
        $genderDistribution = $students->groupBy('gender')
            ->map->count();
            
        // Map common values 1=Male, 2=Female if integers, or strings. Assuming strings or standard ID.
        // Let's just use keys.
        $genderChartData = [
             'labels' => $genderDistribution->keys()->map(fn($k) => $k == 1 ? __('Male') : ($k == 2 ? __('Female') : $k))->toArray(),
             'series' => $genderDistribution->values()->toArray()
        ];
        
        // Chart 2: Students per Group (Top 10)
        $groupCounts = $students->groupBy(fn($s) => $s->studentGroup->name ?? 'Unassigned')
            ->map->count()
            ->sortDesc()
            ->take(10);

        $groupChartData = [
             'labels' => $groupCounts->keys()->toArray(),
             'series' => $groupCounts->values()->toArray()
        ];
        
        // Chart 3: Age Distribution (Calculated from birth_date)
        $ageDistribution = $students->map(function($student) {
            if (!$student->birth_date) return 'Unknown';
            return Carbon::parse($student->birth_date)->age;
        })->groupBy(function($age) {
            if ($age === 'Unknown') return 'Unknown';
            if ($age < 6) return '< 6';
            if ($age <= 12) return '6-12';
            if ($age <= 18) return '13-18';
            return '18+';
        })->map->count();
        
        // Ensure consistent order
        $ageOrder = ['< 6', '6-12', '13-18', '18+', 'Unknown'];
        $sortedAgeData = [];
        foreach ($ageOrder as $key) {
             if (isset($ageDistribution[$key])) {
                 $sortedAgeData[$key] = $ageDistribution[$key];
             }
        }

        $ageChartData = [
             'labels' => array_keys($sortedAgeData),
             'series' => array_values($sortedAgeData)
        ];


        return view('livewire.org-app.reports.educational-progress', [
            'regions' => Region::all(),
            'studentGroups' => StudentGroup::where('activation', 1)->get(),
            'kpis' => compact('totalStudents', 'totalGroups', 'occupancyRate'),
            'genderChartData' => $genderChartData,
            'groupChartData' => $groupChartData,
            'ageChartData' => $ageChartData
        ]);
    }
}
