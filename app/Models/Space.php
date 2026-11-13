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
        'floor_plan_media_id',
        'type',
        'leasing_unit_id',
        'is_available',
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

    public function leasingUnit(): BelongsTo
    {
        return $this->belongsTo(LeasingUnit::class);
    }

    public function leasingMinimumOffer(): ?string
    {
        if ($this->currency === 'ALL' && (float) $this->price_from > 0) {
            return $this->price_from;
        }

        $minimum = config('leasing.minimum_prices_all')[$this->leasingUnit?->code] ?? null;

        return $minimum === null ? null : number_format($minimum, 2, '.', '');
    }

    public function scopePubliclyAccessible(Builder $query): Builder
    {
        return $query->where(fn (Builder $query) => $query
            ->where('type', SpaceType::EventSpace)
            ->orWhere(fn (Builder $query) => $query->where('type', SpaceType::Leasing)
                ->where('is_available', true)->whereHas('leasingUnit')));
    }

    public function translations(): HasMany
    {
        return $this->hasMany(SpaceTranslation::class);
    }

    public function featuredMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'featured_media_id');
    }

    public function floorPlan(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'floor_plan_media_id')
            ->where('disk', 'public')->where('mime_type', 'application/pdf');
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
            'is_available' => 'boolean',
            'display_order' => 'integer',
        ];
    }
}
