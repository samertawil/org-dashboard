<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class displacementCampResident extends Model
{
    protected $fillable = [
        'displacement_camp_id',
        'resident_type',
        'identity_number',
        'full_name',
        'birth_date',
        'phone',
        'gender',
        'activation',
    ];

    public function displacementCamp()
    {
        return $this->belongsTo(displacementCamp::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'resident_type');
    }
}
