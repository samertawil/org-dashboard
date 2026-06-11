<?php

use App\Models\User;
use App\Models\Student;
use App\Models\StudentGroup;
use App\Models\ActivitySchedule;
use App\Models\EducationalActivityDetail;
use App\Models\StudentDailyAttendance;
use App\Models\Status;
use App\Models\EducationalActivityName;
use Livewire\Livewire;
use App\Livewire\OrgApp\Reports\EducationDirectorDashboard;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Carbon;

beforeEach(function () {
    Carbon::setTestNow('2026-06-08 10:00:00');

    // Define standard gates
    Gate::define('manager.reports.all', fn() => false);
    Gate::define('reports.all', fn() => false);

    // Seed educational activity domain statuses
    Status::forceCreate([
        'id' => 187,
        'status_name' => 'التعليم',
        'p_id_sub' => 185,
    ]);
    Status::forceCreate([
        'id' => 188,
        'status_name' => 'الدعم النفسي',
        'p_id_sub' => 185,
    ]);
    Status::forceCreate([
        'id' => 190,
        'status_name' => 'مهارات وقيم تربوية',
        'p_id_sub' => 185,
    ]);

    // Seed period groups status
    Status::forceCreate([
        'id' => 200,
        'status_name' => 'الفترة الأولى',
        'p_id_sub' => 124, // educational_period_groups parent
    ]);
});

afterEach(function () {
    Carbon::setTestNow();
});

it('aborts with 403 for unauthorized users', function () {
    $regularUser = User::factory()->create(['id' => 999]);
    $this->actingAs($regularUser);

    Livewire::test(EducationDirectorDashboard::class)
        ->assertStatus(403);
});

it('allows authorized users to access', function () {
    $adminUser = User::factory()->create(['id' => 1]); // ID 1 is superadmin
    $this->actingAs($adminUser);

    Livewire::test(EducationDirectorDashboard::class)
        ->assertStatus(200)
        ->assertViewHas('metrics');
});

it('calculates dashboard metrics correctly for executed activities and attendance', function () {
    $adminUser = User::factory()->create(['id' => 1]);
    $this->actingAs($adminUser);

    // Create a valid Educational Activity Name to satisfy foreign key constraints
    $actName = EducationalActivityName::create([
        'activity_name' => 'Test Activity Name',
        'activation' => 1,
    ]);

    // 1. Create a Student Group
    $group = StudentGroup::create([
        'name' => 'Test Group',
        'batch_no' => 'B1',
        'activation' => 1,
    ]);

    // 2. Create a Student in this group with status_id matching period group status 200
    $student = Student::create([
        'identity_number' => 123456789,
        'full_name' => 'Test Student',
        'birth_date' => '2018-01-01',
        'student_groups_id' => $group->id,
        'activation' => 1,
        'gender' => 1, // Male/Female Enum or int
        'status_id' => 200, // matches period group status 200
    ]);

    // 3. Create active activity schedule that is completed (has detail) and matches domain 187
    $schedule = ActivitySchedule::create([
        'group_id' => $group->id,
        'activity_name' => $actName->id,
        'educational_activity_domain' => 187, // Education
        'target_category' => 'children',
        'period_start' => '2026-06-08 09:00:00',
        'period_end' => '2026-06-08 10:00:00',
        'educational_period_groups' => 200, // period group
        'activation' => 1,
    ]);

    // Create details to make the schedule "completed"
    EducationalActivityDetail::create([
        'educational_activity_id' => $schedule->id,
        'consistent' => 1,
        'what_learned' => 'Nothing',
        'teacher_report_detail' => 'Done',
        'attchments' => [
            ['path' => 'uploads/image1.png', 'extension' => 'png', 'type_id' => 48]
        ],
    ]);

    // 4. Create daily attendance entry ('present')
    StudentDailyAttendance::create([
        'student_id' => $student->id,
        'student_group_id' => $group->id,
        'attendance_date' => '2026-06-08',
        'status' => 'present',
    ]);

    // 5. Test Livewire component
    Livewire::test(EducationDirectorDashboard::class)
        ->set('dateFrom', '2026-06-01')
        ->set('dateTo', '2026-06-15')
        ->assertViewHas('metrics', function ($metrics) {
            return $metrics['total_executed'] === 1 &&
                   $metrics['executed_educational'] === 1 &&
                   $metrics['executed_psychological'] === 0 &&
                   $metrics['executed_values'] === 0 &&
                   $metrics['total_attendance'] === 1 &&
                   $metrics['avg_daily_attendance'] == 1.0 &&
                   $metrics['weekly_attendance_rate'] == 100.0 &&
                   $metrics['weekly_harmony_rate'] == 100.0 &&
                   $metrics['total_images_count'] === 1;
        })
        ->assertViewHas('chartData', function ($chartData) {
            return count($chartData['labels']) === 1 &&
                   $chartData['labels'][0] === '2026-06-08' &&
                   $chartData['series'][0]['name'] === 'الحضور' &&
                   $chartData['series'][0]['data'][0] === 1 &&
                   $chartData['series'][1]['name'] === 'الغياب' &&
                   $chartData['series'][1]['data'][0] === 0;
        });
});

