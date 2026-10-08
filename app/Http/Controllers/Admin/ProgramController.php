<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ProgramCategory;
use App\Http\Requests\Admin\ProgramRequest;
use App\Models\Program;
use Illuminate\Http\RedirectResponse;

class ProgramController extends TranslatedContentController
{
    protected string $modelClass = Program::class;

    protected string $routeParameter = 'program';

    protected string $routePrefix = 'admin.programs';

    protected string $singular = 'Carousel post';

    protected string $plural = 'Carousel posts';

    protected bool $withGallery = false;

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
            ['name' => 'category', 'label' => 'Carousel page', 'type' => 'select', 'required' => true, 'options' => array_diff_key($this->options(ProgramCategory::cases()), ['business' => true])],
        ];
    }

    protected function translationFields(): array
    {
        return [
            ['name' => 'description', 'label' => 'Carousel caption', 'type' => 'textarea'],
        ];
    }

    private function options(array $cases): array
    {
        return collect($cases)->mapWithKeys(fn ($case) => [$case->value => $case->label()])->all();
    }
}
