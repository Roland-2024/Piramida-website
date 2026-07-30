<?php

namespace App\Models;

use App\Casts\SanitizedHtml;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageSectionTranslation extends Model
{
    protected $fillable = [
        'locale',
        'title',
        'subtitle',
        'description',
        'primary_button_label',
        'secondary_button_label',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(PageSection::class, 'page_section_id');
    }

    protected function casts(): array
    {
        return ['description' => SanitizedHtml::class];
    }
}
