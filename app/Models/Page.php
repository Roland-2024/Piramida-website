<?php

namespace App\Models;

use App\Enums\ContentStatus;
use App\Models\Concerns\HasLocalizedContent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasFactory, HasLocalizedContent, SoftDeletes;

    public const CAROUSEL_SLUGS = ['education', 'innovation', 'business', 'art'];

    public function carouselSlug(): ?string
    {
        $slug = $this->translation('en', false)?->slug;

        return in_array($slug, self::CAROUSEL_SLUGS, true) ? $slug : null;
    }

    public function hasTemplateSlug(): bool
    {
        return $this->carouselSlug() !== null || $this->translation('en', false)?->slug === 'about-us';
    }

    public function scopeWithoutCarousel(Builder $query): Builder
    {
        return $query->whereDoesntHave('translations', fn (Builder $query) => $query
            ->where('locale', 'en')->whereIn('slug', self::CAROUSEL_SLUGS));
    }

    protected $fillable = [
        'featured_media_id',
        'is_homepage',
        'homepage_video_url',
        'status',
        'published_at',
        'display_order',
        'created_by',
        'updated_by',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(PageTranslation::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(PageSection::class)->orderBy('display_order')->orderBy('id');
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
            'status' => ContentStatus::class,
            'is_homepage' => 'boolean',
            'published_at' => 'datetime',
            'display_order' => 'integer',
        ];
    }
}
