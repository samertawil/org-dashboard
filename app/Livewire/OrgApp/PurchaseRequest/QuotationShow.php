<?php

namespace App\Livewire\OrgApp\PurchaseRequest;

use App\Models\PurchaseQuotationResponse;
use Livewire\Component;

class QuotationShow extends Component
{
    public $quotation;

    public function mount($quotation)
    {
        $this->quotation = PurchaseQuotationResponse::with(['vendor', 'prices.requisitionItem.unit', 'currency', 'purchaseRequisition'])
            ->findOrFail($quotation);
    }

    public function downloadPdf()
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.quotation', [
            'quotation' => $this->quotation
        ])->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'quotation-' . $this->quotation->id . '.pdf');
    }

    public function render()
    {
        return view('livewire.org-app.purchase-request.quotation-show');
    }
}
