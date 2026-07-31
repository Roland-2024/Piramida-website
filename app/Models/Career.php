<?php

namespace App\Models;

use App\Enums\BookingMode;
use App\Enums\ContentStatus;
use App\Enums\EmploymentType;
use App\Models\Concerns\HasLocalizedContent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Career extends Model
{
    use HasFactory, HasLocalizedContent, SoftDeletes;

    protected $fillable = [
        'employment_type',
        'status',
        'published_at',
        'deadline',
        'booking_mode',
        'external_url',
        'display_order',
        'created_by',
        'updated_by',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(CareerTranslation::class);
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

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where(fn (Builder $query) => $query
            ->whereNull('deadline')
            ->orWhere('deadline', '>=', now()));
    }

    protected function casts(): array
    {
        return [
            'employment_type' => EmploymentType::class,
            'status' => ContentStatus::class,
            'booking_mode' => BookingMode::class,
            'published_at' => 'datetime',
            'deadline' => 'datetime',
            'display_order' => 'integer',
        ];
    }
}
