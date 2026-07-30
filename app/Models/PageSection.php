<?php

namespace App\Models;

use App\Enums\SectionType;
use App\Models\Concerns\HasLocalizedContent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PageSection extends Model
{
    use HasFactory, HasLocalizedContent, SoftDeletes;

    protected $fillable = [
        'page_id',
        'internal_name',
        'type',
        'primary_media_id',
        'secondary_media_id',
        'primary_button_url',
        'secondary_button_url',
        'display_order',
        'is_active',
        'structured_data',
        'created_by',
        'updated_by',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(PageSectionTranslation::class);
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function primaryMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'primary_media_id');
    }

    public function secondaryMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'secondary_media_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    protected function casts(): array
    {
        return [
            'type' => SectionType::class,
            'display_order' => 'integer',
            'is_active' => 'boolean',
            'structured_data' => 'array',
        ];
    }
}
