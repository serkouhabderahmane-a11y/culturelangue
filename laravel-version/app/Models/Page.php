<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'slug',
        'title_fr',
        'title_en',
        'content_fr',
        'content_en',
        'meta_title_fr',
        'meta_title_en',
        'meta_description_fr',
        'meta_description_en',
        'template',
        'image',
        'is_active',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
