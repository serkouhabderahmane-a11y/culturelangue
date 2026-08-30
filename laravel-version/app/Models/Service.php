<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'service_category_id', 'slug', 'name_fr', 'name_en', 'short_description_fr', 'short_description_en',
        'description_fr', 'description_en', 'benefits_fr', 'benefits_en', 'prerequisites_fr', 'prerequisites_en',
        'learning_objectives_fr', 'learning_objectives_en', 'duration', 'price', 'image', 'banner_image', 'icon',
        'pricing_options', 'is_featured', 'is_active', 'order',
    ];

    protected function casts(): array
    {
        return [
            'benefits_fr' => 'array',
            'benefits_en' => 'array',
            'prerequisites_fr' => 'array',
            'prerequisites_en' => 'array',
            'learning_objectives_fr' => 'array',
            'learning_objectives_en' => 'array',
            'pricing_options' => 'array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    public function coursePackages(): HasMany
    {
        return $this->hasMany(CoursePackage::class);
    }

    public function groupSchedules(): HasMany
    {
        return $this->hasMany(GroupSchedule::class);
    }

    public function programSessions(): HasMany
    {
        return $this->hasMany(ProgramSession::class)->orderBy('sort_order');
    }

    public function benefits(): HasMany
    {
        return $this->hasMany(Benefit::class)->orderBy('sort_order');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function tests(): HasMany
    {
        return $this->hasMany(Test::class);
    }

    public function timeSlots(): HasMany
    {
        return $this->hasMany(TimeSlot::class);
    }

    public function calendarEvents(): HasMany
    {
        return $this->hasMany(CalendarEvent::class);
    }

    public function calendarPrograms(): HasMany
    {
        return $this->hasMany(CalendarProgram::class)->orderBy('sort_order');
    }

    public function type(): string
    {
        $slug = $this->slug;

        if (str_contains($slug, 'solo')) {
            return 'solo';
        }
        if (str_contains($slug, 'atelier')) {
            return 'atelier';
        }

        return 'group';
    }
}
