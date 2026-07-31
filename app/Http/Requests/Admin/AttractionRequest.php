<?php

namespace App\Http\Requests\Admin;

use App\Models\Attraction;

class AttractionRequest extends TranslatedContentRequest
{
    protected string $modelClass = Attraction::class;

    protected string $routeParameter = 'attraction';

    public function rules(): array
    {
        return $this->contentRules('attraction_translations', [
            'is_featured' => ['required', 'boolean'],
        ], [
            'short_description' => ['nullable', 'string', 'max:2000'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'visitor_information' => ['nullable', 'string', 'max:5000'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
