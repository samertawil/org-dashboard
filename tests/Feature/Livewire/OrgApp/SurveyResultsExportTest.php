<?php

use App\Models\User;
use App\Exports\SurveyResultsExport;

beforeEach(function () {
    $this->user = User::factory()->create(['id' => 1]);
    $this->actingAs($this->user);
});

it('correctly constructs query with status_group_name in SurveyResultsExport', function () {
    $export = new SurveyResultsExport(119, 10, 5);
    $query = $export->query();
    $sql = $query->toSql();

    // Check that the status_group_name column is selected
    expect($sql)->toContain('status_group_name');

    // Check that s_stat join is present in the SQL query
    expect($sql)->toContain('s_stat');
    expect($sql)->toContain('status_id');

    // Verify headings
    $headings = $export->headings();
    expect($headings)->toContain('status_group_name');
    expect(array_search('status_group_name', $headings))->toBe(3);

    // Verify map method structure
    $mockRow = (object) [
        'account_id' => '888777666',
        'full_name' => 'John Doe',
        'status_group_name' => 'Active Academic Student',
        'education_point_name' => 'Point A',
        'teacher_name' => 'Teacher Name',
        'survey_no' => 119,
        'درجات_البعد_العاطفي_الانفعالي' => 10,
        'تقييم_البعد_العاطفي_الانفعالي' => 'Good',
        'درجات_البعد_النفسي_والعقلي' => 12,
        'تقييم_البعد_النفسي_والعقلي' => 'Very Good',
        'درجات_البعد_الجسدي_والاجتماعي' => 8,
        'تقييم_البعد_الجسدي_والاجتماعي' => 'Medium',
        'درجات_اللغة_العربية' => 15,
        'تقييم_اللغة_العربية' => 'Excellent',
        'درجات_اللغة_الانجليزية' => 14,
        'تقييم_اللغة_الانجليزية' => 'Excellent',
        'درجات_مادة_الحساب' => 11,
        'تقييم_مادة_الحساب' => 'Very Good',
        'المجموع_الكلي' => 70,
        'التقييم_الكلي' => 'Very Good',
        'اسم_الاستبيان' => 'Student Assessment'
    ];

    // Seed a student with this identity number to satisfy dynamic batch_no check in map()
    $group = \App\Models\StudentGroup::create([
        'name' => 'Test Group',
        'batch_no' => 5,
    ]);

    \App\Models\Student::create([
        'identity_number' => '888777666',
        'full_name' => 'John Doe',
        'birth_date' => '2015-01-01',
        'gender' => 1,
        'enrollment_type' => 'full_week',
        'student_groups_id' => $group->id,
    ]);

    $mapped = $export->map($mockRow);
    expect($mapped[0])->toBe('888777666'); // account_id
    expect($mapped[1])->toBe('John Doe'); // full_name
    expect($mapped[2])->toBe(5); // batch_no (queried dynamically)
    expect($mapped[3])->toBe('Active Academic Student'); // status_group_name
    expect($mapped[4])->toBe('Point A'); // education_point_name
});
