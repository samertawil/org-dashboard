<?php

use App\Models\User;
use App\Models\Student;
use App\Models\StudentGroup;
use App\Models\Region;
use App\Enums\GlobalSystemConstant;
use Livewire\Livewire;
use App\Livewire\OrgApp\Reports\EducationalProgress;
use Illuminate\Support\Facades\Gate;

beforeEach(function () {
    // Define the reports gate to allow the test user
    Gate::define('reports.educational.progress', function () {
        return true;
    });

    // Create super admin user with ID 1 to pass gate checks
    $this->user = User::factory()->create(['id' => 1]);
    $this->actingAs($this->user);
});

it('renders educational progress report and lists batches', function () {
    // Create student groups with different batches
    StudentGroup::create([
        'name' => 'Group Batch A',
        'batch_no' => 10,
        'activation' => 1,
    ]);

    StudentGroup::create([
        'name' => 'Group Batch B',
        'batch_no' => 20,
        'activation' => 1,
    ]);

    Livewire::test(EducationalProgress::class)
        ->assertStatus(200)
        ->assertViewHas('batches', function ($batches) {
            return $batches->contains(10) && $batches->contains(20);
        });
});

it('filters students and groups by selected batch', function () {
    // Create student groups with different batches
    $groupA = StudentGroup::create([
        'name' => 'Group A',
        'batch_no' => 10,
        'activation' => 1,
        'max_students' => 10,
    ]);

    $groupB = StudentGroup::create([
        'name' => 'Group B',
        'batch_no' => 20,
        'activation' => 1,
        'max_students' => 15,
    ]);

    // Create students in those groups
    Student::create([
        'identity_number' => 100000001,
        'full_name' => 'Student in Batch 10',
        'birth_date' => '2015-05-05',
        'student_groups_id' => $groupA->id,
        'activation' => 1,
        'gender' => GlobalSystemConstant::MALE->value,
    ]);

    Student::create([
        'identity_number' => 200000002,
        'full_name' => 'Student in Batch 20',
        'birth_date' => '2015-05-05',
        'student_groups_id' => $groupB->id,
        'activation' => 1,
        'gender' => GlobalSystemConstant::FEMALE->value,
    ]);

    // Test component filtering by selectedBatch = 10
    $component = Livewire::test(EducationalProgress::class)
        ->set('selectedBatch', 10);

    // KPI verification: Total students should be 1 (only the one in Batch 10)
    $component->assertViewHas('kpis', function ($kpis) {
        return $kpis['totalStudents'] === 1 && $kpis['totalGroups'] === 1;
    });

    // Check that gender distribution correctly lists Male for Batch 10
    $genderData = $component->get('genderChartData');
    expect($genderData['labels'])->toContain(__('Male'));
    expect($genderData['labels'])->not->toContain(__('Female'));
    expect($genderData['series'])->toBe([1]);

    // Test component filtering by selectedBatch = 20
    $component2 = Livewire::test(EducationalProgress::class)
        ->set('selectedBatch', 20);

    // KPI verification: Total students should be 1 (only the one in Batch 20)
    $component2->assertViewHas('kpis', function ($kpis) {
        return $kpis['totalStudents'] === 1 && $kpis['totalGroups'] === 1;
    });

    // Check that gender distribution correctly lists Female for Batch 20
    $genderData2 = $component2->get('genderChartData');
    expect($genderData2['labels'])->toContain(__('Female'));
    expect($genderData2['labels'])->not->toContain(__('Male'));
    expect($genderData2['series'])->toBe([1]);
});

it('sources regions from student_groups and filters students by group region', function () {
    // Create regions
    $regionA = Region::create(['region_name' => 'Region Active']);
    $regionB = Region::create(['region_name' => 'Region Inactive']);

    // Create a group in region A (active group)
    $group = StudentGroup::create([
        'name' => 'Group Active Region',
        'region_id' => $regionA->id,
        'activation' => 1,
        'batch_no' => 1,
    ]);

    // Student in active region group
    Student::create([
        'identity_number' => 300000003,
        'full_name' => 'Student in Active Region Group',
        'birth_date' => '2015-05-05',
        'student_groups_id' => $group->id,
        'activation' => 1,
        'gender' => GlobalSystemConstant::MALE->value,
    ]);

    // Assert that the component's rendered regions only lists Region A (since Region B has no active group)
    $component = Livewire::test(EducationalProgress::class);
    $component->assertViewHas('regions', function ($regions) use ($regionA, $regionB) {
        $ids = $regions->pluck('id')->toArray();
        return in_array($regionA->id, $ids) && !in_array($regionB->id, $ids);
    });

    // Test that filtering by Region A includes our student
    $component->set('selectedRegion', $regionA->id);
    $component->assertViewHas('kpis', function ($kpis) {
        return $kpis['totalStudents'] === 1;
    });

    // Test that filtering by a non-existent region ID returns 0 students
    $component->set('selectedRegion', 9999);
    $component->assertViewHas('kpis', function ($kpis) {
        return $kpis['totalStudents'] === 0;
    });
});

it('calculates student age when joining and splits into 6-9 and 10-12 age groups', function () {
    $group = StudentGroup::create([
        'name' => 'Group Age Test',
        'start_date' => '2026-01-01',
        'end_date' => '2026-12-31',
        'activation' => 1,
        'batch_no' => 1,
    ]);

    // Student A: born 2020-01-01 -> join 2026-01-01 -> 6 years old (should fall into 6-9)
    Student::create([
        'identity_number' => 400000004,
        'full_name' => 'Student 6yo at join',
        'birth_date' => '2020-01-01',
        'student_groups_id' => $group->id,
        'activation' => 1,
        'gender' => GlobalSystemConstant::MALE->value,
    ]);

    // Student B: born 2015-01-01 -> join 2026-01-01 -> 11 years old (should fall into 10-12)
    Student::create([
        'identity_number' => 500000005,
        'full_name' => 'Student 11yo at join',
        'birth_date' => '2015-01-01',
        'student_groups_id' => $group->id,
        'activation' => 1,
        'gender' => GlobalSystemConstant::FEMALE->value,
    ]);

    $component = Livewire::test(EducationalProgress::class);
    $ageData = $component->get('ageChartData');

    // Expected labels order: ['< 6', '6-9', '10-12', '13-18', '18+', 'Unknown']
    expect($ageData['labels'])->toBe(['6-9', '10-12']);
    expect($ageData['series'])->toBe([1, 1]);
});


