<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequisitionItem extends Model
{
    protected $table = 'purchase_requisition_items';

    protected $fillable = [
        'purchase_requisition_id',
        'line_number',
        'item_name',
        'item_description',
        'quantity',
        'unit_id',
        'unit_price',
        'currency',
        'created_by',
        'status_id',
    ];

    public function purchaseRequisition()
    {
        return $this->belongsTo(PurchaseRequisition::class, 'purchase_requisition_id');
    }

    public function unit()
    {
        return $this->belongsTo(Status::class, 'unit_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
