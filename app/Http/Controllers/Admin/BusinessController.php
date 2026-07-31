<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BusinessCategory;
use App\Http\Requests\Admin\BusinessRequest;
use App\Models\Business;
use App\Models\Media;
use Illuminate\Http\RedirectResponse;

class BusinessController extends TranslatedContentController
{
    protected string $modelClass = Business::class;

    protected string $routeParameter = 'business';

    protected string $routePrefix = 'admin.businesses';

    protected string $singular = 'Business';

    protected string $plural = 'Businesses';

    protected string $translationTitleColumn = 'name';

    public function store(BusinessRequest $request): RedirectResponse
    {
        return $this->storeContent($request);
    }

    public function update(BusinessRequest $request): RedirectResponse
    {
        return $this->updateContent($request);
    }

    protected function globalFields(): array
    {
        return [
            ['name' => 'category', 'label' => 'Category', 'type' => 'select', 'required' => true, 'options' => collect(BusinessCategory::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])->all()],
            ['name' => 'logo_media_id', 'label' => 'Logo', 'type' => 'select', 'options' => Media::query()->latest()->pluck('original_name', 'id')->prepend('No logo', '')->all()],
            ['name' => 'website_url', 'label' => 'Website URL', 'type' => 'url'],
            ['name' => 'email', 'label' => 'Public email', 'type' => 'email'],
            ['name' => 'phone', 'label' => 'Public phone', 'type' => 'text'],
            ['name' => 'is_featured', 'label' => 'Featured', 'type' => 'checkbox'],
        ];
    }

    protected function translationFields(): array
    {
        return [
            ['name' => 'short_description', 'label' => 'Short description', 'type' => 'textarea'],
            ['name' => 'description', 'label' => 'Description', 'type' => 'richtext'],
            ['name' => 'address', 'label' => 'Address', 'type' => 'text'],
            ['name' => 'opening_hours', 'label' => 'Opening hours', 'type' => 'textarea'],
            ['name' => 'seo_title', 'label' => 'SEO title', 'type' => 'text'],
            ['name' => 'seo_description', 'label' => 'SEO description', 'type' => 'textarea'],
        ];
    }
}
