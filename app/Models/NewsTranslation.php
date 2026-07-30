<?php

namespace App\Models;

use App\Casts\SanitizedHtml;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsTranslation extends Model
{
    protected $fillable = [
        'locale',
        'title',
        'slug',
        'excerpt',
        'content',
        'seo_title',
        'seo_description',
    ];

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }

    protected function casts(): array
    {
        return ['content' => SanitizedHtml::class];
    }
}
