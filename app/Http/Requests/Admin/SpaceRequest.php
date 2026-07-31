<?php

namespace App\Http\Requests\Admin;

use App\Enums\BookingMode;
use App\Enums\SpaceType;
use App\Models\Space;
use Illuminate\Validation\Rule;

class SpaceRequest extends TranslatedContentRequest
{
    protected string $modelClass = Space::class;

    protected string $routeParameter = 'space';

    public function rules(): array
    {
        return $this->contentRules('space_translations', [
            'type' => ['required', Rule::enum(SpaceType::class)],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'area_sqm' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'price_from' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'currency' => ['required', 'string', 'size:3'],
            'booking_mode' => ['required', Rule::enum(BookingMode::class)],
            'external_url' => [
                Rule::requiredIf(in_array($this->input('booking_mode'), ['external', 'both'], true)),
                'nullable',
                'url:http,https',
                'max:2048',
            ],
            'is_featured' => ['required', 'boolean'],
        ], [
            'short_description' => ['nullable', 'string', 'max:2000'],
            'description' => ['nullable', 'string'],
            'features' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'price_label' => ['nullable', 'string', 'max:255'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
