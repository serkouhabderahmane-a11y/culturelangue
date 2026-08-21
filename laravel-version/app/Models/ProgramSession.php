<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramSession extends Model
{
    protected $fillable = [
        'service_id', 'title', 'date_range', 'schedule_text', 'duration_text', 'state',
        'availability_text', 'cta_primary', 'cta_secondary', 'pause_message', 'sort_order',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
