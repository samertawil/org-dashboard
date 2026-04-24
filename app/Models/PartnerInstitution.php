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
        'description',
        'activation'
    ];

    public function type()
    {
        return $this->belongsTo(Status::class, 'type_id');
    }

    protected static function booted()
    {
        static::saved(fn () => \Illuminate\Support\Facades\Cache::forget('partners-all'));
        static::deleted(fn () => \Illuminate\Support\Facades\Cache::forget('partners-all'));
    }
}
