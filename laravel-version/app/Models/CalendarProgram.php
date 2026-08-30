<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CalendarProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_fr',
        'name_en',
        'category',
        'language',
        'service_id',
        'color',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(CalendarSession::class)->orderBy('sort_order');
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(CalendarMeeting::class);
    }
}
