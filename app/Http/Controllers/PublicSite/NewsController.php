<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(): View
    {
        return view('public.news.index', [
            'articles' => News::query()
                ->published()
                ->whereHas('translations', fn (Builder $query) => $query->where('locale', app()->getLocale()))
                ->with(['translations', 'featuredMedia'])
                ->latest('published_at')
                ->paginate(9),
            'languageUrls' => $this->indexLanguageUrls(),
        ]);
    }

    public function show(string $locale, string $slug): View
    {
        $article = News::query()
            ->published()
            ->whereHas('translations', fn (Builder $query) => $query
                ->where('locale', $locale)
                ->where('slug', $slug))
            ->with(['translations', 'featuredMedia', 'gallery'])
            ->firstOrFail();

        return view('public.news.show', [
            'article' => $article,
            'relatedNews' => News::query()->published()->whereKeyNot($article->id)
                ->whereHas('translations', fn (Builder $query) => $query->where('locale', $locale))
                ->with(['translations', 'featuredMedia'])->latest('published_at')->orderByDesc('id')->limit(3)->get(),
            'translation' => $article->translation($locale, false),
            'languageUrls' => collect(config('cms.locales'))
                ->mapWithKeys(function (string $name, string $targetLocale) use ($article): array {
                    $translation = $article->translation($targetLocale, false);

                    return [
                        $targetLocale => $translation
                            ? route('public.news.show', [$targetLocale, $translation->slug])
                            : route('public.news.index', $targetLocale),
                    ];
                })
                ->all(),
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function indexLanguageUrls(): array
    {
        return collect(config('cms.locales'))
            ->mapWithKeys(fn (string $name, string $locale) => [
                $locale => route('public.news.index', $locale),
            ])
            ->all();
    }
}
