<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    protected $fillable = [
        'booking_ref', 'user_id', 'service_id', 'program_id', 'calendar_program_id', 'calendar_session_id',
        'session_label', 'session_date', 'first_name', 'last_name', 'email', 'phone',
        'contact_method', 'notes', 'status', 'preferred_date', 'preferred_time', 'preferred_slot', 'payment_status',
        'placement_score', 'placement_level', 'oral_test_date', 'oral_test_slot', 'oral_test_status',
        'total_amount', 'currency', 'source', 'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'preferred_date' => 'datetime',
            'total_amount' => 'decimal:2',
            'oral_test_date' => 'date',
            'session_date' => 'date',
        ];
    }

    public function calendarProgram(): BelongsTo
    {
        return $this->belongsTo(CalendarProgram::class);
    }

    public function calendarSession(): BelongsTo
    {
        return $this->belongsTo(CalendarSession::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function teachingSessions(): HasMany
    {
        return $this->hasMany(TeachingSession::class);
    }

    public function enrollment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Enrollment::class);
    }

    public function placementTests(): HasMany
    {
        return $this->hasMany(PlacementTest::class);
    }

    public function scopeForStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
