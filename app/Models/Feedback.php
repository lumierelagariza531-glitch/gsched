<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $fillable = [
        'appointment_id',
        'student_id',
        'rating',
        'comments',
        'submitted_at',
        'sqd0',
        'sqd1',
        'sqd2',
        'sqd3',
        'sqd4',
        'sqd5',
        'sqd6',
        'sqd7',
        'sqd8',
        'suggestions',
    ];

    protected $casts = [
        'rating' => 'decimal:1',
        'submitted_at' => 'datetime',
    ];

    public const SQD_OPTIONS = [
        'strongly_disagree' => 'Strongly Disagree',
        'disagree' => 'Disagree',
        'neither' => 'Neither Agree nor Disagree',
        'agree' => 'Agree',
        'strongly_agree' => 'Strongly Agree',
        'not_applicable' => 'N/A / Not Applicable',
    ];

    public static function calculateRating(array $answers): ?float
    {
        $scores = [
            'strongly_disagree' => 1,
            'disagree' => 2,
            'neither' => 3,
            'agree' => 4,
            'strongly_agree' => 5,
        ];
        $values = collect($answers)
            ->map(fn ($answer) => $scores[$answer] ?? null)
            ->filter(fn ($score) => $score !== null);

        if ($values->isEmpty()) {
            return null;
        }

        return round(($values->avg() * 2)) / 2;
    }

    public const SQD_QUESTIONS = [
        'sqd0' => 'I am satisfied with the service that I availed.',
        'sqd1' => 'I spent a reasonable amount of time for my transaction.',
        'sqd2' => 'The office followed the transaction\'s requirements and steps based on the information provided.',
        'sqd3' => 'The steps (including payment) I needed to do for my transaction were easy and simple.',
        'sqd4' => 'I easily found information about my transaction from the office or its website.',
        'sqd5' => 'I paid a reasonable amount of fees for my transaction. (If service was free, mark the \'N/A\' column.)',
        'sqd6' => 'I feel the office was fair to everyone, or \'walang palakasan,\' during my transaction.',
        'sqd7' => 'I was treated courteously by the staff, and (if asked for help) the staff was helpful.',
        'sqd8' => 'I got what I needed from the government office, or (if denied) denial of request was sufficiently explained to me.',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}