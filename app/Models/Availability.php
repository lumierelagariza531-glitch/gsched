<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    protected $table = 'availability';
    
    protected $fillable = [
        'guidance_associate_id',
        'available_date',
        'start_time',
        'end_time',
        'slot_duration',
        'status',
    ];

    protected $casts = [
        'available_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function guidanceAssociate()
    {
        return $this->belongsTo(User::class, 'guidance_associate_id');
    }

    public function isAvailable()
    {
        return $this->status === 'available';
    }

    public function isBooked()
    {
        return $this->status === 'booked';
    }
}