<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseQuotationResponse extends Model
{
    protected $fillable = [
        'purchase_requisition_id',
        'vendor_id',
        'total_amount',
        'currency_id',
        'status_id',
        'notes',
        'attachments',
        'submitted_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'submitted_at' => 'datetime',
    ];

    public function purchaseRequisition()
    {
        return $this->belongsTo(PurchaseRequisition::class);
    }

    public function vendor()
    {
        return $this->belongsTo(PartnerInstitution::class, 'vendor_id');
    }

    public function currency()
    {
        return $this->belongsTo(Status::class, 'currency_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function prices()
    {
        return $this->hasMany(PurchaseQuotationPrice::class, 'quotation_response_id');
    }
}
