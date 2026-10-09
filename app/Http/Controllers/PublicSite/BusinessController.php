<?php

namespace App\Http\Controllers\PublicSite;

use App\Enums\BusinessCategory;
use App\Models\Business;
use Illuminate\Database\Eloquent\Builder;

class BusinessController extends TranslatedCatalogController
{
    protected string $modelClass = Business::class;

    protected string $routePrefix = 'public.businesses';

    protected string $plural = 'Businesses';

    protected string $translationTitleColumn = 'name';

    protected string $indexView = 'public.businesses.index';

    protected string $showView = 'public.businesses.show';

    protected array $with = ['translations', 'featuredMedia', 'logoMedia', 'gallery', 'categories'];

    protected function indexQuery(Builder $query): Builder
    {
        return $query->whereHas('categories', fn (Builder $categories) => $categories->where('slug', BusinessCategory::SocialSpaces->value));
    }
}
