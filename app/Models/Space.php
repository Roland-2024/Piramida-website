<?php

namespace App\Models;

use App\Enums\BookingMode;
use App\Enums\ContentStatus;
use App\Enums\SpaceType;
use App\Models\Concerns\HasLocalizedContent;
use App\Models\Concerns\HasMediaGallery;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Space extends Model
{
    use HasFactory, HasLocalizedContent, HasMediaGallery, SoftDeletes;

    protected $fillable = [
        'featured_media_id',
        'type',
        'status',
        'published_at',
        'capacity',
        'area_sqm',
        'price_from',
        'currency',
        'booking_mode',
        'external_url',
        'is_featured',
        'display_order',
        'created_by',
        'updated_by',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(SpaceTranslation::class);
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

    protected function casts(): array
    {
        return [
            'type' => SpaceType::class,
            'status' => ContentStatus::class,
            'booking_mode' => BookingMode::class,
            'published_at' => 'datetime',
            'capacity' => 'integer',
            'area_sqm' => 'decimal:2',
            'price_from' => 'decimal:2',
            'is_featured' => 'boolean',
            'display_order' => 'integer',
        ];
    }
}
