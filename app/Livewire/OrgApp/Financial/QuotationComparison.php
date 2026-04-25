<?php

namespace App\Livewire\OrgApp\Financial;

use App\Models\PurchaseRequisition;
use App\Models\PurchaseQuotationResponse;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

use App\Exports\QuotationComparisonExport;
use Maatwebsite\Excel\Facades\Excel;

class QuotationComparison extends Component
{
    public $prId;
    public $pr;
    public $quotations;
    public $acceptedQuotation;
    public $exchangeRate;

    public function mount($id)
    {
        $this->prId = $id;
        $this->exchangeRate = \App\Models\CurrancyValue::orderBy('exchange_date', 'desc')->first()?->currency_value ?? 3.5; // قيمة افتراضية في حال عدم وجود بيانات
        $this->loadData();
    }

    public function loadData()
    {
        $this->pr = PurchaseRequisition::with(['items.unit'])
            ->findOrFail($this->prId);

        $this->quotations = PurchaseQuotationResponse::with(['vendor', 'prices.requisitionItem', 'currency'])
            ->where('purchase_requisition_id', $this->prId)
            ->get();

        $this->acceptedQuotation = $this->quotations->where('status_id', 1)->first();
    }

    public function acceptQuotation($quotationId)
    {
        $quotation = PurchaseQuotationResponse::findOrFail($quotationId);
        
        DB::transaction(function () use ($quotation) {
            // Reset all quotations for this PR to null status
            PurchaseQuotationResponse::where('purchase_requisition_id', $this->prId)
                ->update(['status_id' => null]);

            // Mark this one as accepted
            $quotation->update(['status_id' => 176]); // Accepted
            
            // تحديث أسعار الأصناف الفردية في طلب الشراء الأصلي بناءً على العرض الفائز
            foreach ($quotation->prices as $price) {
                \App\Models\PurchaseRequisitionItem::where('id', $price->purchase_requisition_item_id)
                    ->update(['unit_price' => $price->offered_price]);
            }

            // Update PR status and estimated total (linked to the accepted price)
            $this->pr->update([
                'estimated_total_nis' => $quotation->total_amount, 
                'status_id' => 109,
                'order_count' => 0,
            ]);
        });

        $this->loadData();
        $this->dispatch('notify', ['message' => 'تم ترسية العرض بنجاح', 'type' => 'success']);
    }

    public function exportExcel()
    {
        return Excel::download(new QuotationComparisonExport($this->pr), 'quotation-comparison-' . $this->pr->request_number . '.xlsx');
    }

    public function render()
    {
        return view('livewire.org-app.financial.quotation-comparison')
            ->layout('layouts.app');
    }
}
