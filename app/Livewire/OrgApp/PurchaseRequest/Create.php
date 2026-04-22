<?php

namespace App\Livewire\OrgApp\PurchaseRequest;

use App\Concerns\PurchaseRequest\PurchaseTrait;
use App\Models\PurchaseRequisition;
use App\Services\ManagecurrencyServices;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;


class Create extends Component
{
    use PurchaseTrait;
    public $Currenyvalue;
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
        $value = new ManagecurrencyServices();
        $this->Currenyvalue = $value->convertCurrency($this->estimated_total_nis,$this->estimated_total_dollar);
        
        DB::transaction(function () {
            $purchaseRequisition = PurchaseRequisition::create([
                'request_number' => $this->request_number,

                'request_date' => $this->request_date,
                'description' => $this->description? : null,
                'justification' => $this->justification? : null,
                'suggested_vendor_ids' => $this->suggested_vendor_ids,
                'need_by_date' => $this->need_by_date,
                'budget_details' => $this->budget_details? : null,
                'estimated_total_dollar' => $this->estimated_total_dollar ?: $this->Currenyvalue,
                'estimated_total_nis' => $this->estimated_total_nis ?: $this->Currenyvalue,
      
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
        if(Gate::denies('purchase_request.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        return view('livewire.org-app.purchase-request.create', [
            'heading' => __('Create Purchase Requisition'),
        ]);
    }
}
