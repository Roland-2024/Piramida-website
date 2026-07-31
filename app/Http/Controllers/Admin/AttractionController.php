<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\AttractionRequest;
use App\Models\Attraction;
use Illuminate\Http\RedirectResponse;

class AttractionController extends TranslatedContentController
{
    protected string $modelClass = Attraction::class;

    protected string $routeParameter = 'attraction';

    protected string $routePrefix = 'admin.attractions';

    protected string $singular = 'Attraction';

    protected string $plural = 'Attractions';

    public function store(AttractionRequest $request): RedirectResponse
    {
        return $this->storeContent($request);
    }

    public function update(AttractionRequest $request): RedirectResponse
    {
        return $this->updateContent($request);
    }

    protected function globalFields(): array
    {
        return [
            ['name' => 'is_featured', 'label' => 'Featured', 'type' => 'checkbox'],
        ];
    }

    protected function translationFields(): array
    {
        return [
            ['name' => 'short_description', 'label' => 'Short description', 'type' => 'textarea'],
            ['name' => 'description', 'label' => 'Description', 'type' => 'richtext'],
            ['name' => 'location', 'label' => 'Location', 'type' => 'text'],
            ['name' => 'visitor_information', 'label' => 'Visitor information', 'type' => 'textarea'],
            ['name' => 'seo_title', 'label' => 'SEO title', 'type' => 'text'],
            ['name' => 'seo_description', 'label' => 'SEO description', 'type' => 'textarea'],
        ];
    }
}
