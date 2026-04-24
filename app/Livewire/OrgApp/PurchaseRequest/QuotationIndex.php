<?php

namespace App\Livewire\OrgApp\PurchaseRequest;

use App\Models\PurchaseQuotationResponse;
use Livewire\Component;
use Livewire\WithPagination;

class QuotationIndex extends Component
{
    use WithPagination;

    public $search_vendor;
    public $search_pr_number;

    public function render()
    {
        $quotations = PurchaseQuotationResponse::with(['vendor', 'purchaseRequisition', 'currency'])
            ->when($this->search_vendor, function ($query) {
                $query->whereHas('vendor', function ($q) {
                    $q->where('name', 'like', '%' . $this->search_vendor . '%');
                });
            })
            ->when($this->search_pr_number, function ($query) {
                $query->whereHas('purchaseRequisition', function ($q) {
                    $q->where('request_number', 'like', '%' . $this->search_pr_number . '%');
                });
            })
            ->orderBy('submitted_at', 'desc')
            ->paginate(10);

        return view('livewire.org-app.purchase-request.quotation-index', [
            'quotations' => $quotations
        ]);
    }
}
