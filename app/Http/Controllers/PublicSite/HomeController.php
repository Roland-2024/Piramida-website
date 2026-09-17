<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\Attraction;
use App\Models\Business;
use App\Models\Event;
use App\Models\News;
use App\Models\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(string $locale): View
    {
        $pageQuery = Page::query()
            ->published()
            ->with([
                'translations',
                'featuredMedia',
                'sections' => fn (HasMany $query) => $query
                    ->active()
                    ->with(['translations', 'primaryMedia', 'secondaryMedia', 'gallery']),
            ])
            ->orderBy('display_order')
            ->orderBy('id');

        $page = (clone $pageQuery)->where('is_homepage', true)->first()
            ?? $pageQuery->first();

        return view('public.home', [
            'aboutUrl' => ($about = Page::query()->published()->whereHas('translations', fn (Builder $query) => $query->where('slug', 'about-us'))->with('translations')->first())?->translation($locale, false)
                ? route('public.pages.show', [$locale, $about->translation($locale, false)->slug])
                : route('public.home', $locale),
            'page' => $page,
            'latestNews' => News::query()
                ->published()
                ->whereHas('translations', fn (Builder $query) => $query->where('locale', $locale))
                ->with(['translations', 'featuredMedia'])
                ->latest('published_at')
                ->limit(3)
                ->get(),
            'upcomingEvents' => Event::query()
                ->published()
                ->upcoming()
                ->whereHas('translations', fn (Builder $query) => $query->where('locale', $locale))
                ->with(['translations', 'featuredMedia'])
                ->orderBy('starts_at')
                ->limit(3)
                ->get(),
            'featuredAttractions' => Attraction::query()
                ->published()
                ->where('is_featured', true)
                ->whereHas('translations', fn (Builder $query) => $query->where('locale', $locale))
                ->with(['translations', 'featuredMedia'])
                ->orderBy('display_order')
                ->limit(2)
                ->get(),
            'featuredBusinesses' => Business::query()
                ->published()
                ->where('is_featured', true)
                ->whereHas('translations', fn (Builder $query) => $query->where('locale', $locale))
                ->with(['translations', 'featuredMedia'])
                ->orderBy('display_order')
                ->limit(3)
                ->get(),
            'languageUrls' => $this->languageUrls(),
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function languageUrls(): array
    {
        return collect(config('cms.locales'))
            ->mapWithKeys(fn (string $name, string $locale) => [
                $locale => route('public.home', $locale),
            ])
            ->all();
    }
}
