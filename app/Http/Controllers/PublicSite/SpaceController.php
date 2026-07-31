<?php

namespace App\Http\Controllers\PublicSite;

use App\Models\Space;

class SpaceController extends TranslatedCatalogController
{
    protected string $modelClass = Space::class;

    protected string $routePrefix = 'public.spaces';

    protected string $plural = 'Spaces';

    protected array $with = ['translations', 'featuredMedia', 'gallery'];
}