it('filters metrics by selected student group (educational center)', function () {
    $adminUser = User::factory()->create(['id' => 1]);
    $this->actingAs($adminUser);

    $actName = EducationalActivityName::create([
        'activity_name' => 'Test Activity Name',
        'activation' => 1,
    ]);

    // Create Group A
    $groupA = StudentGroup::create([
        'name' => 'Group A',
        'batch_no' => 'B1',
        'activation' => 1,
    ]);

    // Create Group B
    $groupB = StudentGroup::create([
        'name' => 'Group B',
        'batch_no' => 'B1',
        'activation' => 1,
    ]);

    // Create Student in Group A
    $studentA = Student::create([
        'identity_number' => 111111111,
        'full_name' => 'Student A',
        'birth_date' => '2018-01-01',
        'student_groups_id' => $groupA->id,
        'activation' => 1,
        'gender' => 1,
        'status_id' => 200,
    ]);

    // Create Student in Group B
    $studentB = Student::create([
        'identity_number' => 222222222,
        'full_name' => 'Student B',
        'birth_date' => '2018-01-01',
        'student_groups_id' => $groupB->id,
        'activation' => 1,
        'gender' => 1,
        'status_id' => 200,
    ]);

    // Activity for Group A
    $scheduleA = ActivitySchedule::create([
        'group_id' => $groupA->id,
        'activity_name' => $actName->id,
        'educational_activity_domain' => 187,
        'target_category' => 'children',
        'period_start' => '2026-06-08 09:00:00',
        'period_end' => '2026-06-08 10:00:00',
        'educational_period_groups' => 200,
        'activation' => 1,
    ]);
    EducationalActivityDetail::create([
        'educational_activity_id' => $scheduleA->id,
        'consistent' => 1,
        'what_learned' => 'Nothing',
        'teacher_report_detail' => 'Done',
    ]);
    StudentDailyAttendance::create([
        'student_id' => $studentA->id,
        'student_group_id' => $groupA->id,
        'attendance_date' => '2026-06-08',
        'status' => 'present',
    ]);

    // Activity for Group B
    $scheduleB = ActivitySchedule::create([
        'group_id' => $groupB->id,
        'activity_name' => $actName->id,
        'educational_activity_domain' => 187,
        'target_category' => 'children',
        'period_start' => '2026-06-08 09:00:00',
        'period_end' => '2026-06-08 10:00:00',
        'educational_period_groups' => 200,
        'activation' => 1,
    ]);
    EducationalActivityDetail::create([
        'educational_activity_id' => $scheduleB->id,
        'consistent' => 1,
        'what_learned' => 'Nothing',
        'teacher_report_detail' => 'Done',
    ]);
    StudentDailyAttendance::create([
        'student_id' => $studentB->id,
        'student_group_id' => $groupB->id,
        'attendance_date' => '2026-06-08',
        'status' => 'present',
    ]);

    // Without group filter, metrics sum both
    Livewire::test(EducationDirectorDashboard::class)
        ->set('dateFrom', '2026-06-01')
        ->set('dateTo', '2026-06-15')
        ->assertViewHas('metrics', function ($metrics) {
            return $metrics['total_executed'] === 2 &&
                   $metrics['total_attendance'] === 2;
        });

    // Filtering by Group A
    Livewire::test(EducationDirectorDashboard::class)
        ->set('dateFrom', '2026-06-01')
        ->set('dateTo', '2026-06-15')
        ->set('selectedGroupId', $groupA->id)
        ->assertViewHas('metrics', function ($metrics) {
            return $metrics['total_executed'] === 1 &&
                   $metrics['total_attendance'] === 1;
        });
});

