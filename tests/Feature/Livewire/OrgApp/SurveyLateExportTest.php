<?php

use App\Models\User;
use App\Exports\SurveyLate;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->user = User::factory()->create(['id' => 1]);
    $this->actingAs($this->user);
});

it('correctly retrieves status_group_name in SurveyLate export collection', function () {
    // Mock DB::select to avoid MySQL-specific syntax issues on SQLite in testing
    DB::shouldReceive('select')
        ->once()
        ->with(Mockery::on(function ($query) {
            // Normalize spaces to make checking robust
            $normalizedQuery = preg_replace('/\s+/', ' ', $query);
            return str_contains($normalizedQuery, 's_stat.status_name as status_group_name') 
                && str_contains($normalizedQuery, 'LEFT JOIN statuses s_stat ON s.status_id = s_stat.id');
        }), Mockery::any())
        ->andReturn([
            (object) [
                'id' => 1,
                'identity_number' => '888777666',
                'full_name' => 'Late Student Name',
                'group_name' => 'Late Export Group',
                'batch_no' => 5,
                'status_group_name' => 'Active Academic Student',
                'survey_name' => 'Late Survey Section',
                'activation' => 1,
            ]
        ]);

    $export = new SurveyLate(801, 10, 5);

    // Retrieve the collection
    $collection = $export->collection();

    // Assert that the student record is retrieved and includes status_group_name
    expect($collection)->not->toBeEmpty();
    expect($collection->count())->toBe(1);

    $row = $collection->first();
    expect($row->identity_number)->toBe('888777666');
    expect($row->full_name)->toBe('Late Student Name');
    expect($row->group_name)->toBe('Late Export Group');
    expect($row->status_group_name)->toBe('Active Academic Student');
    expect($row->survey_name)->toBe('Late Survey Section');

    // Test the mapped output format
    $mappedRow = $export->map($row);
    expect($mappedRow[0])->toBe(1); // sequence
    expect($mappedRow[1])->toBe('888777666'); // identity number
    expect($mappedRow[2])->toBe('Late Student Name'); // full name
    expect($mappedRow[3])->toBe(5); // batch_no
    expect($mappedRow[4])->toBe('Late Export Group'); // group_name
    expect($mappedRow[5])->toBe('Active Academic Student'); // status_group_name
    expect($mappedRow[6])->toBe('Late Survey Section'); // survey_name
    expect($mappedRow[7])->toBe('Active'); // status (activation)
});
