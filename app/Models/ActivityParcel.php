<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityParcel extends Model
{
    protected $fillable = ['activity_id', 'parcel_type', 'distributed_parcels_count', 'cost_for_each_parcel','notes'];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function parcelType()
    {
        return $this->belongsTo(Status::class, 'parcel_type');
    }

}
