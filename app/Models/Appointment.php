<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'student_id',
        'guidance_associate_id',
        'appointment_status_id',
        'appointment_date',
        'start_time',
        'end_time',
        'purpose',
        'service_type',
        'concern_category',
        'appointment_mode',
        'online_meeting_url',
        'severity',
        'notes',
        'cancellation_reason',
        'reschedule_reason',
        'approved_at',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function guidanceAssociate()
    {
        return $this->belongsTo(User::class, 'guidance_associate_id');
    }

    public function status()
    {
        return $this->belongsTo(AppointmentStatus::class, 'appointment_status_id');
    }

    public function feedback()
    {
        return $this->hasOne(Feedback::class);
    }

    public function rescheduleRequests()
    {
        return $this->hasMany(RescheduleRequest::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'related_appointment_id');
    }

    public function isPending()
    {
        return $this->status && $this->status->name === 'pending';
    }

    public function isApproved()
    {
        return $this->status && $this->status->name === 'approved';
    }

    public function isCompleted()
    {
        return $this->status && $this->status->name === 'completed';
    }

    public function isCancelled()
    {
        return $this->status && $this->status->name === 'cancelled';
    }

    public function getFormattedDateAttribute()
    {
        return $this->appointment_date->format('F d, Y');
    }

    public function getFormattedTimeAttribute()
    {
        return $this->start_time->format('g:i A') . ' - ' . $this->end_time->format('g:i A');
    }

    public function canBeCancelled()
    {
        return in_array($this->status->name ?? '', ['pending', 'approved', 'reschedule_requested']);
    }

    public function canBeRescheduled()
    {
        return in_array($this->status->name ?? '', ['pending', 'approved']);
    }

    public function isHighSeverity()
    {
        return $this->severity === 'high';
    }

    public function isStudentInfoHiddenFromGuidance()
    {
        return $this->isHighSeverity();
    }

    public function isAssigned()
    {
        return $this->guidance_associate_id !== null;
    }

    public function severityLabel(): string
    {
        return match($this->severity) {
            'not_assessed' => 'Not Yet Assessed',
            'low' => 'Low',
            'moderate' => 'Moderate',
            'high' => 'High',
            default => 'Not Yet Assessed',
        };
    }

    public function isSeverityAssessed(): bool
    {
        return in_array($this->severity, ['low', 'moderate', 'high']);
    }

    public function getSafeOnlineMeetingUrlAttribute(): ?string
    {
        $url = $this->online_meeting_url;
        $scheme = is_string($url) ? strtolower(parse_url($url, PHP_URL_SCHEME) ?: '') : '';

        return in_array($scheme, ['http', 'https'], true) && filter_var($url, FILTER_VALIDATE_URL)
            ? $url
            : null;
    }
}