<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestQuestion extends Model
{
    protected $fillable = ['test_type', 'category', 'question_text', 'passage_text', 'options', 'correct_index', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['options' => 'array', 'is_active' => 'boolean'];
    }

    public function answers(): HasMany
    {
        return $this->hasMany(TestAnswer::class);
    }
}
