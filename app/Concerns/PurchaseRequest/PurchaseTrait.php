<?php

namespace App\Concerns\PurchaseRequest;

use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use App\Models\PartnerInstitution;
use App\Models\Status;
use App\Reposotries\StatusRepo;
use App\Reposotries\PartnersRepo;

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

    #[Validate('nullable|array')]
    public $suggested_vendor_ids = [];

    #[Validate('nullable|date')]
    public $need_by_date;

    #[Validate('nullable|string')]
    public $budget_details;

    #[Validate('nullable')]
    public $estimated_total_dollar = null;

    #[Validate('nullable')]
    public $estimated_total_nis = null;

    #[Validate('nullable|integer')]
    public $estimated_total_currency;

    #[Validate('nullable|exists:statuses,id')]
    public $status_id;

    public $items = [];
    
    public $partners = [];
    public $statuses = [];
    public $units = [];
    public $currencies = [];

    public function bootPurchaseTrait()
    {
        $this->partners = PartnersRepo::partners()->where('type_id',112);
        $this->statuses = StatusRepo::statuses()->where('p_id_sub', config('appConstant.purchase_requisition_statuses'));  
        $this->units = StatusRepo::statuses()->where('p_id_sub', config('appConstant.units_statuses'));   
     
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
