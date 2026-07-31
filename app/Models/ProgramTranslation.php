<?php

namespace App\Models;

use App\Casts\SanitizedHtml;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramTranslation extends Model
{
    protected $fillable = [
        'locale',
        'title',
        'slug',
        'short_description',
        'description',
        'location',
        'schedule',
        'price_label',
        'seo_title',
        'seo_description',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    protected function casts(): array
    {
        return ['description' => SanitizedHtml::class];
    }
}
