<?php

namespace App\Concerns\PurchaseRequest;
use App\Reposotries\PartnersRepo;
use App\Reposotries\StatusRepo;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;

trait PurchaseTrait
{
    #[Validate('required|integer')]
    public $request_number;

    #[Validate('required|date')]
    public $request_date;

    #[Validate('nullable|string|max:255')]
    public $description;

    #[Validate('nullable|string')]
    public $justification;

    #[Validate('required|array')]
    public $suggested_vendor_ids = [];

    #[Validate('nullable|date')]
    public $quotation_deadline;

    #[Validate('nullable|string')]
    public $budget_details;

    #[Validate('required')]
    public $estimated_total_dollar = null;

    #[Validate('required')]
    public $estimated_total_nis = null;

    #[Validate('nullable|integer')]
    public $estimated_total_currency;

    #[Validate('required|exists:statuses,id')]
    public $status_id;

    public $order_count = null;
    public $exchange_rate = null;

    public $items = [];
    
    // public $partners = [];
    public $statuses = [];
    public $units = [];
    public $currencies = [];

    public function bootPurchaseTrait()
    {

        $this->statuses = StatusRepo::statuses()->where('p_id_sub', config('appConstant.purchase_requisition_statuses'));  
        $this->units = StatusRepo::statuses()->where('p_id_sub', config('appConstant.units_statuses'));   
        $this->exchange_rate = \App\Models\CurrancyValue::latest('exchange_date')->first()?->currency_value;
     
    }

    #[Computed()]
    public function partners() {
 
       return    PartnersRepo::partners()->where('type_id',112);
    }
    
   
    public function addPurchaseRequisitionItem()
    {
        $this->items[] = [
            'item_name' => '',
            'item_description' => '',
            'quantity' => 1,
            'unit_id' => null,
            'unit_price' => 0,
            'currency' => null,
        ];
    }

    public function removePurchaseRequisitionItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }
}
