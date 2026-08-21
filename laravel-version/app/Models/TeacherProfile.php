<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeacherProfile extends Model
{
    protected $fillable = [
        'user_id', 'employee_number', 'specialization', 'department', 'bio_fr', 'bio_en',
        'languages', 'certifications', 'hourly_rate', 'hourly_rate_solo', 'hourly_rate_group',
        'contract_hours_month', 'hire_date', 'rating', 'availability',
    ];

    protected function casts(): array
    {
        return [
            'languages' => 'array',
            'certifications' => 'array',
            'hourly_rate' => 'decimal:2',
            'hourly_rate_solo' => 'decimal:2',
            'hourly_rate_group' => 'decimal:2',
            'hire_date' => 'date',
            'rating' => 'decimal:1',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function specialties(): HasMany
    {
        return $this->hasMany(TeacherSpecialty::class, 'teacher_id', 'user_id');
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(TeacherAvailability::class, 'teacher_id', 'user_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(TeacherNote::class, 'teacher_id', 'user_id');
    }
}
