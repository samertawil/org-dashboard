<?php

namespace App\Livewire\OrgApp\PurchaseRequest;

use Livewire\Component;
use App\Models\PurchaseRequisition;
use App\Concerns\PurchaseRequest\PurchaseTrait;
use Illuminate\Support\Facades\DB;


class Create extends Component
{
    use PurchaseTrait;

    public function mount()
    {
        $this->bootPurchaseTrait(); // Load default data
        $this->addPurchaseRequisitionItem();
        $this->request_date = now()->toDateString();
        $this->request_number = $this->generateRequestNumber();
       
    }

    public function generateRequestNumber()
    {
        $latestRequest = PurchaseRequisition::whereYear('request_date',$this->request_date)->count();
        return $latestRequest +1  ;
    }

    public function updatedRequestDate()   {
     
        $this->request_number = $this->generateRequestNumber();
    }
    public function save()
    {
        $this->validate();

        DB::transaction(function () {
            $purchaseRequisition = PurchaseRequisition::create([
                'request_number' => $this->request_number,

                'request_date' => $this->request_date,
                'description' => $this->description? : null,
                'justification' => $this->justification? : null,
                'suggested_vendor_ids' => $this->suggested_vendor_ids,
                'need_by_date' => $this->need_by_date,
                'budget_details' => $this->budget_details? : null,
                'estimated_total' => $this->estimated_total,
                'estimated_total_currency' => $this->estimated_total_currency,
                'status_id' => $this->status_id,
                'created_by' => auth()->id(),
            ]);

            foreach ($this->items as $item) {
                if (!empty($item['item_name'])) {
                    $purchaseRequisition->items()->create([
                        'line_number' => $item['line_number'] ?? ($loop->index ?? 0) + 1, // Auto increment line number if not set
                        'item_name' => $item['item_name'],
                        'item_description' => $item['item_description'],
                        'quantity' => $item['quantity'],
                        'unit_id' => $item['unit_id'],
                        'unit_price' => $item['unit_price'],
                        'currency' => $item['currency'],
                        'created_by' => auth()->id(),
                        'status_id' => $item['status_id'] ?? null, // Item status if any
                    ]);
                }
            }
        });

        session()->flash('message', __('Purchase Requisition created successfully.'));
        return $this->redirect(route('purchase_request.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.org-app.purchase-request.create', [
            'heading' => __('Create Purchase Requisition'),
        ]);
    }
}
