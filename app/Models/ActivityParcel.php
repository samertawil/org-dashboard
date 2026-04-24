<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityParcel extends Model
{
    protected $fillable = ['activity_id', 'parcel_type', 'distributed_parcels_count', 'cost_for_each_parcel','notes','unit_id','purchase_requisition_id'];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function parcelType()
    {
        return $this->belongsTo(Status::class, 'parcel_type');
    }

    public function unit()
    {
        return $this->belongsTo(Status::class, 'unit_id');
    }
    
    public function purchaseRequisition()
    {
        return $this->belongsTo(PurchaseRequisition::class, 'purchase_requisition_id');
    }
   
}
