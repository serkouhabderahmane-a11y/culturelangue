<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoursePackage extends Model
{
    protected $fillable = [
        'service_id', 'name', 'name_en', 'hours', 'sessions', 'price', 'rate_per_hour',
        'badge', 'badge_color', 'is_popular', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'rate_per_hour' => 'decimal:2',
            'is_popular' => 'boolean',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
