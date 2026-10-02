<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'related_appointment_id',
        'facebook_profile_url',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'related_appointment_id');
    }

    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function getIconAttribute()
    {
        return match ($this->type) {
            'appointment_request' => 'bi-calendar-plus',
            'appointment_approved' => 'bi-calendar-check',
            'appointment_rejected' => 'bi-calendar-x',
            'appointment_cancelled' => 'bi-calendar-x',
            'appointment_rescheduled' => 'bi-calendar-event',
            'appointment_meeting_link' => 'bi-camera-video',
            'appointment_reminder' => 'bi-bell',
            'case_assessed' => 'bi-clipboard-data',
            'case_high_severity' => 'bi-exclamation-triangle',
            'system' => 'bi-info-circle',
            'feedback' => 'bi-chat-text',
            default => 'bi-bell',
        };
    }

    public function getTypeColorAttribute()
    {
        return match ($this->type) {
            'appointment_request' => '#F7AD19',
            'appointment_approved' => '#429EBD',
            'appointment_rejected' => '#F27F0C',
            'appointment_cancelled' => '#F27F0C',
            'appointment_rescheduled' => '#9FE7F5',
            'appointment_meeting_link' => '#429EBD',
            'appointment_reminder' => '#429EBD',
            'case_assessed' => '#429EBD',
            'case_high_severity' => '#F27F0C',
            'system' => '#64748B',
            'feedback' => '#9FE7F5',
            default => '#64748B',
        };
    }
}