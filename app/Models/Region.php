<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property string $region_name
 */
class Region extends Model
{
    use HasFactory;

    protected $fillable =['region_name'];
    
}
