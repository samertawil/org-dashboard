<?php

namespace App\Livewire\OrgApp\PurchaseRequest;

use App\Models\PurchaseRequisition;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Show extends Component
{
    public PurchaseRequisition $purchaseRequisition;

    public function mount(PurchaseRequisition $purchaseRequisition)
    {
        $this->purchaseRequisition = $purchaseRequisition->load(['items.unit', 'status', 'creator']);
    }

    public function render()
    {
        if(Gate::denies('purchase_request.index')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        return view('livewire.org-app.purchase-request.show');
    }
}
