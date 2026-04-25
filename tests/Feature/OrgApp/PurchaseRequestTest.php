<?php

use App\Livewire\OrgApp\PurchaseRequest\Create;
use App\Livewire\OrgApp\PurchaseRequest\Edit;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseRequisitionItem;
use App\Models\Status;
use App\Models\User;
use Livewire\Livewire;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    // Seed necessary statuses for PR status
    $this->pendingStatus = Status::create(['status_name' => 'Pending', 'p_id_sub' => 1]);
    $this->approvedStatus = Status::create(['status_name' => 'Approved', 'p_id_sub' => 2]);

    // Seed Currency for PR items and total estimates calculation
    \App\Models\CurrancyValue::create(['exchange_date' => now(), 'currency_value' => 3.5]);
});

it('renders the create purchase request page', function () {
    Livewire::test(Create::class)
        ->assertStatus(200);
});

it('creates a purchase requisition with items', function () {
    $itemData = [
        [
            'item_name' => 'Laptop',
            'item_description' => 'Dell XPS',
            'quantity' => 2,
            'unit_id' => 1,
            'unit_price' => 1500,
            'currency' => 'USD',
        ]
    ];

    Livewire::test(Create::class)
        ->set('request_date', now()->toDateString())
        ->set('request_number', 1001)
        ->set('suggested_vendor_ids', [1])
        ->set('estimated_total_dollar', 1500)
        ->set('estimated_total_nis', 5250)
        ->set('status_id', 1)
        ->set('items', $itemData)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('purchase_request.index'));

    $this->assertDatabaseHas('purchase_requisitions', ['request_number' => 1001]);
    $this->assertDatabaseHas('purchase_requisition_items', ['item_name' => 'Laptop']);
});

it('updates purchase requisition and syncs items smartly', function () {
    // 1. Create original PR
    $pr = PurchaseRequisition::create([
        'request_number' => 2002,
        'request_date' => now()->toDateString(),
        'suggested_vendor_ids' => [1],
        'estimated_total_dollar' => 100,
        'estimated_total_nis' => 350,
        'attachments' => [],
        'status_id' => $this->pendingStatus->id,
        'created_by' => $this->user->id
    ]);

    // Add 2 items
    $item1 = $pr->items()->create([
        'line_number' => 1,
        'item_name' => 'Original Item 1',
        'quantity' => 1,
        'unit_id' => 1,
        'unit_price' => 10,
        'currency' => 'USD',
        'created_by' => $this->user->id
    ]);

    $item2 = $pr->items()->create([
        'line_number' => 2,
        'item_name' => 'Original Item 2',
        'quantity' => 1,
        'unit_id' => 1,
        'unit_price' => 20,
        'currency' => 'USD',
        'created_by' => $this->user->id
    ]);

    // 2. Test Edit Smart Sync
    $updatedItems = [
        [
            'id' => $item1->id,
            'item_name' => 'Updated Item 1',
            'quantity' => 5,
            'unit_id' => 1,
            'unit_price' => 15,
            'currency' => 'USD',
        ],
        [
            'item_name' => 'New Item 3',
            'quantity' => 10,
            'unit_id' => 1,
            'unit_price' => 100,
            'currency' => 'USD',
        ]
    ];

    Livewire::test(Edit::class, ['purchaseRequisition' => $pr])
        ->set('items', $updatedItems)
        ->call('update')
        ->assertHasNoErrors()
        ->assertRedirect(route('purchase_request.index'));

    // Verify DB state
    $this->assertDatabaseHas('purchase_requisition_items', [
        'id' => $item1->id,
        'item_name' => 'Updated Item 1',
        'quantity' => 5
    ]);

    $this->assertDatabaseMissing('purchase_requisition_items', [
        'id' => $item2->id
    ]);

    $this->assertDatabaseHas('purchase_requisition_items', [
        'item_name' => 'New Item 3',
        'quantity' => 10
    ]);
});

it('shows warning when no changes were made to PR', function () {
    $pr = PurchaseRequisition::create([
        'request_number' => 3003,
        'request_date' => now()->toDateString(),
        'suggested_vendor_ids' => [1],
        'estimated_total_dollar' => 100,
        'estimated_total_nis' => 350,
        'attachments' => [],
        'status_id' => $this->pendingStatus->id,
        'created_by' => $this->user->id
    ]);

    $component = Livewire::test(Edit::class, ['purchaseRequisition' => $pr]);
    
    // Ensure items match exactly to avoid index mismatch
    $component->set('items', $pr->items->toArray());

    $component->call('update')
        ->assertHasNoErrors()
        ->assertSessionHas('type', 'warning')
        ->assertSessionHas('message', __('No changes were made!'));
});
