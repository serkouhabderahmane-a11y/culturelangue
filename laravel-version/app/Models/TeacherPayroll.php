<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherPayroll extends Model
{
    protected $fillable = [
        'teacher_id', 'period_start', 'period_end', 'solo_hours', 'group_hours', 'solo_rate',
        'group_rate', 'total_amount', 'status', 'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'solo_hours' => 'decimal:2',
            'group_hours' => 'decimal:2',
            'solo_rate' => 'decimal:2',
            'group_rate' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
