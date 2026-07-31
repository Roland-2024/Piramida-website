<?php

namespace App\Http\Controllers\PublicSite;

use App\Models\Attraction;

class AttractionController extends TranslatedCatalogController
{
    protected string $modelClass = Attraction::class;

    protected string $routePrefix = 'public.attractions';

    protected string $plural = 'Attractions';

    protected array $with = ['translations', 'featuredMedia', 'gallery'];
}
