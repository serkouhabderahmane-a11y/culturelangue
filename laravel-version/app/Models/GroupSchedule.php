<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GroupSchedule extends Model
{
    protected $fillable = [
        'service_id', 'label', 'day_of_week', 'start_time', 'end_time', 'start_date', 'end_date',
        'room', 'max_students', 'current_students', 'status',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    public function seatsRemaining(): int
    {
        return max(0, $this->max_students - $this->current_students);
    }
}
