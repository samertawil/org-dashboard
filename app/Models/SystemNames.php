<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemNames extends Model
{
    use HasFactory;

    protected $table='system_names';

    protected $fillable=[ 'system_name','description' ];
       
   
}
