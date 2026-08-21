<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Program extends Model
{
    protected $fillable = [
        'service_id', 'name_fr', 'name_en', 'description_fr', 'description_en', 'duration', 'price',
        'price_label_fr', 'price_label_en', 'sessions_count', 'hours_count', 'features_fr', 'features_en',
        'rate_per_hour', 'badge', 'badge_color', 'is_popular', 'is_active', 'order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'rate_per_hour' => 'decimal:2',
            'features_fr' => 'array',
            'features_en' => 'array',
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
