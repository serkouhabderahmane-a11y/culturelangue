<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentSkillProgress extends Model
{
    public $timestamps = false;

    protected $fillable = ['student_id', 'skill', 'percentage', 'updated_at'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
