<?php

use App\Livewire\OrgApp\Student\Create;
use App\Livewire\OrgApp\Student\Edit;
use App\Livewire\OrgApp\Student\Index;
use App\Models\Status;
use App\Models\Student;
use App\Models\User;
use Livewire\Livewire;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Imports\StudentsImport;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);

    // Seed necessary statuses
    $this->status = Status::create(['status_name' => 'Active', 'p_id' => 1]);
    $this->livingParentStatus = Status::create(['status_name' => 'Father', 'p_id' => 2]);
});

it('renders the create student page', function () {
    Livewire::test(Create::class)
        ->assertStatus(200);
});

it('validates required fields', function () {
    Livewire::test(Create::class)
        ->set('enrollment_type', '') // Validation requires enrollment_type to be empty to fail
        ->call('save')
        ->assertHasErrors([
            'identity_number' => 'required',
            'full_name' => 'required',
            'birth_date' => 'required',
            'gender' => 'required',
            'enrollment_type' => 'required',
        ]);
});

it('validates identity number format', function () {
    Livewire::test(Create::class)
        ->set('identity_number', '123') // Too short
        ->call('save')
        ->assertHasErrors(['identity_number' => 'min_digits']);
        
    Livewire::test(Create::class)
        ->set('identity_number', 'not-a-number') // Not integer
        ->call('save')
        ->assertHasErrors(['identity_number' => 'integer']);
});

it('validates birth date age limits', function () {
    // Mocking config for age limits as used in Student model
    // Assuming config is loaded, but we can verify logic matches:
    // older than 6 years, younger than 11 years (approx)
    
    $tooYoung = now()->subYears(4)->format('Y-m-d');
    $tooOld = now()->subYears(15)->format('Y-m-d');
    
    Livewire::test(Create::class)
        ->set('birth_date', $tooYoung)
        ->call('save')
        ->assertHasErrors(['birth_date' => 'before_or_equal']);

    Livewire::test(Create::class)
        ->set('birth_date', $tooOld)
        ->call('save')
        ->assertHasErrors(['birth_date' => 'after_or_equal']);
});

it('creates a student', function () {
    $validBirthDate = now()->subYears(8)->format('Y-m-d');
    
    Livewire::test(Create::class)
        ->set('identity_number', 123456789)
        ->set('full_name', 'John Doe')
        ->set('birth_date', $validBirthDate)
        ->set('gender', 'Male')
        ->set('enrollment_type', 'full_week')
        ->set('parent_phone', '0599123456')
        ->set('status_id', $this->status->id)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('students', [
        'identity_number' => 123456789,
        'full_name' => 'John Doe',
        'enrollment_type' => 'full_week',
        'added_type' => 1, // Manual
    ]);
});

it('updates a student', function () {
    $student = Student::create([
        'identity_number' => 987654321,
        'full_name' => 'Jane Doe',
        'birth_date' => now()->subYears(9)->format('Y-m-d'),
        'gender' => 'Female',
        'enrollment_type' => 'sat_mon_wed',
        'activation' => 1,
        'created_by' => $this->user->id,
    ]);

    Livewire::test(Edit::class, ['student' => $student])
        ->set('full_name', 'Jane Updated')
        ->set('enrollment_type', 'sun_tue_thu')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('student.index'));

    $this->assertDatabaseHas('students', [
        'id' => $student->id,
        'full_name' => 'Jane Updated',
        'enrollment_type' => 'sun_tue_thu',
    ]);
});

it('renders index page and searches students', function () {
    Student::create([
        'identity_number' => 111111111,
        'full_name' => 'Alpha Student',
        'birth_date' => now()->subYears(10)->format('Y-m-d'),
        'gender' => 'Male',
        'created_by' => $this->user->id,
    ]);
    
    Student::create([
        'identity_number' => 222222222,
        'full_name' => 'Beta Student',
        'birth_date' => now()->subYears(10)->format('Y-m-d'),
        'gender' => 'Female',
        'created_by' => $this->user->id,
    ]);

    Livewire::test(Index::class)
        ->assertStatus(200)
        ->set('search', 'Alpha')
        ->assertSee('Alpha Student')
        ->assertDontSee('Beta Student');
});

it('imports students from excel', function () {
    Excel::fake();
    
    $file = UploadedFile::fake()->create('students.xlsx');
    
    Livewire::test(Index::class)
        ->set('excelFile', $file)
        ->call('import')
        ->assertHasNoErrors();
        
    Excel::assertImported('students.xlsx', function(StudentsImport $import) {
        return true;
    });
});

