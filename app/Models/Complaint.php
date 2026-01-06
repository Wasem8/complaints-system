<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Complaint extends Model
{

    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'department_id',
        'status',
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
    public function statusLogs()
    {
        return $this->hasMany(Complaint_status_log::class);
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function editNotification()
    {
        return $this->user->notifications()
            ->whereJsonContains('data->data->type', 'request_more_information')
            ->whereJsonContains('data->data->complaint_id', $this->id)
            ->where('data->data->used_for_edit', false)
            ->first();
    }

    public function canEdit(): bool
    {
        return $this->editNotification() !== null;
    }

}
