<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'authority',
        'description',
        'location_text',
        'status',
        'tracking_number'
    ];

    public function files()
    {
        return $this->hasMany(Complaint_file::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
