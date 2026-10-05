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
        'postmark_enabled',
        'postmark_username',
        'postmark_password',
        'mail_from_address',
        'mail_from_name',
    ];

    protected $hidden = ['postmark_username', 'postmark_password'];

    protected function casts(): array
    {
        return [
            'postmark_enabled' => 'boolean',
            'postmark_username' => 'encrypted',
            'postmark_password' => 'encrypted',
        ];
    }

    public function translations(): HasMany
    {
        return $this->hasMany(SiteSettingTranslation::class);
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1]);
    }
}
