<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityBeneficiary extends Model
{
    protected $fillable = ['activity_id', 'beneficiary_type', 'beneficiaries_count','cost_for_each_beneficiary', 'notes'];

    public function project()
    {
        return $this->belongsTo(Activity::class);
    }

    public function beneficiaryType()
    {
        return $this->belongsTo(Status::class, 'beneficiary_type');
    }

    // public function status()
    // {
    //     return $this->belongsTo(Status::class, 'status_id');
    // }
}
