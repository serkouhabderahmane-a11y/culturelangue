<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherHour extends Model
{
    protected $fillable = ['teacher_id', 'lesson_id', 'hours', 'hour_type', 'work_date'];

    protected function casts(): array
    {
        return [
            'hours' => 'decimal:2',
            'work_date' => 'date',
        ];
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
