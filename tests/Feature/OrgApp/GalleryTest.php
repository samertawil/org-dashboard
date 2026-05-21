<?php

use App\Livewire\OrgApp\Gallery\Index;
use App\Models\Activity;
use App\Models\ActivityAttchment;
use App\Models\ActivitySchedule;
use App\Models\EducationalActivityDetail;
use App\Models\PurchaseRequisition;
use App\Models\StudentSubjectForLearn;
use App\Models\Status;
use App\Models\User;
use Livewire\Livewire;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    $this->user = User::factory()->create([
        'id' => 999,
        'activation' => 1,
    ]);
    $this->actingAs($this->user);

    // Clear status cache and seed attachment types status
    Cache::forget('statuses-all');
    $this->imageStatus = Status::create([
        'status_name' => 'Image',
        'p_id' => 1,
        'p_id_sub' => 47,
    ]);
});

it('renders the gallery component', function () {
    // Define all permissions to allow rendering and loading
    Gate::define('activity.index', fn() => true);
    Gate::define('purchase_request.index', fn() => true);
    Gate::define('educational-activity-detail.index', fn() => true);

    Livewire::test(Index::class)
        ->assertStatus(200);
});

it('loads and merges attachments from various sources when user has all permissions', function () {
    // Define all permissions
    Gate::define('activity.index', fn() => true);
    Gate::define('purchase_request.index', fn() => true);
    Gate::define('educational-activity-detail.index', fn() => true);

    // Create Activity and ActivityAttachment
    $activity = Activity::create([
        'name' => 'Test Activity',
        'start_date' => now()->toDateString(),
        'activation' => 1,
        'created_by' => $this->user->id,
        'status' => 1,
    ]);

    $activityAttachment = ActivityAttchment::create([
        'activity_id' => $activity->id,
        'attchment_path' => 'public/activity_image.jpg',
        'notes' => 'Activity Attachment Note',
        'attchment_type' => $this->imageStatus->id,
    ]);

    // Create Subject Learning and ActivityAttachment
    $subject = StudentSubjectForLearn::create([
        'name' => 'Maths',
        'activation' => 1,
    ]);

    $subjectAttachment = ActivityAttchment::create([
        'subject_learning_id' => $subject->id,
        'attchment_path' => 'public/subject_document.pdf',
        'notes' => 'Subject Document Note',
        'attchment_type' => $this->imageStatus->id,
    ]);

    // Create Purchase Request
    $pr = PurchaseRequisition::create([
        'request_number' => 1001,
        'request_date' => now()->toDateString(),
        'estimated_total_dollar' => 100,
        'estimated_total_nis' => 350,
        'status_id' => 1,
        'created_by' => $this->user->id,
        'attachments' => [
            [
                'path' => 'public/pr_invoice.png',
                'name' => 'Invoice File',
                'extension' => 'png',
            ]
        ]
    ]);

    // Create Educational Activity Schedule and Detail
    $schedule = ActivitySchedule::create([
        'activity_name' => 'Test Edu Activity',
        'period_start'  => now(),
        'period_end'    => now()->addHour(),
        'activation'    => 1,
        'created_by'    => $this->user->id,
    ]);

    $eduDetail = EducationalActivityDetail::create([
        'educational_activity_id' => $schedule->id,
        'attchments' => [
            [
                'path' => 'public/edu_report.docx',
                'name' => 'Edu Report File',
                'extension' => 'docx',
            ]
        ]
    ]);

    // Test Livewire component retrieves all attachments
    Livewire::test(Index::class)
        ->assertSet('genericAttachments', function ($attachments) {
            $names = collect($attachments)->pluck('name')->toArray();
            return in_array('Activity Attachment Note', $names)
                && in_array('Subject Document Note', $names)
                && in_array('Invoice File', $names)
                && in_array('Edu Report File', $names);
        });
});

it('filters attachments by source correctly', function () {
    Gate::define('activity.index', fn() => true);
    Gate::define('purchase_request.index', fn() => true);
    Gate::define('educational-activity-detail.index', fn() => true);

    // Seed data
    $activity = Activity::create([
        'name' => 'Activity A',
        'start_date' => now()->toDateString(),
        'activation' => 1,
        'created_by' => $this->user->id,
        'status' => 1,
    ]);
    ActivityAttchment::create([
        'activity_id' => $activity->id,
        'attchment_path' => 'public/activity_image.jpg',
        'notes' => 'Activity Item',
    ]);

    $pr = PurchaseRequisition::create([
        'request_number' => 1002,
        'request_date' => now()->toDateString(),
        'estimated_total_dollar' => 100,
        'estimated_total_nis' => 350,
        'status_id' => 1,
        'created_by' => $this->user->id,
        'attachments' => [
            [
                'path' => 'public/pr_invoice.png',
                'name' => 'PR Item',
            ]
        ]
    ]);

    // Test Activity filter source
    Livewire::test(Index::class)
        ->set('filterSource', 'activity')
        ->assertSet('genericAttachments', function ($attachments) {
            $names = collect($attachments)->pluck('name')->toArray();
            return in_array('Activity Item', $names) && !in_array('PR Item', $names);
        });

    // Test Purchase Request filter source
    Livewire::test(Index::class)
        ->set('filterSource', 'purchase_request')
        ->assertSet('genericAttachments', function ($attachments) {
            $names = collect($attachments)->pluck('name')->toArray();
            return in_array('PR Item', $names) && !in_array('Activity Item', $names);
        });
});

it('hides unauthorized attachments and respects permission gates', function () {
    // Only define activity permissions; deny PR and Educational Activity/Subject Learning permissions
    Gate::define('activity.index', fn() => true);
    Gate::define('purchase_request.index', fn() => false);
    Gate::define('educational-activity-detail.index', fn() => false);

    // Create Activity attachment
    $activity = Activity::create([
        'name' => 'Activity Test',
        'start_date' => now()->toDateString(),
        'activation' => 1,
        'created_by' => $this->user->id,
        'status' => 1,
    ]);
    ActivityAttchment::create([
        'activity_id' => $activity->id,
        'attchment_path' => 'public/activity_image.jpg',
        'notes' => 'Allowed Activity Item',
    ]);

    // Create PR attachment
    $pr = PurchaseRequisition::create([
        'request_number' => 1003,
        'request_date' => now()->toDateString(),
        'estimated_total_dollar' => 100,
        'estimated_total_nis' => 350,
        'status_id' => 1,
        'created_by' => $this->user->id,
        'attachments' => [
            [
                'path' => 'public/pr_invoice.png',
                'name' => 'Denied PR Item',
            ]
        ]
    ]);

    // Create Educational Activity Schedule and Detail
    $schedule = ActivitySchedule::create([
        'activity_name' => 'Denied Test Edu Activity',
        'period_start'  => now(),
        'period_end'    => now()->addHour(),
        'activation'    => 1,
        'created_by'    => $this->user->id,
    ]);

    EducationalActivityDetail::create([
        'educational_activity_id' => $schedule->id,
        'attchments' => [
            [
                'path' => 'public/edu_report.docx',
                'name' => 'Denied Edu Item',
            ]
        ]
    ]);

    // Test component only returns Allowed Activity Item
    Livewire::test(Index::class)
        ->assertSet('genericAttachments', function ($attachments) {
            $names = collect($attachments)->pluck('name')->toArray();
            return in_array('Allowed Activity Item', $names)
                && !in_array('Denied PR Item', $names)
                && !in_array('Denied Edu Item', $names);
        });
});
