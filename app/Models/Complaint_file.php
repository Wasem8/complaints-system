<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint_file extends Model
{
    protected $fillable = ['complaint_id', 'file_path', 'file_type'];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }

    public function getUrlAttribute()
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }
}
