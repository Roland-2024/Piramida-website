<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingMode;
use App\Enums\ProgramCategory;
use App\Http\Requests\Admin\ProgramRequest;
use App\Models\Program;
use Illuminate\Http\RedirectResponse;

class ProgramController extends TranslatedContentController
{
    protected string $modelClass = Program::class;

    protected string $routeParameter = 'program';

    protected string $routePrefix = 'admin.programs';

    protected string $singular = 'Program';

    protected string $plural = 'Programs';

    public function store(ProgramRequest $request): RedirectResponse
    {
        return $this->storeContent($request);
    }

    public function update(ProgramRequest $request): RedirectResponse
    {
        return $this->updateContent($request);
    }

    protected function globalFields(): array
    {
        return [
            ['name' => 'category', 'label' => 'Category', 'type' => 'select', 'required' => true, 'options' => $this->options(ProgramCategory::cases())],
            ['name' => 'starts_at', 'label' => 'Starts at', 'type' => 'datetime-local'],
            ['name' => 'ends_at', 'label' => 'Ends at', 'type' => 'datetime-local'],
            ['name' => 'booking_mode', 'label' => 'Request / booking action', 'type' => 'select', 'required' => true, 'options' => $this->options(BookingMode::cases())],
            ['name' => 'external_url', 'label' => 'External booking URL', 'type' => 'url'],
            ['name' => 'is_featured', 'label' => 'Featured', 'type' => 'checkbox'],
        ];
    }

    protected function translationFields(): array
    {
        return [
            ['name' => 'short_description', 'label' => 'Short description', 'type' => 'textarea'],
            ['name' => 'description', 'label' => 'Description', 'type' => 'richtext'],
            ['name' => 'location', 'label' => 'Location', 'type' => 'text'],
            ['name' => 'schedule', 'label' => 'Schedule', 'type' => 'text'],
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
