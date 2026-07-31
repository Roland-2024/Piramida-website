<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingMode;
use App\Enums\SpaceType;
use App\Http\Requests\Admin\SpaceRequest;
use App\Models\Space;
use Illuminate\Http\RedirectResponse;

class SpaceController extends TranslatedContentController
{
    protected string $modelClass = Space::class;

    protected string $routeParameter = 'space';

    protected string $routePrefix = 'admin.spaces';

    protected string $singular = 'Space';

    protected string $plural = 'Spaces';

    public function store(SpaceRequest $request): RedirectResponse
    {
        return $this->storeContent($request);
    }

    public function update(SpaceRequest $request): RedirectResponse
    {
        return $this->updateContent($request);
    }

    protected function globalFields(): array
    {
        return [
            ['name' => 'type', 'label' => 'Space type', 'type' => 'select', 'required' => true, 'options' => $this->options(SpaceType::cases())],
            ['name' => 'capacity', 'label' => 'Capacity', 'type' => 'number', 'min' => 1],
            ['name' => 'area_sqm', 'label' => 'Area (m²)', 'type' => 'number', 'min' => 0, 'step' => '0.01'],
            ['name' => 'price_from', 'label' => 'Price from', 'type' => 'number', 'min' => 0, 'step' => '0.01'],
            ['name' => 'currency', 'label' => 'Currency', 'type' => 'text', 'required' => true, 'default' => 'EUR'],
            ['name' => 'booking_mode', 'label' => 'Request / booking action', 'type' => 'select', 'required' => true, 'options' => $this->options(BookingMode::cases()), 'default' => 'internal'],
            ['name' => 'external_url', 'label' => 'External booking URL', 'type' => 'url'],
            ['name' => 'is_featured', 'label' => 'Featured', 'type' => 'checkbox'],
        ];
    }

    protected function translationFields(): array
    {
        return [
            ['name' => 'short_description', 'label' => 'Short description', 'type' => 'textarea'],
            ['name' => 'description', 'label' => 'Description', 'type' => 'richtext'],
            ['name' => 'features', 'label' => 'Features', 'type' => 'richtext'],
            ['name' => 'location', 'label' => 'Location', 'type' => 'text'],
            ['name' => 'price_label', 'label' => 'Price label', 'type' => 'text'],
            ['name' => 'seo_title', 'label' => 'SEO title', 'type' => 'text'],
            ['name' => 'seo_description', 'label' => 'SEO description', 'type' => 'textarea'],
        ];
    }

    private function options(array $cases): array
    {
        return collect($cases)->mapWithKeys(fn ($case) => [$case->value => $case->label()])->all();
    }
}
