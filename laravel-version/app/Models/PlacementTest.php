<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlacementTest extends Model
{
    protected $fillable = [
        'booking_id', 'student_id', 'total_questions', 'score', 'level', 'category_scores',
        'time_taken', 'started_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'category_scores' => 'array',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(TestAnswer::class);
    }
}
