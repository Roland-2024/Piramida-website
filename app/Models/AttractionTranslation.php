<?php

namespace App\Models;

use App\Casts\SanitizedHtml;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttractionTranslation extends Model
{
    protected $fillable = [
        'locale',
        'title',
        'slug',
        'short_description',
        'description',
        'location',
        'visitor_information',
        'seo_title',
        'seo_description',
    ];

    public function attraction(): BelongsTo
    {
        return $this->belongsTo(Attraction::class);
    }

    protected function casts(): array
    {
        return ['description' => SanitizedHtml::class];
    }
}
