<?php

namespace App\Support;

use App\Models\Media;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class WebsiteContent
{
    private ?array $images = null;

    public static function defaults(string $locale): array
    {
        $lines = [];
        foreach (['cms', 'website', 'seo', 'validation', 'pagination'] as $group) {
            $defaults = app('translation.loader')->load('en', $group);
            $localized = app('translation.loader')->load($locale, $group);
            foreach (Arr::dot(array_replace_recursive($defaults, $localized)) as $key => $value) {
                if (is_string($value)) {
                    $lines[$group.'.'.$key] = $value;
                }
            }
        }

        return $lines;
    }

    public function loadTranslations(): void
    {
        $overrides = DB::table('website_texts')->get()->groupBy('locale');
        foreach (array_keys(config('cms.locales')) as $locale) {
            $texts = ($overrides->get($locale) ?? collect())->pluck('text', 'key')->all();
            foreach (['cms', 'website', 'seo', 'validation', 'pagination'] as $group) {
                app('translator')->get($group, [], $locale);
                $lines = array_replace_recursive(app('translation.loader')->load('en', $group), app('translation.loader')->load($locale, $group));
                $replace = function (array $items, string $prefix) use (&$replace, $texts): array {
                    foreach ($items as $key => $value) {
                        $path = $prefix.'.'.$key;
                        $items[$key] = is_array($value) ? $replace($value, $path) : ($texts[$path] ?? $value);
                    }

                    return $items;
                };
                // Preserve literal dotted keys such as SEO route names inside arrays.
                foreach ($replace($lines, $group) as $key => $value) {
                    app('translator')->addLines([$group.'.'.$key => $value], $locale);
                }
            }
        }
    }

    public static function image(string $path, bool $optimized = true): string
    {
        if (! $optimized) {
            return (app(self::class)->images()[sha1($path)] ?? null)?->url() ?? asset($path);
        }

        $fallback = preg_match('~^template/images/leasing/(kati-0|kati-1|kati-3|kati-4|outdoor)\.png$~', $path)
            || in_array($path, config('template_photos'), true)
            ? substr($path, 0, strrpos($path, '.')).'.webp' : $path;

        return (app(self::class)->images()[sha1($path)] ?? null)?->displayUrl() ?? asset($fallback);
    }

    public static function imageSrcset(string $path): ?string
    {
        if ($media = app(self::class)->images()[sha1($path)] ?? null) {
            return $media->srcset();
        }

        return $path === 'template/images/piramida-hero-1920.jpg'
            ? asset('template/images/piramida-hero-960.jpg').' 960w, '.asset($path).' 1920w'
            : null;
    }

    private function images(): array
    {
        if ($this->images === null) {
            $references = DB::table('website_images')->pluck('media_id', 'key');
            $media = Media::whereIn('id', $references->values())->get()->keyBy('id');
            $this->images = [];
            foreach ($references as $key => $id) {
                if ($item = $media->get($id)) {
                    $this->images[$key] = $item;
                }
            }
        }

        return $this->images;
    }
}
