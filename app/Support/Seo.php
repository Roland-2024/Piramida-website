<?php

namespace App\Support;

use App\Models\Attraction;
use App\Models\Business;
use App\Models\Career;
use App\Models\Event;
use App\Models\News;
use App\Models\Page;
use App\Models\Space;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Seo
{
    public const MODELS = [Page::class => 'pages', News::class => 'news', Event::class => 'events', Attraction::class => 'attractions', Business::class => 'businesses', Space::class => 'spaces', Career::class => 'careers'];

    public static function url(string $url): string
    {
        return rtrim(config('seo.url'), '/').(parse_url($url, PHP_URL_PATH) ?: '/');
    }

    public static function recordUrl(Model $record, string $locale, string $slug): string
    {
        return self::url($record instanceof Page && $record->is_homepage
            ? route('public.home', $locale)
            : route('public.'.self::MODELS[$record::class].'.show', [$locale, $slug]));
    }

    public static function metadata(?Model $record, ?string $title, ?string $description, array $languageUrls): array
    {
        $locale = app()->getLocale();
        $language = $locale === 'al' ? 'sq' : 'en';
        $route = Str::after(request()->route()?->getName() ?? '', 'public.');
        $translation = $record?->translation($locale, false);
        $key = $record instanceof Page ? ($record->is_homepage ? 'home' : $record->translation('en', false)?->slug) : $route;
        $defaults = trans('seo.pages');
        $default = $defaults[$key ?? ''] ?? null;
        $title = self::text($translation?->seo_title ?: ($default[0] ?? $title) ?: __('seo.name'));
        $description = self::text($translation?->seo_description ?: ($default[1] ?? $description) ?: $translation?->description ?: $translation?->content ?: __('seo.description'));
        $description = Str::limit($description, 160);
        $query = [];
        if ($route === 'events.index' && request('period') === 'past') {
            $query['period'] = 'past';
            $title = __('seo.past_events');
        }
        if (str_ends_with($route, '.index') && filter_var(request('page'), FILTER_VALIDATE_INT) > 1) {
            $query['page'] = (int) request('page');
            $title .= ' — '.__('seo.page').' '.$query['page'];
        }
        $suffix = $query ? '?'.http_build_query($query) : '';
        $canonical = ($record && $translation ? self::recordUrl($record, $locale, $translation->slug) : self::url(request()->url())).$suffix;
        $alternates = [];
        if ($record) {
            foreach ($record->translations as $variant) {
                if (isset(config('cms.locales')[$variant->locale])) {
                    $alternates[$variant->locale === 'al' ? 'sq' : 'en'] = self::recordUrl($record, $variant->locale, $variant->slug).$suffix;
                }
            }
        } else {
            foreach ($languageUrls as $variant => $url) {
                $alternates[$variant === 'al' ? 'sq' : 'en'] = self::url($url).$suffix;
            }
        }
        $media = $record && $record->relationLoaded('featuredMedia') ? $record->featuredMedia : null;
        $image = $media && str_starts_with($media->mime_type, 'image/') ? $media->url() : WebsiteContent::image('template/images/piramida_block_1.jpg');
        // Keep external CDN URLs; normalize local storage URLs to the production domain.
        $host = parse_url($image, PHP_URL_HOST);
        if (! $host || in_array($host, [request()->getHost(), parse_url(config('app.url'), PHP_URL_HOST)], true)) {
            $image = self::url($image);
        }
        $image = str_replace(' ', '%20', $image);
        $brand = __('seo.name');
        $title = str_contains(mb_strtolower($title), 'piramid') || str_contains(mb_strtolower($title), 'pyramid') ? $title : $title.' | '.$brand;
        $schema = [
            '@type' => 'WebPage', '@id' => $canonical.'#webpage',
            'url' => $canonical, 'name' => $title, 'description' => $description, 'inLanguage' => $language,
            'isPartOf' => ['@id' => self::url('/').'#website'],
        ];
        if ($record instanceof News) {
            $schema['@type'] = 'NewsArticle';
            $schema['headline'] = $translation->title;
            $schema['image'] = [$image];
            $schema['datePublished'] = $record->published_at->toIso8601String();
            $schema['dateModified'] = max($record->updated_at, $translation->updated_at)->toIso8601String();
            $schema['publisher'] = ['@type' => 'Organization', 'name' => $brand, 'url' => self::url('/')];
        }
        $graph = [$schema];
        if ($record instanceof Event && $translation) {
            $graph[0]['mainEntity'] = ['@id' => $canonical.'#event'];
            $graph[] = array_filter([
                '@type' => 'Event', '@id' => $canonical.'#event', 'url' => $canonical,
                'name' => $translation->title, 'description' => $description, 'image' => [$image],
                'startDate' => $record->starts_at?->toIso8601String(),
                'endDate' => $record->ends_at?->toIso8601String(),
                'location' => $translation->location ? array_filter([
                    '@type' => 'Place', 'name' => $translation->location,
                    'address' => $translation->street_address && $translation->address_locality && $translation->address_country ? array_filter([
                        '@type' => 'PostalAddress', 'streetAddress' => $translation->street_address,
                        'addressLocality' => $translation->address_locality, 'postalCode' => $translation->postal_code,
                        'addressCountry' => $translation->address_country,
                    ]) : null,
                ]) : null,
            ], fn ($value) => $value !== null);
        }
        if ($route === 'home') {
            $graph[] = ['@type' => 'WebSite', '@id' => self::url('/').'#website', 'url' => self::url('/'), 'name' => __('seo.name', [], 'al'), 'alternateName' => __('seo.name', [], 'en'), 'inLanguage' => ['sq', 'en']];
            $graph[] = ['@type' => 'Organization', '@id' => self::url('/').'#organization', 'url' => self::url('/'), 'name' => __('seo.name', [], 'al'), 'alternateName' => __('seo.name', [], 'en')];
        }

        return compact('title', 'description', 'canonical', 'alternates', 'image', 'language', 'graph') + [
            'type' => $record instanceof News ? 'article' : 'website',
            'imageAlt' => $media?->{'alt_text_'.$locale} ?: $brand,
        ];
    }

    private static function text(?string $value): string
    {
        return Str::squish(html_entity_decode(strip_tags($value ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }
}
