<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class activityBeneficiaryName extends Model
{
   protected $fillable=[
    'activity_id',
    'displacement_camps_id',
    'identity_number',
    'full_name',
    'phone',
    'receipt_date',
    'receive_method',
    'receive_by_name'
   ];

   public function activity()
   {
       return $this->belongsTo(Activity::class);
   }

   public function displacementCamp()
   {
       return $this->belongsTo(displacementCamp::class, 'displacement_camps_id');
   }

   public function status()
   {
       return $this->belongsTo(Status::class, 'receive_method');
   }
}
