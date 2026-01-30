<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityParcel extends Model
{
    protected $guarded = [];

    public function project()
    {
        return $this->belongsTo(Activity::class);
    }

    public function parcelType()
    {
        return $this->belongsTo(Status::class, 'parcel_type');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }
}
