<?php

namespace App\Http\Controllers\PublicSite;

use App\Enums\SpaceType;
use App\Models\Space;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SpaceController extends TranslatedCatalogController
{
    protected string $modelClass = Space::class;

    protected string $routePrefix = 'public.spaces';

    protected string $plural = 'Spaces';

    protected string $showView = 'public.spaces.show';

    protected array $with = ['translations', 'featuredMedia', 'gallery'];

    public function overview(): View
    {
        return view('public.spaces.overview', [
            'languageUrls' => collect(config('cms.locales'))
                ->mapWithKeys(fn (string $name, string $locale) => [$locale => route('public.spaces.overview', $locale)])
                ->all(),
        ]);
    }

    public function index(string $locale): View
    {
        $filters = request()->validate([
            'type' => ['nullable', Rule::enum(SpaceType::class)],
        ]);
        $type = SpaceType::tryFrom($filters['type'] ?? '') ?? SpaceType::EventSpace;

        return view('public.spaces.index', [
            'items' => Space::query()
                ->published()
                ->where('type', $type)
                ->whereHas('translations', fn (Builder $query) => $query->where('locale', $locale))
                ->with($this->with)
                ->orderBy('display_order')
                ->orderBy('id')
                ->paginate(12)
                ->withQueryString(),
            'spaceType' => $type,
            'languageUrls' => collect(config('cms.locales'))
                ->mapWithKeys(fn (string $name, string $targetLocale) => [
                    $targetLocale => route('public.spaces.index', [$targetLocale, 'type' => $type->value]),
                ])
                ->all(),
        ]);
    }
}
