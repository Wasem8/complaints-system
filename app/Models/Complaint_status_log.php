<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint_status_log extends Model
{
    protected $fillable =
    [
        'complaint_id',
        'new_status',
        'note',
    ];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }
}
