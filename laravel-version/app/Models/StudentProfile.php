<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentProfile extends Model
{
    protected $fillable = [
        'user_id', 'student_number', 'date_of_birth', 'address', 'city', 'province', 'postal_code',
        'country', 'native_language', 'current_level', 'target_level', 'goal', 'enrollment_date',
        'preferred_language', 'emergency_contact_name', 'emergency_contact_phone',
        'language_level_fr', 'language_level_en', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'enrollment_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'student_id', 'user_id');
    }

    public function skillProgress(): HasMany
    {
        return $this->hasMany(StudentSkillProgress::class, 'student_id', 'user_id');
    }
}
