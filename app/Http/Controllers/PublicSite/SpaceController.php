<?php

namespace App\Http\Controllers\PublicSite;

use App\Enums\SpaceType;
use App\Models\LeasingUnit;
use App\Models\Space;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SpaceController extends TranslatedCatalogController
{
    protected string $modelClass = Space::class;

    protected string $routePrefix = 'public.spaces';

    protected string $plural = 'Spaces';

    protected string $showView = 'public.spaces.show';

    protected array $with = ['translations', 'featuredMedia', 'gallery', 'leasingUnit'];

    protected function showQuery(Builder $query): Builder
    {
        return $query->publiclyAccessible();
    }

    public function leasing(string $locale): View
    {
        return view('public.leasing.index', [
            'floors' => LeasingUnit::FLOORS,
            'languageUrls' => collect(config('cms.locales'))->mapWithKeys(
                fn (string $name, string $target) => [$target => route('public.leasing.index', $target)]
            )->all(),
        ]);
    }

    public function floor(string $locale, string $floor): View
    {
        abort_unless(isset(LeasingUnit::FLOORS[$floor]), 404);
        $units = LeasingUnit::query()->where('floor', $floor)->orderBy('display_order')->get()->keyBy('code');

        return view('public.leasing.floor', [
            'floor' => $floor,
            'floorInfo' => LeasingUnit::FLOORS[$floor],
            'units' => $units,
            'availableSpaces' => Space::query()->published()->publiclyAccessible()
                ->where('type', SpaceType::Leasing)->whereIn('leasing_unit_id', $units->modelKeys())
                ->whereHas('translations', fn (Builder $query) => $query->where('locale', $locale))
                ->with('translations')->get()->keyBy('leasing_unit_id'),
            'languageUrls' => collect(config('cms.locales'))->mapWithKeys(
                fn (string $name, string $target) => [$target => route('public.leasing.floor', [$target, $floor])]
            )->all(),
        ]);
    }

    public function overview(): View
    {
        return view('public.spaces.overview', [
            'languageUrls' => collect(config('cms.locales'))
                ->mapWithKeys(fn (string $name, string $locale) => [$locale => route('public.spaces.overview', $locale)])
                ->all(),
        ]);
    }

    public function index(string $locale): View|RedirectResponse
    {
        $filters = request()->validate([
            'type' => ['nullable', Rule::enum(SpaceType::class)],
        ]);
        $type = SpaceType::tryFrom($filters['type'] ?? '') ?? SpaceType::EventSpace;

        if ($type === SpaceType::Leasing) {
            return redirect()->route('public.leasing.index', $locale, 301);
        }

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
