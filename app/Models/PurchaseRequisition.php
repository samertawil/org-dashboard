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

    public function getCachedCreatorAttribute()
    {
        return \Illuminate\Support\Facades\Cache::remember("user_basic_{$this->created_by}", 86400, function() {
            return $this->creator;
        });
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
        
        // Cache the result on the instance to prevent duplicate queries
        if (!$this->relationLoaded('suggestedVendorsCache')) {
            $vendors = \Illuminate\Support\Facades\Cache::remember("pr_vendors_{$this->id}", 3600, function() {
                return PartnerInstitution::whereIn('id', $this->suggested_vendor_ids)->get();
            });
            $this->setRelation('suggestedVendorsCache', $vendors);
        }

        return $this->getRelation('suggestedVendorsCache');
    }

    public function quotations()
    {
        return $this->hasMany(PurchaseQuotationResponse::class, 'purchase_requisition_id');
    }
}
