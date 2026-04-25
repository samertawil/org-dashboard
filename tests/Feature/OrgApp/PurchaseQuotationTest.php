<?php

use App\Livewire\OrgApp\Financial\QuotationComparison;
use App\Models\PartnerInstitution;
use App\Models\PurchaseQuotationPrice;
use App\Models\PurchaseQuotationResponse;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseRequisitionItem;
use App\Models\Status;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    // Setup statuses
    Status::unguard();
    $this->pendingStatus = Status::firstOrCreate(['id' => 1], ['status_name' => 'Pending']);
    $this->awardedPrStatus = Status::firstOrCreate(['id' => 109], ['status_name' => 'Awarded PR']);
    $this->awardedQuoteStatus = Status::firstOrCreate(['id' => 172], ['status_name' => 'Winner']);
    $this->currencyStatus = Status::firstOrCreate(['id' => 3], ['status_name' => 'USD']);
    Status::reguard();

    // Setup vendors
    $this->vendor1 = PartnerInstitution::create(['name' => 'Vendor A', 'type' => 'Supplier']);
    $this->vendor2 = PartnerInstitution::create(['name' => 'Vendor B', 'type' => 'Supplier']);

    // Setup Purchase Requisition
    $this->pr = PurchaseRequisition::create([
        'request_number' => 'PR-TEST-001',
        'request_date' => now()->toDateString(),
        'suggested_vendor_ids' => [$this->vendor1->id, $this->vendor2->id],
        'status_id' => $this->pendingStatus->id,
        'created_by' => $this->user->id,
    ]);

    // Setup PR Items
    $this->item1 = PurchaseRequisitionItem::create([
        'purchase_requisition_id' => $this->pr->id,
        'line_number' => 1,
        'item_name' => 'Laptop',
        'quantity' => 2,
        'unit_price' => 0, // Will be updated
    ]);

    $this->item2 = PurchaseRequisitionItem::create([
        'purchase_requisition_id' => $this->pr->id,
        'line_number' => 2,
        'item_name' => 'Mouse',
        'quantity' => 5,
        'unit_price' => 0, // Will be updated
    ]);

    // Setup Quotation 1 (from Vendor 1)
    $this->quotation1 = PurchaseQuotationResponse::create([
        'purchase_requisition_id' => $this->pr->id,
        'vendor_id' => $this->vendor1->id,
        'total_amount' => 2100, // 2*1000 + 5*20
        'status_id' => null,
    ]);

    PurchaseQuotationPrice::create([
        'quotation_response_id' => $this->quotation1->id,
        'purchase_requisition_item_id' => $this->item1->id,
        'offered_price' => 1000,
    ]);

    PurchaseQuotationPrice::create([
        'quotation_response_id' => $this->quotation1->id,
        'purchase_requisition_item_id' => $this->item2->id,
        'offered_price' => 20,
    ]);

    // Setup Quotation 2 (from Vendor 2)
    $this->quotation2 = PurchaseQuotationResponse::create([
        'purchase_requisition_id' => $this->pr->id,
        'vendor_id' => $this->vendor2->id,
        'total_amount' => 1900, // 2*900 + 5*20
        'status_id' => null,
    ]);

    PurchaseQuotationPrice::create([
        'quotation_response_id' => $this->quotation2->id,
        'purchase_requisition_item_id' => $this->item1->id,
        'offered_price' => 900,
    ]);

    PurchaseQuotationPrice::create([
        'quotation_response_id' => $this->quotation2->id,
        'purchase_requisition_item_id' => $this->item2->id,
        'offered_price' => 20,
    ]);
});

it('renders the quotation comparison component correctly', function () {
    Livewire::test(QuotationComparison::class, ['id' => $this->pr->id])
        ->assertStatus(200)
        ->assertSee('Vendor A')
        ->assertSee('Vendor B');
});

it('awards a quotation and updates the purchase requisition and items correctly', function () {
    Livewire::test(QuotationComparison::class, ['id' => $this->pr->id])
        ->call('acceptQuotation', $this->quotation2->id)
        ->assertDispatched('notify');

    // 1. Check Quotation Statuses
    $this->assertDatabaseHas('purchase_quotation_responses', [
        'id' => $this->quotation2->id,
        'status_id' => 176, // Winner
    ]);

    $this->assertDatabaseHas('purchase_quotation_responses', [
        'id' => $this->quotation1->id,
        'status_id' => null, // Not awarded
    ]);

    // 2. Check Purchase Requisition Status and Total
    $this->assertDatabaseHas('purchase_requisitions', [
        'id' => $this->pr->id,
        'status_id' => 109, // Awarded PR
        'estimated_total_nis' => 1900, // Matched quotation 2 total
        'order_count' => 0,
    ]);

    // 3. Check Purchase Requisition Items unit prices updated to winner prices
    $this->assertDatabaseHas('purchase_requisition_items', [
        'id' => $this->item1->id,
        'unit_price' => 900,
    ]);

    $this->assertDatabaseHas('purchase_requisition_items', [
        'id' => $this->item2->id,
        'unit_price' => 20,
    ]);
});
