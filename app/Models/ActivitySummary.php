<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivitySummary extends Model
{
    protected $guarded = [];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}
