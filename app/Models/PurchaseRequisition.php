<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PartnerInstitution;

class PurchaseRequisition extends Model
{
    protected $table = 'purchase_requisitions';

    protected $fillable = [
        'request_number',
        'request_year',
        'request_date',
        'description',
        'justification',
        'suggested_vendor_ids',
        'need_by_date',
        'budget_details',
        'estimated_total_dollar',
        'estimated_total_nis',
     
        'created_by',
        'status_id',
        'attachments'
    ];

    protected $casts = [
        'suggested_vendor_ids' => 'array',
        'attachments' => 'array',
        'request_date' => 'date',
        'need_by_date' => 'date',
        'request_year' => 'date:Y'  
    ];

    public function items()
    {
        return $this->hasMany(PurchaseRequisitionItem::class, 'purchase_requisition_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // public function currency()
    // {
        
    //      return $this->belongsTo(Status::class, 'estimated_total_currency');
    // }

    public function getSuggestedVendorsAttribute()
    {
        if (empty($this->suggested_vendor_ids)) {
            return collect();
        }
        return PartnerInstitution::whereIn('id', $this->suggested_vendor_ids)->get();
    }
}
