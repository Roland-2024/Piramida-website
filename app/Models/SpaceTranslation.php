<?php

namespace App\Models;

use App\Casts\SanitizedHtml;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpaceTranslation extends Model
{
    protected $fillable = [
        'locale',
        'title',
        'slug',
        'short_description',
        'description',
        'features',
        'location',
        'price_label',
        'seo_title',
        'seo_description',
    ];

    public function space(): BelongsTo
    {
        return $this->belongsTo(Space::class);
    }

    protected function casts(): array
    {
        return [
            'description' => SanitizedHtml::class,
            'features' => SanitizedHtml::class,
        ];
    }
}
