<?php

namespace App\Http\Controllers\PublicSite;

use App\Models\Page;
use App\Support\Seo;
use App\Support\WebsiteContent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class LlmsController
{
    public function __invoke(): Response
    {
        app(WebsiteContent::class)->loadTranslations();
        $text = static fn (string $value): string => str_replace(
            ['\\', '[', ']'], ['\\\\', '\\[', '\\]'],
            Str::squish(strip_tags(html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'))),
        );
        $lines = ['# '.$text(__('seo.name', [], 'al')), '', '> '.$text(__('seo.description', [], 'en')), ''];
        $pages = Page::query()->published()->where('is_homepage', false)
            ->whereHas('translations', fn (Builder $query) => $query->where('locale', 'en')
                ->whereIn('slug', [...Page::CAROUSEL_SLUGS, 'about-us']))
            ->with('translations')->orderBy('display_order')->orderBy('id')->get();

        foreach (config('cms.locales') as $locale => $name) {
            $lines[] = '## '.$text($name);
            $lines[] = '';
            $defaults = trans('seo.pages', [], $locale);
            foreach (Seo::INDEX_ROUTES as $route) {
                $lines[] = '- ['.$text($defaults[$route][0]).']('.Seo::url(route('public.'.$route, $locale)).'): '.$text($defaults[$route][1]);
            }
            foreach ($pages as $page) {
                if ($translation = $page->translation($locale, false)) {
                    $lines[] = '- ['.$text($translation->title).']('.Seo::recordUrl($page, $locale, $translation->slug).')';
                }
            }
            $lines[] = '';
        }
        $lines[] = '## Optional';
        $lines[] = '';
        $lines[] = '- [XML sitemap]('.Seo::url('/sitemap.xml').'): All public URLs and their available language variants.';

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
