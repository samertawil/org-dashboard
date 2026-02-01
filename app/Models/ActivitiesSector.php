<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivitiesSector extends Model
{
   protected $table = 'activities_by_sectors_vw';

   protected $fillable = [
      'sector_id',
      'sector_name',
      'activites_date',
     
   ];
}
