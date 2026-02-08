<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityPartner extends Model
{
    protected $fillable = [
        'activity_id',
        'partner_id',
        'notes',
    ];


    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
    public function partner()
    {
        return $this->belongsTo(PartnerInstitution::class);
    }

}
