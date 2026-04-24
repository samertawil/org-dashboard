<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PartnerInstitution;
use Illuminate\Support\Str;

class PurchaseRequisition extends Model
{
    protected $table = 'purchase_requisitions';

    protected static function booted()
    {
        static::creating(function ($pr) {
            if (empty($pr->token)) {
                $pr->token = Str::random(32);
            }
        });
    }

    public function getTokenAttribute($value)
    {
        if (empty($value)) {
            $value = Str::random(32);
            $this->update(['token' => $value]);
        }
        return $value;
    }

    public function calculateVendorPin($vendorId)
    {
        // معادلة رياضية ثابتة تولد 4 أرقام فريدة بناءً على المورد والتوكن
        $seed = $this->token . $vendorId;
        return str_pad(abs(crc32($seed)) % 10000, 4, '0', STR_PAD_LEFT);
    }

    protected $fillable = [
        'request_number',
        'request_year',
        'request_date',
        'description',
        'justification',
        'suggested_vendor_ids',
        'quotation_deadline',
        'budget_details',
        'estimated_total_dollar',
        'estimated_total_nis',
        'created_by',
        'status_id',
        'order_count',
        'attachments',
        'token',
        'quotation_deadline',
    ];

    protected $casts = [
        'suggested_vendor_ids' => 'array',
        'attachments' => 'array',
        'request_date' => 'date',
        'quotation_deadline' => 'date',
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

    public function quotations()
    {
        return $this->hasMany(PurchaseQuotationResponse::class, 'purchase_requisition_id');
    }
}
