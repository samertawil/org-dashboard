<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerInstitution extends Model
{
   protected $fillable = [
    'name',
'manager_name',
'type_id',
'location',
'phone',
'email',
'website',
'description','activation'
   ];

   public function type()
   {
       return $this->belongsTo(Status::class, 'type_id');
   }
}
