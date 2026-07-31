<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;

abstract class TranslatedCatalogController extends Controller
{
    /** @var class-string<Model> */
    protected string $modelClass;

    protected string $routePrefix;

    protected string $plural;

    protected string $translationTitleColumn = 'title';

    /** @var array<int, string> */
    protected array $with = ['translations', 'featuredMedia'];

    public function index(string $locale): View
    {
        $modelClass = $this->modelClass;

        return view('public.catalog.index', [
            'items' => $this->indexQuery($modelClass::query())
                ->published()
                ->whereHas('translations', fn (Builder $query) => $query->where('locale', $locale))
                ->with($this->with)
                ->orderBy('display_order')
                ->orderBy('id')
                ->paginate(12),
            'plural' => $this->plural,
            'routePrefix' => $this->routePrefix,
            'translationTitleColumn' => $this->translationTitleColumn,
            'languageUrls' => $this->indexLanguageUrls(),
        ]);
    }

    public function show(string $locale, string $slug): View
    {
        $modelClass = $this->modelClass;
        $item = $this->showQuery($modelClass::query())
            ->published()
            ->whereHas('translations', fn (Builder $query) => $query
                ->where('locale', $locale)
                ->where('slug', $slug))
            ->with($this->with)
            ->firstOrFail();

        return view('public.catalog.show', [
            'item' => $item,
            'translation' => $item->translation($locale, false),
            'routePrefix' => $this->routePrefix,
            'translationTitleColumn' => $this->translationTitleColumn,
            'languageUrls' => $this->showLanguageUrls($item),
        ]);
    }

    protected function indexQuery(Builder $query): Builder
    {
        return $query;
    }

    protected function showQuery(Builder $query): Builder
    {
        return $query;
    }

    /**
     * @return array<string, string>
     */
    private function indexLanguageUrls(): array
    {
        return collect(config('cms.locales'))
            ->mapWithKeys(fn (string $name, string $locale) => [
                $locale => route("{$this->routePrefix}.index", $locale),
            ])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    private function showLanguageUrls(Model $item): array
    {
        return collect(config('cms.locales'))
            ->mapWithKeys(function (string $name, string $locale) use ($item): array {
                $translation = $item->translation($locale, false);

                return [
                    $locale => $translation
                        ? route("{$this->routePrefix}.show", [$locale, $translation->slug])
                        : route("{$this->routePrefix}.index", $locale),
                ];
            })
            ->all();
    }
}
