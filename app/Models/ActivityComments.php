<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityComments extends Model
{
    protected $guarded = [];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
