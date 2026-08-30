<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalendarMeeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'calendar_program_id',
        'calendar_session_id',
        'title',
        'event_date',
        'day_of_week',
        'start_time',
        'end_time',
        'slot',
        'event_type',
        'is_active',
    ];

    protected $casts = [
        'event_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(CalendarProgram::class, 'calendar_program_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(CalendarSession::class, 'calendar_session_id');
    }
}
