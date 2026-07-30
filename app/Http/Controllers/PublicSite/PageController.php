<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\Page;
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
            ->with([
                'translations',
                'featuredMedia',
                'sections' => fn (HasMany $query) => $query
                    ->active()
                    ->with(['translations', 'primaryMedia', 'secondaryMedia']),
            ])
            ->firstOrFail();

        return view('public.pages.show', [
            'page' => $page,
            'translation' => $page->translation($locale, false),
            'languageUrls' => $this->languageUrls($page),
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
