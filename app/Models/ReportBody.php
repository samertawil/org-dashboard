<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportBody extends Model
{
    protected $table = 'report_body';

    protected $fillable = [
        'report_id',
        'item_order',
        'content',
        'observation',
        'status_id',
        'attachments',
        'report_body_attachments',
    ];

    protected $casts = [
        'attachments' => 'array',
        'report_body_attachments' => 'array',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class, 'report_id');
    }
}
