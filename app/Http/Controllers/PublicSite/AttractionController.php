<?php

namespace App\Http\Controllers\PublicSite;

use App\Models\Attraction;
use App\Models\Business;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class AttractionController extends TranslatedCatalogController
{
    protected string $indexView = 'public.attractions.index';

    public function index(string $locale): View
    {
        return parent::index($locale)->with('businesses', Business::query()->published()
            ->whereHas('translations', fn (Builder $query) => $query->where('locale', $locale))
            ->with(['translations', 'featuredMedia', 'logoMedia', 'gallery'])
            ->orderBy('display_order')->orderBy('id')->limit(12)->get());
    }

    protected string $modelClass = Attraction::class;

    protected string $routePrefix = 'public.attractions';

    protected string $plural = 'Attractions';

    protected array $with = ['translations', 'featuredMedia', 'gallery'];
}
