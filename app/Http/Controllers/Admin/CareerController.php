<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingMode;
use App\Enums\EmploymentType;
use App\Http\Requests\Admin\CareerRequest;
use App\Models\Career;
use Illuminate\Http\RedirectResponse;

class CareerController extends TranslatedContentController
{
    protected string $modelClass = Career::class;

    protected string $routeParameter = 'career';

    protected string $routePrefix = 'admin.careers';

    protected string $singular = 'Career';

    protected string $plural = 'Careers';

    protected bool $withMedia = false;

    public function store(CareerRequest $request): RedirectResponse
    {
        return $this->storeContent($request);
    }

    public function update(CareerRequest $request): RedirectResponse
    {
        return $this->updateContent($request);
    }

    protected function globalFields(): array
    {
        return [
            ['name' => 'employment_type', 'label' => 'Employment type', 'type' => 'select', 'required' => true, 'options' => $this->options(EmploymentType::cases())],
            ['name' => 'deadline', 'label' => 'Application deadline', 'type' => 'datetime-local'],
            ['name' => 'booking_mode', 'label' => 'Application action', 'type' => 'select', 'required' => true, 'options' => $this->options(BookingMode::cases()), 'default' => 'internal'],
            ['name' => 'external_url', 'label' => 'External application URL', 'type' => 'url'],
        ];
    }

    protected function translationFields(): array
    {
        return [
            ['name' => 'department', 'label' => 'Department', 'type' => 'text'],
            ['name' => 'location', 'label' => 'Location', 'type' => 'text'],
            ['name' => 'short_description', 'label' => 'Short description', 'type' => 'textarea'],
            ['name' => 'description', 'label' => 'Description', 'type' => 'richtext'],
            ['name' => 'requirements', 'label' => 'Requirements', 'type' => 'richtext'],
            ['name' => 'seo_title', 'label' => 'SEO title', 'type' => 'text'],
            ['name' => 'seo_description', 'label' => 'SEO description', 'type' => 'textarea'],
        ];
    }

    private function options(array $cases): array
    {
        return collect($cases)->mapWithKeys(fn ($case) => [$case->value => $case->label()])->all();
    }
}
