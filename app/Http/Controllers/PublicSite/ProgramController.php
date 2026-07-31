<?php

namespace App\Http\Controllers\PublicSite;

use App\Models\Program;

class ProgramController extends TranslatedCatalogController
{
    protected string $modelClass = Program::class;

    protected string $routePrefix = 'public.programs';

    protected string $plural = 'Programs';

    protected array $with = ['translations', 'featuredMedia', 'gallery'];
}
