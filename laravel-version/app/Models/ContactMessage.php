<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = ['first_name', 'last_name', 'email', 'phone', 'subject', 'message', 'consent', 'status', 'notes', 'ip_address'];

    protected function casts(): array
    {
        return ['consent' => 'boolean'];
    }
}
