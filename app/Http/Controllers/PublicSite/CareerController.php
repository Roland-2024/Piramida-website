<?php

namespace App\Http\Controllers\PublicSite;

use App\Models\Career;
use Illuminate\Database\Eloquent\Builder;

class CareerController extends TranslatedCatalogController
{
    protected string $indexView = 'public.careers.index';

    protected string $showView = 'public.careers.show';

    protected string $modelClass = Career::class;

    protected string $routePrefix = 'public.careers';

    protected string $plural = 'Careers';

    protected array $with = ['translations'];

    protected function indexQuery(Builder $query): Builder
    {
        return $query->open();
    }

    protected function showQuery(Builder $query): Builder
    {
        return $query->open();
    }
}
