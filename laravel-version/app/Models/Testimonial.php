<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'name_fr',
        'name_en',
        'role_fr',
        'role_en',
        'content_fr',
        'content_en',
        'image',
        'rating',
        'is_active',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
