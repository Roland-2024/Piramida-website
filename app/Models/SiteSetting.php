<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedContent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SiteSetting extends Model
{
    use HasLocalizedContent;

    protected $fillable = [
        'notification_email',
        'email',
        'phone',
        'facebook_url',
        'x_url',
        'instagram_url',
        'linkedin_url',
        'map_url',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(SiteSettingTranslation::class);
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1]);
    }
}
