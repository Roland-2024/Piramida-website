<?php

namespace App\Http\Controllers\PublicSite;

use App\Models\Career;
use App\Models\LeasingUnit;
use App\Models\Page;
use App\Models\Space;
use App\Support\Seo;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SitemapController
{
    public function __invoke(): StreamedResponse
    {
        return response()->stream(function () {
            echo '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            $write = static function (string $url): void {
                echo '<url><loc>'.htmlspecialchars($url, ENT_XML1 | ENT_QUOTES, 'UTF-8').'</loc></url>';
            };
            foreach (array_keys(config('cms.locales')) as $locale) {
                foreach (['home', 'news.index', 'events.index', 'attractions.index', 'businesses.index', 'leasing.index', 'spaces.index', 'spaces.overview', 'careers.index', 'contact'] as $route) {
                    $write(Seo::url(route('public.'.$route, $locale)));
                }
                foreach (array_keys(LeasingUnit::FLOORS) as $floor) {
                    $write(Seo::url(route('public.leasing.floor', [$locale, $floor])));
                }
            }
            foreach (array_keys(Seo::MODELS) as $model) {
                $query = $model::query()->published()->with('translations');
                if ($model === Space::class) {
                    $query->publiclyAccessible();
                }
                if ($model === Career::class) {
                    $query->open();
                }
                if ($model === Page::class) {
                    $query->where('is_homepage', false);
                }
                $query->chunkById(200, function ($records) use ($write) {
                    foreach ($records as $record) {
                        foreach ($record->translations as $translation) {
                            if (isset(config('cms.locales')[$translation->locale])) {
                                $write(Seo::recordUrl($record, $translation->locale, $translation->slug));
                            }
                        }
                    }
                });
            }
            echo '</urlset>';
        }, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
