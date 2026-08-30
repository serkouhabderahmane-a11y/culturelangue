<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CalendarSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'calendar_program_id',
        'session_number',
        'title',
        'start_date',
        'end_date',
        'days_text',
        'start_time',
        'end_time',
        'start_time_2',
        'end_time_2',
        'duration_text',
        'duration_weeks',
        'notes',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(CalendarProgram::class, 'calendar_program_id');
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(CalendarMeeting::class);
    }
}
