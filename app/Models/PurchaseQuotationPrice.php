<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseQuotationPrice extends Model
{
    protected $fillable = [
        'quotation_response_id',
        'purchase_requisition_item_id',
        'offered_price',
        'vendor_item_notes',
    ];

    public function quotationResponse()
    {
        return $this->belongsTo(PurchaseQuotationResponse::class, 'quotation_response_id');
    }

    public function requisitionItem()
    {
        return $this->belongsTo(PurchaseRequisitionItem::class, 'purchase_requisition_item_id');
    }
}
