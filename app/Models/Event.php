<?php

namespace App\Models;

use App\Enums\BookingMode;
use App\Enums\ContentStatus;
use App\Enums\EventCategory;
use App\Models\Concerns\HasLocalizedContent;
use App\Models\Concerns\HasMediaGallery;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, HasLocalizedContent, HasMediaGallery, SoftDeletes;

    protected $fillable = [
        'featured_media_id',
        'category',
        'status',
        'published_at',
        'starts_at',
        'ends_at',
        'external_url',
        'booking_mode',
        'is_featured',
        'capacity',
        'display_order',
        'created_by',
        'updated_by',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(EventTranslation::class);
    }

    public function featuredMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'featured_media_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', ContentStatus::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('ends_at', '>=', now());
    }

    public function scopePast(Builder $query): Builder
    {
        return $query->where('ends_at', '<', now());
    }

    protected function casts(): array
    {
        return [
            'category' => EventCategory::class,
            'status' => ContentStatus::class,
            'booking_mode' => BookingMode::class,
            'published_at' => 'datetime',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_featured' => 'boolean',
            'capacity' => 'integer',
            'display_order' => 'integer',
        ];
    }
}
