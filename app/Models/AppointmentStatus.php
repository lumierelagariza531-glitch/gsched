<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentStatus extends Model
{
    protected $fillable = ['name', 'label', 'color', 'chart_color', 'description', 'sort_order'];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}