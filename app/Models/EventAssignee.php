<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventAssignee extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'employee_id',
        'notes',
        'response',
        'status',
        'assigned_by',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

   
}
