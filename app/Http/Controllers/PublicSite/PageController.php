<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Page;
use App\Models\Program;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(string $locale, string $slug): View
    {
        $page = Page::query()
            ->published()
            ->whereHas('translations', fn (Builder $query) => $query
                ->where('locale', $locale)
                ->where('slug', $slug))
            ->with(['translations', 'featuredMedia'])
            ->firstOrFail();

        if (! $page->carouselSlug()) {
            $page->load(['sections' => fn (HasMany $query) => $query->active()
                ->with(['translations', 'primaryMedia', 'secondaryMedia', 'gallery'])]);
        }

        // Draft template mapping; keep other CMS pages on the generic section renderer.
        $view = match (true) {
            $page->translation('en', false)?->slug === 'about-us' => 'public.pages.about',
            $page->carouselSlug() !== null => 'public.pages.education',
            default => 'public.pages.show',
        };

        return view($view, [
            'page' => $page,
            'translation' => $page->translation($locale, false),
            'languageUrls' => $this->languageUrls($page),
            'slides' => $page->carouselSlug() === 'business'
                ? Business::query()->published()
                    ->whereHas('translations', fn (Builder $query) => $query->where('locale', $locale))
                    ->with(['translations', 'featuredMedia'])
                    ->orderBy('display_order')->orderBy('id')->get()
                    ->map(fn (Business $business) => [
                        'url' => $business->featuredMedia?->url(),
                        'title' => $business->translation($locale, false)->name,
                        'description' => strip_tags($business->translation($locale, false)->description ?? ''),
                    ])
                : ($page->carouselSlug() !== null
                    ? Program::query()->published()
                        ->where('category', $page->translation('en', false)->slug === 'art' ? 'art_culture' : $page->translation('en', false)->slug)
                        ->whereHas('translations', fn (Builder $query) => $query->where('locale', $locale))
                        ->with(['translations', 'featuredMedia'])->orderBy('display_order')->orderBy('id')->get()
                        ->map(fn (Program $program) => [
                            'url' => $program->featuredMedia?->url(),
                            'title' => $program->translation($locale, false)->title,
                            'description' => strip_tags($program->translation($locale, false)->description ?? ''),
                        ])
                    : collect()),
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function languageUrls(Page $page): array
    {
        return collect(config('cms.locales'))
            ->mapWithKeys(function (string $name, string $locale) use ($page): array {
                $translation = $page->translation($locale, false);

                return [
                    $locale => $translation
                        ? route('public.pages.show', [$locale, $translation->slug])
                        : route('public.home', $locale),
                ];
            })
            ->all();
    }
}
