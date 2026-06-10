<?php

namespace App\Livewire\OrgApp\Reports;

use App\Models\Region;
use App\Models\Student;
use App\Models\StudentGroup;
use App\Enums\GlobalSystemConstant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class EducationalProgress extends Component
{
    public $selectedRegion;
    public $selectedGroup;
    public $selectedBatch;

    public array $genderChartData = ['labels' => [], 'series' => []];
    public array $groupChartData = ['labels' => [], 'series' => []];
    public array $ageChartData = ['labels' => [], 'series' => []];

    public function updatedSelectedBatch(): void
    {
        $this->selectedGroup = '';
    }

    public function updatedSelectedRegion(): void
    {
        $this->selectedGroup = '';
    }

    public function render()
    {
        if (Gate::denies('reports.educational.progress')) {
            abort(403, 'You do not have the necessary permissions.');
        }

        // Fetch distinct available batch numbers from active student groups
        $batches = StudentGroup::where('activation', 1)
            ->whereNotNull('batch_no')
            ->distinct()
            ->pluck('batch_no')
            ->sort()
            ->values();

        // 1. Base Query for Students
        $studentQuery = Student::query()
            ->where('activation', 1);

        if ($this->selectedRegion) {
            $studentQuery->whereHas('studentGroup', function ($q) {
                $q->where('region_id', $this->selectedRegion);
            });
        }

        if ($this->selectedBatch) {
            $studentQuery->whereHas('studentGroup', function ($q) {
                $q->where('batch_no', $this->selectedBatch);
            });
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
        if ($this->selectedBatch) {
            $groupQuery->where('batch_no', $this->selectedBatch);
        }
        if ($this->selectedGroup) {
            $groupQuery->where('id', $this->selectedGroup);
        }
        $groups = $groupQuery->get();

        // Get groups filtered by region & batch for the dropdown selection
        $dropdownGroupsQuery = StudentGroup::where('activation', 1);
        if ($this->selectedRegion) {
            $dropdownGroupsQuery->where('region_id', $this->selectedRegion);
        }
        if ($this->selectedBatch) {
            $dropdownGroupsQuery->where('batch_no', $this->selectedBatch);
        }
        $studentGroups = $dropdownGroupsQuery->get();

        // 3. KPIs
        $totalStudents = $students->count();
        $totalGroups = $groups->count();
        $totalCapacity = $groups->sum('max_students');

        $occupancyRate = $totalCapacity > 0 ? ($totalStudents / $totalCapacity) * 100 : 0;

        // 4. Chart Data

        // Chart 1: Gender Distribution
        $genderDistribution = $students->groupBy('gender')
            ->map->count();

        // Map keys to Male/Female from GlobalSystemConstant
        $this->genderChartData = [
            'labels' => $genderDistribution->keys()->map(function ($k) {
                if ($k == GlobalSystemConstant::MALE->value) {
                    return __('Male');
                } elseif ($k == GlobalSystemConstant::FEMALE->value) {
                    return __('Female');
                }
                return $k;
            })->toArray(),
            'series' => $genderDistribution->values()->toArray()
        ];

        // Chart 2: Students per Group (Top 10)
        $groupCounts = $students->groupBy(fn($s) => $s->studentGroup->name ?? 'Unassigned')
            ->map->count()
            ->sortDesc()
            ->take(10);

        $this->groupChartData = [
            'labels' => $groupCounts->keys()->toArray(),
            'series' => $groupCounts->values()->toArray()
        ];

        // Chart 3: Age Distribution (Calculated from student_age_when_join logic)
        $ageDistribution = $students->map(function ($student) {
            $birthDate = $student->birth_date;
            $joinDate = $student->studentGroup->start_date ?? null;
            if (!$birthDate || !$joinDate) {
                return 'Unknown';
            }
            try {
                $birth = Carbon::parse($birthDate);
                $join = Carbon::parse($joinDate);
                return $birth->diffInYears($join);
            } catch (\Exception $e) {
                return 'Unknown';
            }
        })->groupBy(function ($age) {
            if ($age === 'Unknown' || $age < 0) return 'Unknown';
            if ($age < 6) return '< 6';
            if ($age <= 9) return '6-9';
            if ($age <= 12) return '10-12';
            if ($age <= 18) return '13-18';
            return '18+';
        })->map->count();

        // Ensure consistent order
        $ageOrder = ['< 6', '6-9', '10-12', '13-18', '18+', 'Unknown'];
        $sortedAgeData = [];
        foreach ($ageOrder as $key) {
            if (isset($ageDistribution[$key])) {
                $sortedAgeData[$key] = $ageDistribution[$key];
            }
        }

        $this->ageChartData = [
            'labels' => array_keys($sortedAgeData),
            'series' => array_values($sortedAgeData)
        ];

        // Fetch only regions that have active student groups
        $activeRegions = Region::whereIn('id', function ($q) {
            $q->select('region_id')
                ->from('student_groups')
                ->where('activation', 1)
                ->whereNotNull('region_id');
        })->get();

        return view('livewire.org-app.reports.educational-progress', [
            'regions' => $activeRegions,
            'studentGroups' => $studentGroups,
            'batches' => $batches,
            'kpis' => compact('totalStudents', 'totalGroups', 'occupancyRate'),
        ]);
    }
}
