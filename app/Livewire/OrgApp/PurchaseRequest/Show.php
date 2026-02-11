<?php

namespace App\Livewire\OrgApp\PurchaseRequest;

use Livewire\Component;
use App\Models\PurchaseRequisition;

class Show extends Component
{
    public PurchaseRequisition $purchaseRequisition;

    public function mount(PurchaseRequisition $purchaseRequisition)
    {
        $this->purchaseRequisition = $purchaseRequisition->load(['items.unit', 'status', 'creator']);
    }

    public function render()
    {
        return view('livewire.org-app.purchase-request.show');
    }
}
