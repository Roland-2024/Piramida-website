<?php

namespace App\Models;

use App\Casts\SanitizedHtml;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventTranslation extends Model
{
    protected $fillable = [
        'locale',
        'wordpress_id',
        'title',
        'slug',
        'short_description',
        'description',
        'location',
        'street_address',
        'address_locality',
        'postal_code',
        'address_country',
        'price_label',
        'seo_title',
        'seo_description',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    protected function casts(): array
    {
        return [
            'wordpress_id' => 'integer',
            'description' => SanitizedHtml::class,
        ];
    }
}
