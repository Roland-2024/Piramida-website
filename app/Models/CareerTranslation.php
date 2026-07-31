<?php

namespace App\Models;

use App\Casts\SanitizedHtml;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CareerTranslation extends Model
{
    protected $fillable = [
        'locale',
        'title',
        'slug',
        'department',
        'location',
        'short_description',
        'description',
        'requirements',
        'seo_title',
        'seo_description',
    ];

    public function career(): BelongsTo
    {
        return $this->belongsTo(Career::class);
    }

    protected function casts(): array
    {
        return [
            'description' => SanitizedHtml::class,
            'requirements' => SanitizedHtml::class,
        ];
    }
}
