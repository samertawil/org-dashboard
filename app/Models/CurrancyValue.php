<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurrancyValue extends Model
{
   protected $fillable = ['exchange_date', 'currency_value'];
}
