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

    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();
        $leasing = $this->routeIs('admin.leasing.*');
        $this->merge([
            'type' => $leasing ? SpaceType::Leasing->value : SpaceType::EventSpace->value,
            'is_available' => $leasing && $this->boolean('is_available'),
            'leasing_unit_id' => $leasing ? $this->input('leasing_unit_id') : null,
        ]);
    }

    public function authorize(): bool
    {
        $item = $this->route('space');
        if ($item instanceof Space) {
            abort_unless($item->type->value === $this->input('type'), 404);
        }

        return parent::authorize();
    }

    public function rules(): array
    {
        return $this->contentRules('space_translations', [
            'type' => ['required', Rule::enum(SpaceType::class)],
            'is_available' => ['required', 'boolean'],
            'leasing_unit_id' => [
                Rule::requiredIf($this->boolean('is_available')),
                'nullable', 'integer', Rule::exists('leasing_units', 'id'),
                Rule::unique('spaces', 'leasing_unit_id')->ignore($this->route('space')?->id),
            ],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'area_sqm' => [
                Rule::requiredIf($this->boolean('is_available')),
                'nullable', 'numeric', $this->boolean('is_available') ? 'gt:0' : 'min:0', 'max:99999999.99',
            ],
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
