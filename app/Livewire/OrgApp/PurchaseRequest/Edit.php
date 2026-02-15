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
            $this->purchaseRequisition->update([
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

            // Re-create items
            $this->purchaseRequisition->items()->delete();
            
            foreach ($this->items as $index => $item) {
                if (!empty($item['item_name'])) {
                    $this->purchaseRequisition->items()->create([
                        'line_number' => $index + 1,
                        'item_name' => $item['item_name'],
                        'item_description' => $item['item_description'],
                        'quantity' => $item['quantity'],
                        'unit_id' => $item['unit_id'],
                        'unit_price' => $item['unit_price'],
                        'currency' => $item['currency'],
                        'created_by' => auth()->id(), // Preserve original? Or new val? Usually auth id on new creation
                        'status_id' => $item['status_id'] ?? null,
                    ]);
                }
            }
        });

        session()->flash('message', __('Purchase Requisition updated successfully.'));
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
