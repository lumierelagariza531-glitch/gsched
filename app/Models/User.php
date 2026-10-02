<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'role_id',
        'student_id',
        'student_id_front',
        'student_id_back',
        'date_of_birth',
        'school',
        'facebook_profile_url',
        'age',
        'gender',
        'professional_title',
        'educational_background',
        'professional_credentials',
        'certifications',
        'trainings',
        'areas_of_expertise',
        'professional_experience',
        'professional_biography',
        'office_location',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'phone',
        'password',
        'profile_photo',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'age' => 'integer',
            'date_of_birth' => 'date',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function studentAppointments()
    {
        return $this->hasMany(Appointment::class, 'student_id');
    }

    public function guidanceAppointments()
    {
        return $this->hasMany(Appointment::class, 'guidance_associate_id');
    }

    public function availability()
    {
        return $this->hasMany(Availability::class, 'guidance_associate_id');
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class, 'student_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function rescheduleRequests()
    {
        return $this->hasMany(RescheduleRequest::class, 'requested_by');
    }

    public function reviewedRescheduleRequests()
    {
        return $this->hasMany(RescheduleRequest::class, 'reviewed_by');
    }

    public function getFullNameAttribute()
    {
        $parts = [$this->first_name];
        if ($this->middle_name) {
            $parts[] = $this->middle_name;
        }
        $parts[] = $this->last_name;
        return implode(' ', $parts);
    }

    public function getDisplayRoleNameAttribute(): string
    {
        if ($this->isGuidanceAssociate()) {
            $school = trim((string) $this->school);

            return $school !== '' ? "{$school} Guidance Associate" : 'Guidance Associate';
        }

        return $this->role
            ? ucfirst(str_replace('_', ' ', $this->role->name))
            : '';
    }

    public function getStudentIdFrontUrlAttribute(): ?string
    {
        return null;
    }

    public function getStudentIdBackUrlAttribute(): ?string
    {
        return null;
    }

    public function getSafeFacebookProfileUrlAttribute(): ?string
    {
        $url = trim((string) $this->facebook_profile_url);
        $parts = parse_url($url);

        if (
            !$parts
            || strtolower($parts['scheme'] ?? '') !== 'https'
            || !in_array(strtolower($parts['host'] ?? ''), ['facebook.com', 'www.facebook.com', 'm.facebook.com'], true)
            || !filter_var($url, FILTER_VALIDATE_URL)
        ) {
            return null;
        }

        return $url;
    }

    public function isStudent()
    {
        return $this->role && $this->role->name === 'student';
    }

    public function isGuidanceAssociate()
    {
        return $this->role && $this->role->name === 'guidance_associate';
    }

    public function isAdmin()
    {
        return $this->role && $this->role->name === 'admin';
    }

    public function unreadNotificationsCount()
    {
        return $this->notifications()->where('is_read', false)->count();
    }

    public static function getSchoolOptions(): array
    {
        return ['STCS', 'SNHS', 'SCJE', 'STED', 'SAS', 'SME', 'SOE', 'SBM'];
    }

    public static function getGenderOptions(): array
    {
        return ['male' => 'Male', 'female' => 'Female', 'prefer_not_to_say' => 'Prefer not to say'];
    }
}