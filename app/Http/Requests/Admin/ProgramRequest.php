<?php

namespace App\Http\Requests\Admin;

use App\Enums\BookingMode;
use App\Enums\ProgramCategory;
use App\Models\Program;
use Illuminate\Validation\Rule;

class ProgramRequest extends TranslatedContentRequest
{
    protected string $modelClass = Program::class;

    protected string $routeParameter = 'program';

    public function rules(): array
    {
        return $this->contentRules('program_translations', [
            'category' => ['required', Rule::enum(ProgramCategory::class)],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
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
            'location' => ['nullable', 'string', 'max:255'],
            'schedule' => ['nullable', 'string', 'max:255'],
            'price_label' => ['nullable', 'string', 'max:255'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
