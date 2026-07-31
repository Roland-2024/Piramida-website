<?php

namespace App\Http\Controllers\PublicSite;

use App\Models\Business;

class BusinessController extends TranslatedCatalogController
{
    protected string $modelClass = Business::class;

    protected string $routePrefix = 'public.businesses';

    protected string $plural = 'Businesses';

    protected string $translationTitleColumn = 'name';

    protected array $with = ['translations', 'featuredMedia', 'gallery'];
}
