<?php

namespace App\Http\Requests\Admin;

use App\Enums\BookingMode;
use App\Enums\EmploymentType;
use App\Models\Career;
use Illuminate\Validation\Rule;

class CareerRequest extends TranslatedContentRequest
{
    protected string $modelClass = Career::class;

    protected string $routeParameter = 'career';

    public function rules(): array
    {
        return $this->contentRules('career_translations', [
            'employment_type' => ['required', Rule::enum(EmploymentType::class)],
            'deadline' => ['nullable', 'date'],
            'booking_mode' => ['required', Rule::enum(BookingMode::class)],
            'external_url' => [
                Rule::requiredIf(in_array($this->input('booking_mode'), ['external', 'both'], true)),
                'nullable',
                'url:http,https',
                'max:2048',
            ],
        ], [
            'department' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:2000'],
            'description' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:2000'],
        ], withMedia: false);
    }
}
