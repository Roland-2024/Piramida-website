<?php

namespace App\Models;

use App\Casts\SanitizedHtml;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessTranslation extends Model
{
    protected $fillable = [
        'locale',
        'name',
        'slug',
        'short_description',
        'description',
        'address',
        'opening_hours',
        'seo_title',
        'seo_description',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    protected function casts(): array
    {
        return ['description' => SanitizedHtml::class];
    }
}
