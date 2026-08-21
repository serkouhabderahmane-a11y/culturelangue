<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
        'name',
        'file_name',
        'mime_type',
        'path',
        'disk',
        'collection',
        'size',
        'custom_properties',
        'model_type',
        'model_id',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'custom_properties' => 'array',
        ];
    }

    public function model()
    {
        return $this->morphTo();
    }
}
