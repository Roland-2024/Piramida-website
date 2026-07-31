<?php

namespace App\Models;

use App\Enums\BusinessCategory;
use App\Enums\ContentStatus;
use App\Models\Concerns\HasLocalizedContent;
use App\Models\Concerns\HasMediaGallery;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Business extends Model
{
    use HasFactory, HasLocalizedContent, HasMediaGallery, SoftDeletes;

    protected $fillable = [
        'featured_media_id',
        'category',
        'status',
        'published_at',
        'website_url',
        'email',
        'phone',
        'is_featured',
        'display_order',
        'created_by',
        'updated_by',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(BusinessTranslation::class);
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
            'category' => BusinessCategory::class,
            'status' => ContentStatus::class,
            'published_at' => 'datetime',
            'is_featured' => 'boolean',
            'display_order' => 'integer',
        ];
    }
}
