<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCategory extends Model
{
    protected $fillable = [
        'slug', 'name_fr', 'name_en', 'description_fr', 'description_en',
        'icon', 'image', 'banner_image', 'order', 'is_active',
        'hero_title_html', 'hero_subtitle', 'hero_chips', 'programs_intro',
        'description_html', 'benefits', 'benefits_title', 'benefits_intro',
        'audience', 'audience_intro',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'hero_chips' => 'array',
            'benefits' => 'array',
            'audience' => 'array',
        ];
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function activeServices(): HasMany
    {
        return $this->hasMany(Service::class)->where('is_active', true)->orderBy('order');
    }
}
