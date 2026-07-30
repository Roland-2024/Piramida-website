<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;

trait HasLocalizedContent
{
    public function translation(?string $locale = null, bool $fallback = true): ?Model
    {
        $this->loadMissing('translations');
        $locale ??= app()->getLocale();

        $translation = $this->translations->firstWhere('locale', $locale);

        if ($translation || ! $fallback) {
            return $translation;
        }

        return $this->translations->firstWhere('locale', config('cms.fallback_locale'));
    }

    /**
     * @param  array<string, array<string, mixed>>  $translations
     */
    public function syncTranslations(array $translations): void
    {
        foreach ($translations as $locale => $values) {
            $this->translations()->updateOrCreate(
                ['locale' => $locale],
                $values,
            );
        }

        $this->unsetRelation('translations');
    }
}
