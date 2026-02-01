<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedBack extends Model
{
    protected $fillable = ['activity_id', 'rating', 'comment', 'client_name'];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}
