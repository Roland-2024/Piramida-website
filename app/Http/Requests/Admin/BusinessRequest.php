<?php

namespace App\Http\Requests\Admin;

use App\Enums\BusinessCategory;
use App\Models\Business;
use Illuminate\Validation\Rule;

class BusinessRequest extends TranslatedContentRequest
{
    protected string $modelClass = Business::class;

    protected string $routeParameter = 'business';

    public function rules(): array
    {
        return $this->contentRules('business_translations', [
            'category' => ['required', Rule::enum(BusinessCategory::class)],
            'website_url' => ['nullable', 'url:http,https', 'max:2048'],
            'email' => ['nullable', 'email:rfc', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'is_featured' => ['required', 'boolean'],
        ], [
            'short_description' => ['nullable', 'string', 'max:2000'],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:255'],
            'opening_hours' => ['nullable', 'string', 'max:5000'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:2000'],
        ], 'name');
    }
}