it('calculates Section 4 survey 120 metrics correctly and filters by group but ignores dates', function () {
    $adminUser = User::factory()->create(['id' => 1]);
    $this->actingAs($adminUser);

    // Seed prerequisite status and survey_table records for foreign keys
    \App\Models\Status::forceCreate([
        'id' => 120,
        'status_name' => 'Survey Section 120',
        'p_id_sub' => 185, // valid parent ID seeded in beforeEach
    ]);

    \DB::table('survey_table')->insert([
        'id' => 3,
        'survey_for_section' => 120,
    ]);

    // Seed Survey Questions first to satisfy foreign key constraints on survey_answers
    $qids = [6, 7, 9, 10, 12, 13, 101, 105];
    foreach ($qids as $qid) {
        \App\Models\SurveyQuestion::forceCreate([
            'id' => $qid,
            'question_ar_text' => 'Question ' . $qid,
            'survey_table_id' => 3,
            'survey_for_section' => 120,
            'answer_input_type' => 1,
            'batch_no' => 1,
        ]);
    }

    // Create student group A
    $groupA = StudentGroup::create([
        'name' => 'Group A',
        'batch_no' => 'B1',
        'activation' => 1,
    ]);

    // Create student group B
    $groupB = StudentGroup::create([
        'name' => 'Group B',
        'batch_no' => 'B2',
        'activation' => 1,
    ]);

    // Create student A in group A
    $studentA = Student::create([
        'identity_number' => 111111111,
        'full_name' => 'Student A',
        'birth_date' => '2018-01-01',
        'student_groups_id' => $groupA->id,
        'activation' => 1,
        'gender' => 1,
        'status_id' => 200,
    ]);

    // Create student B in group B
    $studentB = Student::create([
        'identity_number' => 222222222,
        'full_name' => 'Student B',
        'birth_date' => '2018-01-01',
        'student_groups_id' => $groupB->id,
        'activation' => 1,
        'gender' => 1,
        'status_id' => 200,
    ]);

    // Seed Survey Answers for Student A (Survey 120)
    // ID 6: "هل الأسرة نازحة /مقيمة" -> "نازحة"
    // ID 7: "هل الطفل يتيم" -> "نعم"
    // ID 9: "هل تعرض الطفل لأي اصابات..." -> "نعم"
    // ID 10: "هل يعاني الطفل من مشاكل صحية" -> "نعم"
    // ID 12: "هل يتابع التعليم الالكتروني" -> "نعم"
    // ID 13: "هل يتابع التعليم الوجاهي" -> "لا"
    // ID 101: "جنس الطفل" -> "2" (Male)
    // ID 105: "عمر الطفل" -> "8"
    $answersA = [
        6 => 'نازحة',
        7 => 'نعم',
        9 => 'نعم',
        10 => 'نعم',
        12 => 'نعم',
        13 => 'لا',
        101 => '2',
        105 => '8',
    ];
    foreach ($answersA as $qid => $val) {
        \App\Models\SurveyAnswer::create([
            'survey_no' => 120,
            'account_id' => $studentA->identity_number,
            'question_id' => $qid,
            'answer_ar_text' => $val,
        ]);
    }

    // Seed Survey Answers for Student B (Survey 120)
    // ID 6: "هل الأسرة نازحة /مقيمة" -> "مقيمة"
    // ID 7: "هل الطفل يتيم" -> "لا"
    // ID 9: "هل تعرض الطفل لأي اصابات..." -> "لا"
    // ID 10: "هل يعاني الطفل من مشاكل صحية" -> "لا"
    // ID 12: "هل يتابع التعليم الالكتروني" -> "لا"
    // ID 13: "هل يتابع التعليم الوجاهي" -> "نعم"
    // ID 101: "جنس الطفل" -> "3" (Female)
    // ID 105: "عمر الطفل" -> "11"
    $answersB = [
        6 => 'مقيمة',
        7 => 'لا',
        9 => 'لا',
        10 => 'لا',
        12 => 'لا',
        13 => 'نعم',
        101 => '3',
        105 => '11',
    ];
    foreach ($answersB as $qid => $val) {
        \App\Models\SurveyAnswer::create([
            'survey_no' => 120,
            'account_id' => $studentB->identity_number,
            'question_id' => $qid,
            'answer_ar_text' => $val,
        ]);
    }

    // Test 1: Without group or batch filter, should aggregate both (2 students)
    Livewire::test(EducationDirectorDashboard::class)
        ->assertViewHas('surveyMetrics', function ($sm) {
            return $sm['total_registered']['count'] === 2 &&
                   $sm['age_6_9']['count'] === 1 && // Student A (8)
                   $sm['age_10_12']['count'] === 1 && // Student B (11)
                   $sm['male']['count'] === 1 && // Student A (2)
                   $sm['female']['count'] === 1 && // Student B (3)
                   $sm['elearning']['count'] === 1 && // Student A
                   $sm['face_to_face']['count'] === 1 && // Student B
                   $sm['war_injured']['count'] === 1 && // Student A
                   $sm['displaced']['count'] === 1 && // Student A
                   $sm['orphan']['count'] === 1 && // Student A
                   $sm['health_issues']['count'] === 1; // Student A
        });

    // Test 2: With group filter (Group A only), should only show Student A metrics
    Livewire::test(EducationDirectorDashboard::class)
        ->set('selectedGroupId', $groupA->id)
        ->assertViewHas('surveyMetrics', function ($sm) {
            return $sm['total_registered']['count'] === 1 &&
                   $sm['total_registered']['pct'] == 100.0 &&
                   $sm['age_6_9']['count'] === 1 &&
                   $sm['age_6_9']['pct'] == 100.0 &&
                   $sm['age_10_12']['count'] === 0 &&
                   $sm['male']['count'] === 1 &&
                   $sm['female']['count'] === 0 &&
                   $sm['elearning']['count'] === 1 &&
                   $sm['face_to_face']['count'] === 0 &&
                   $sm['war_injured']['count'] === 1 &&
                   $sm['displaced']['count'] === 1 &&
                   $sm['orphan']['count'] === 1 &&
                   $sm['health_issues']['count'] === 1;
        });

    // Test 3: With batch filter 'B1' only, should only show Student A metrics
    Livewire::test(EducationDirectorDashboard::class)
        ->set('selectedBatchNo', 'B1')
        ->assertViewHas('surveyMetrics', function ($sm) {
            return $sm['total_registered']['count'] === 1 &&
                   $sm['age_6_9']['count'] === 1 &&
                   $sm['age_10_12']['count'] === 0 &&
                   $sm['male']['count'] === 1;
        });

    // Test 4: With batch filter 'B2' only, should only show Student B metrics
    Livewire::test(EducationDirectorDashboard::class)
        ->set('selectedBatchNo', 'B2')
        ->assertViewHas('surveyMetrics', function ($sm) {
            return $sm['total_registered']['count'] === 1 &&
                   $sm['age_6_9']['count'] === 0 &&
                   $sm['age_10_12']['count'] === 1 &&
                   $sm['female']['count'] === 1;
        });

    // Test 5: Date filters shouldn't affect the survey metrics
    Livewire::test(EducationDirectorDashboard::class)
        ->set('dateFrom', '2030-01-01')
        ->set('dateTo', '2030-12-31')
        ->assertViewHas('surveyMetrics', function ($sm) {
            return $sm['total_registered']['count'] === 2; // still 2
        });
});

