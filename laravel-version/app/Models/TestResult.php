<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestResult extends Model
{
    protected $fillable = ['test_id', 'student_id', 'teacher_id', 'score', 'level', 'letter_grade', 'status', 'sections', 'taken_at'];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'sections' => 'array',
            'taken_at' => 'datetime',
        ];
    }

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
