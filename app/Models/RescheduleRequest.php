<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RescheduleRequest extends Model
{
    protected $fillable = [
        'appointment_id',
        'requested_by',
        'old_date',
        'old_start_time',
        'old_end_time',
        'requested_date',
        'requested_start_time',
        'requested_end_time',
        'reason',
        'status',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'old_date' => 'date',
        'old_start_time' => 'datetime:H:i',
        'old_end_time' => 'datetime:H:i',
        'requested_date' => 'date',
        'requested_start_time' => 'datetime:H:i',
        'requested_end_time' => 'datetime:H:i',
        'reviewed_at' => 'datetime',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }
}