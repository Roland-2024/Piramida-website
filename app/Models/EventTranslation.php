<?php

namespace App\Models;

use App\Casts\SanitizedHtml;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventTranslation extends Model
{
    protected $fillable = [
        'locale',
        'title',
        'slug',
        'short_description',
        'description',
        'location',
        'seo_title',
        'seo_description',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    protected function casts(): array
    {
        return ['description' => SanitizedHtml::class];
    }
}
