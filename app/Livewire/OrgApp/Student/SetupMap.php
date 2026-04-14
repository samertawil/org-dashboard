<?php

namespace App\Livewire\OrgApp\Student;

use App\Enums\GlobalSystemConstant;
use App\Models\Status;
use App\Models\Student;
use App\Models\StudentDailyAttendance;
use App\Models\StudentGroup;
use App\Models\StudentGroupSchedule;
use App\Models\SurveyQuestion;
use App\Reposotries\StudentRepo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;


class SetupMap extends Component
{

    #[Layout('layouts.app.land')]
    public function render()
    {
        // Get late students grouped by group id
        $lateStudentsData = StudentRepo::studentsSurveyLate();
        $lateStudentsGroups = collect($lateStudentsData)->where('activation', 1)->groupBy('student_groups_id');

        // 1. Get active group IDs for today and their max_students sum
        $activeGroupsQuery = StudentGroup::activeToday();
        $activeGroupsData = $activeGroupsQuery->withCount([
            'students' => function ($query) {
                $query->where('activation', 1);
            },
            'students as withdrawn_count' => function ($query) {
                $query->where('activation', 0);
            },
            'teachers',
            'studentGroupSchedules as past_schedules_count' => function ($query) {
                $query->where('schedule_date', '<=', now()->format('Y-m-d'))
                    ->where('is_off_day', 0);
            },
            'dailyAttendances as present_count' => function ($query) {
                $query->where('status', 'present')
                    ->where('attendance_date', '<=', now()->format('Y-m-d'))
                    ->whereHas('student', function ($q) {
                        $q->where('activation', 1);
                    });
            },
            'dailyAttendances as absent_count' => function ($query) {
                $query->where('status', 'absent')
                    ->where('attendance_date', '<=', now()->format('Y-m-d'))
                    ->whereHas('student', function ($q) {
                        $q->where('activation', 1);
                    });
            },
            'dailyAttendances as today_present_count' => function ($query) {
                $query->where('status', 'present')
                    ->where('attendance_date', now()->format('Y-m-d'))
                    ->whereHas('student', function ($q) {
                        $q->where('activation', 1);
                    });
            },
            'dailyAttendances as today_absent_count' => function ($query) {
                $query->where('status', 'absent')
                    ->where('attendance_date', now()->format('Y-m-d'))
                    ->whereHas('student', function ($q) {
                        $q->where('activation', 1);
                    });
            }
        ])
            ->get()
            ->map(function ($group) use ($lateStudentsGroups) {
                $group->subjects_count = count($group->subject_to_learn_id ?? []);
                // Merge late students count
                $group->late_students_count = isset($lateStudentsGroups[$group->id]) ? count($lateStudentsGroups[$group->id]) : 0;

                // Calculate missing attendance
                $expected = $group->students_count * $group->past_schedules_count;
                $actual = $group->present_count + $group->absent_count;
                $group->missing_attendance_count = max(0, $expected - $actual);

                // Calculate percentages based on entered data only
                $totalEntered = $group->present_count + $group->absent_count;
                $group->attendance_percentage = $totalEntered > 0 ? (int) round(($group->present_count / $totalEntered) * 100) : 0;
                $group->absence_percentage = $totalEntered > 0 ? (int) round(($group->absent_count / $totalEntered) * 100) : 0;

                // Calculate today's percentages
                $todayEntered = $group->today_present_count + $group->today_absent_count;
                $group->today_attendance_percentage = $todayEntered > 0 ? (int) round(($group->today_present_count / $todayEntered) * 100) : 0;
                $group->today_absence_percentage = $todayEntered > 0 ? (int) round(($group->today_absent_count / $todayEntered) * 100) : 0;

                // Calculate withdrawn percentage per group
                $totalStudentsInGroup = $group->students_count + $group->withdrawn_count;
                $group->withdrawn_percentage = $totalStudentsInGroup > 0 ? (int) round(($group->withdrawn_count / $totalStudentsInGroup) * 100) : 0;

                return $group;
            });

        $activeGroupsCount = $activeGroupsQuery->count();
        $activeGroupsIds = $activeGroupsQuery->pluck('id');
        $hasGroups = $activeGroupsIds->isNotEmpty();
        $activeGroupsNames = implode('// ', $activeGroupsQuery->pluck('name')->toArray());
        $totalMaxStudents = $activeGroupsQuery->sum('max_students');
        $activeGroupsBatchs = $activeGroupsQuery->pluck('batch_no');


        $SubjectsCounts = $activeGroupsQuery
            ->whereJsonLength('subject_to_learn_id', '>', 0)
            ->count();

        $hasSubjects =   $activeGroupsQuery
            ->whereJsonLength('subject_to_learn_id', '>', 0)
            ->exists();




        $hasStudents = Student::whereIn('student_groups_id',  $activeGroupsIds)->count();
        $studentsPercentage = $totalMaxStudents > 0 ? (int) round(($hasStudents / $totalMaxStudents) * 100) : 0;

        // Get required survey sections from survey_table
        $surveyTableSectors = DB::table('survey_table')->pluck('survey_for_section')->unique()->toArray();
        $statusSectors = Status::whereIn('id', $surveyTableSectors);
        $statusSectorsIds = $statusSectors->pluck('id')->toArray();
        $statusSectorsCount = count($statusSectorsIds);

        // Check for each batch if any required section is missing questions
        $missingSectionIds = [];
        $uniqueBatches = $activeGroupsBatchs->unique();
        $totalExpectedPairs = count($uniqueBatches) * $statusSectorsCount;
        $actualFoundPairsCount = 0;

        foreach ($uniqueBatches as $batch) {
            $addedInSectionForThisBatch = SurveyQuestion::where('batch_no', $batch)
                ->whereIn('survey_for_section', $statusSectorsIds)
                ->distinct('survey_for_section')
                ->pluck('survey_for_section')
                ->toArray();

            $missingForThisBatch = array_diff($statusSectorsIds, $addedInSectionForThisBatch);
            $missingSectionIds = array_merge($missingSectionIds, $missingForThisBatch);
            $actualFoundPairsCount += count($addedInSectionForThisBatch);
        }

        $notAddedIds = array_unique($missingSectionIds);
        $sectorNotAddedNames = Status::whereIn('id', $notAddedIds)->pluck('status_name')->toArray();

        $hasSurveys = $actualFoundPairsCount;
        $hasSurveysPersentage = $totalExpectedPairs > 0 ? (int) round(($hasSurveys / $totalExpectedPairs) * 100) : 0;
        // use Illuminate\Support\Arr;

        $statusSectorsIds = Arr::except($statusSectorsIds, 120);

        $array = $statusSectorsIds;

        $keys = array_keys($array, 120);

        $filtered = Arr::except($array, $keys);

        $gradingScaleMissingIds = [];
        foreach ($uniqueBatches as $batch) {
            foreach ($filtered  as $sectionId) {

                $stats = DB::table('survey_grading_scale_tables')
                    ->where('batch_no', $batch)
                    ->where('survey_for_section', $sectionId)
                    ->selectRaw('count(*) as count, min(from_percentage) as min_from, max(to_percentage) as max_to')
                    ->first();

                if ($stats->min_from != 0 || $stats->max_to != 100) {
                    $gradingScaleMissingIds[] = $sectionId;
                }
            }
        }

        $gradingScaleMissingNames = Status::whereIn('id', array_unique($gradingScaleMissingIds))->pluck('status_name')->toArray();
       
        // 5. حساب إحصائيات الحضور والغياب لليوم الحالي
        $enrollmentTypes = ['full_week'];
        $dayOfWeek = now()->dayOfWeek; // 0=Sun, 1=Mon, ..., 6=Sat

        if (in_array($dayOfWeek, [6, 1, 3])) {
            $enrollmentTypes[] = 'sat_mon_wed';
        } elseif (in_array($dayOfWeek, [0, 2, 4])) {
            $enrollmentTypes[] = 'sun_tue_thu';
        }

        // المجموعات التي لها جدول اليوم
        $scheduledGroupIds = StudentGroupSchedule::where('schedule_date', today()->format('Y-m-d'))
            ->whereIn('student_group_id', $activeGroupsIds)
            ->pluck('student_group_id');

        // عدد الطلاب الذين يجب إدخال الحضور والغياب لهم اليوم
        $expectedAttendanceStudentsCount = Student::whereIn('student_groups_id', $scheduledGroupIds)
            ->where('activation', GlobalSystemConstant::ACTIVE)
            ->whereIn('enrollment_type', $enrollmentTypes)
            ->count();

        // عدد الطلاب الذين تم إدخال الحضور والغياب لهم بالفعل اليوم
        $enteredAttendanceStudentsCount = StudentDailyAttendance::where('attendance_date', today()->format('Y-m-d'))
            ->whereIn('student_group_id', $scheduledGroupIds)
            ->count();

        $attendancePercentage = $expectedAttendanceStudentsCount > 0
            ? (int) round(($enteredAttendanceStudentsCount / $expectedAttendanceStudentsCount) * 100)
            : 0;

        $hasAttendance = $expectedAttendanceStudentsCount > 0 && $enteredAttendanceStudentsCount >= $expectedAttendanceStudentsCount;

        $steps = [
            $hasGroups,
            $hasSubjects,
            $hasStudents,
            $hasSurveys,
            $hasAttendance
        ];

        $completedCount = count(array_filter($steps));
        $totalSteps = count($steps);
        $progressPercentage = $totalSteps > 0 ? ($completedCount / $totalSteps) * 100 : 0;

        // Overall statistics across all active groups
        $totalAllPresent = $activeGroupsData->sum('present_count');
        $totalAllAbsent = $activeGroupsData->sum('absent_count');
        $totalAllEntered = $totalAllPresent + $totalAllAbsent;

        $totalAllTodayPresent = $activeGroupsData->sum('today_present_count');
        $totalAllTodayAbsent = $activeGroupsData->sum('today_absent_count');
        $totalAllTodayEntered = $totalAllTodayPresent + $totalAllTodayAbsent;

        $overallAttendancePercentage = $totalAllEntered > 0 ? (int) round(($totalAllPresent / $totalAllEntered) * 100) : 0;
        $overallAbsencePercentage = $totalAllEntered > 0 ? (int) round(($totalAllAbsent / $totalAllEntered) * 100) : 0;

        $todayOverallAttendancePercentage = $totalAllTodayEntered > 0 ? (int) round(($totalAllTodayPresent / $totalAllTodayEntered) * 100) : 0;
        $todayOverallAbsencePercentage = $totalAllTodayEntered > 0 ? (int) round(($totalAllTodayAbsent / $totalAllTodayEntered) * 100) : 0;

        // Overall withdrawal statistics
        $overallWithdrawnCount = $activeGroupsData->sum('withdrawn_count');
        $totalAllStudentsCombined = $activeGroupsData->sum('students_count') + $overallWithdrawnCount;
        $overallWithdrawnPercentage = $totalAllStudentsCombined > 0 ?      (($overallWithdrawnCount / $totalAllStudentsCombined) * 100) : 1;



        return view('livewire.org-app.student.setup-map', [
            'activeGroupsData' => $activeGroupsData,
            'hasGroups' => $hasGroups,
            'activeGroupsNames' => $activeGroupsNames,
            'activeGroupsCount' =>  $activeGroupsCount,
            'hasSubjects' => $hasSubjects,
            'SubjectsCounts' => $SubjectsCounts,
            'hasStudents' => $hasStudents,
            'studentsPercentage' => $studentsPercentage,
            'hasAttendance' => $hasAttendance,
            'expectedAttendanceStudentsCount' => $expectedAttendanceStudentsCount,
            'enteredAttendanceStudentsCount' => $enteredAttendanceStudentsCount,
            'attendancePercentage' => $attendancePercentage,
            'hasSurveys' => $hasSurveys,
            'hasSurveysPersentage' => $hasSurveysPersentage,
            'sectorNotAddedNames' => $sectorNotAddedNames,
            'completedCount' => $completedCount,
            'totalSteps' => $totalSteps,
            'progressPercentage' => $progressPercentage,
            'overallAttendancePercentage' => $overallAttendancePercentage,
            'overallAbsencePercentage' => $overallAbsencePercentage,
            'todayOverallAttendancePercentage' => $todayOverallAttendancePercentage,
            'todayOverallAbsencePercentage' => $todayOverallAbsencePercentage,
            'overallWithdrawnCount' => $overallWithdrawnCount,
            'overallWithdrawnPercentage' => $overallWithdrawnPercentage,
            'gradingScaleMissingNames' => $gradingScaleMissingNames,
        ]);
    }
}
