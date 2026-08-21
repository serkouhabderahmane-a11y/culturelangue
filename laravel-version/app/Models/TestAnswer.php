<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestAnswer extends Model
{
    protected $fillable = ['placement_test_id', 'question_id', 'selected_index', 'is_correct'];

    public function placementTest(): BelongsTo
    {
        return $this->belongsTo(PlacementTest::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(TestQuestion::class);
    }
}
