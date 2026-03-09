<?php

namespace App\Livewire\OrgApp\PurchaseRequest;

use App\Concerns\PurchaseRequest\PurchaseTrait;
use App\Models\PurchaseRequisition;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Edit extends Component
{
    use PurchaseTrait;

    public PurchaseRequisition $purchaseRequisition;

    public function mount(PurchaseRequisition $purchaseRequisition)
    {
        $this->bootPurchaseTrait();
        $this->purchaseRequisition = $purchaseRequisition;
        
        $this->request_number = $purchaseRequisition->request_number;
        $this->request_date = $purchaseRequisition->request_date ? $purchaseRequisition->request_date->format('Y-m-d') : null;
        $this->description = $purchaseRequisition->description;
        $this->justification = $purchaseRequisition->justification;
        $this->suggested_vendor_ids = $purchaseRequisition->suggested_vendor_ids ?? [];
        $this->need_by_date = $purchaseRequisition->need_by_date ? $purchaseRequisition->need_by_date->format('Y-m-d') : null;
        $this->budget_details = $purchaseRequisition->budget_details;
        $this->estimated_total = $purchaseRequisition->estimated_total;
        $this->estimated_total_currency = $purchaseRequisition->estimated_total_currency;
        $this->status_id = $purchaseRequisition->status_id;
        
        $this->items = $purchaseRequisition->items->toArray();
        if(empty($this->items)) {
            $this->addPurchaseRequisitionItem();
        }
    }

    public function update()
    {
        $this->validate();

        DB::transaction(function () {
            $this->purchaseRequisition->fill([
                'request_number' => $this->request_number,
               
                'request_date' => $this->request_date,
                'description' => $this->description,
                'justification' => $this->justification,
                'suggested_vendor_ids' => $this->suggested_vendor_ids,
                'need_by_date' => $this->need_by_date,
                'budget_details' => $this->budget_details,
                'estimated_total' => $this->estimated_total,
                'estimated_total_currency' => $this->estimated_total_currency,
                'status_id' => $this->status_id,
            ]);

            if ($this->purchaseRequisition->isDirty()) {
                $this->purchaseRequisition->save();
                   session()->flash('message', __('Purchase Requisition updated successfully.'));
            } else {
                session()->flash('message', __('No changes were made!'));
                session()->flash('type', 'warning');
            }

            // Smart Sync Items
            $submittedItemIds = collect($this->items)->filter(fn($item) => !empty($item['id']))->pluck('id')->toArray();
            
            // Delete items that are no longer in the array
            $isDeleted = $this->purchaseRequisition->items()->whereNotIn('id', $submittedItemIds)->delete();
            if ($isDeleted > 0) {
                session()->flash('type', 'success');
                session()->flash('message', __('Items successfully updated.'));
            }
            
            foreach ($this->items as $index => $item) {
                if (!empty($item['item_name'])) {
                    $itemData = [
                        'line_number' => $index + 1,
                        'item_name' => $item['item_name'],
                        'item_description' => $item['item_description'] ?? null,
                        'quantity' => $item['quantity'] ?? 0,
                        'unit_id' => $item['unit_id'] ?? null,
                        'unit_price' => $item['unit_price'] ?? 0,
                        'currency' => $item['currency'] ?? null,
                        'status_id' => $item['status_id'] ?? null,
                    ];

                    if (!empty($item['id'])) {
                        // Update existing
                        $existingItem = $this->purchaseRequisition->items()->where('id', $item['id'])->first();
                        if ($existingItem) {
                            $existingItem->fill($itemData);
                            if ($existingItem->isDirty()) {
                                $existingItem->save();
                                session()->flash('type', 'success');
                                session()->flash('message', __('Item successfully updated.'));
                            } 
                        }
                    } else {
                        // Create new
                        $this->purchaseRequisition->items()->create(array_merge($itemData, [
                            'created_by' => auth()->id(),
                        ]));
                       session()->flash('type', 'success');
                       session()->flash('message', __('Item successfully created.'));
                    }
                }
            }
        });

     
        return $this->redirect(route('purchase_request.index'), navigate: true);
    }

    public function render()
    {
        if(Gate::denies('purchase_request.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        return view('livewire.org-app.purchase-request.edit', [
            'heading' => __('Edit Purchase Requisition'),
        ]);
    }
}
