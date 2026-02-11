<?php

namespace App\Livewire\OrgApp\PurchaseRequest;

use Livewire\Component;
use App\Models\PurchaseRequisition;
use App\Concerns\PurchaseRequest\PurchaseTrait;
use Illuminate\Support\Facades\DB;
use App\Models\PartnerInstitution;
use App\Reposotries\StatusRepo;
use App\Reposotries\PartnersRepo;

class Create extends Component
{
    use PurchaseTrait;

    public function mount()
    {
        $this->bootPurchaseTrait(); // Load default data
        $this->addPurchaseRequisitionItem();
        $this->request_date = now()->toDateString();
       
    }

    public function save()
    {
        $this->validate();

        DB::transaction(function () {
            $purchaseRequisition = PurchaseRequisition::create([
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
