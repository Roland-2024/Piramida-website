<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteSettingTranslation extends Model
{
    protected $fillable = [
        'locale',
        'address',
        'opening_hours',
        'footer_text',
    ];

    public function siteSetting(): BelongsTo
    {
        return $this->belongsTo(SiteSetting::class);
    }
}
