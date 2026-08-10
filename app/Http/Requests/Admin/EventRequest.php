<?php

namespace App\Http\Requests\Admin;

use App\Enums\BookingMode;
use App\Enums\ContentStatus;
use App\Enums\EventCategory;
use App\Models\Event;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EventRequest extends FormRequest
{
    public function authorize(): bool
    {
        $event = $this->route('event');

        return $this->user()?->can($event ? 'update' : 'create', $event ?: Event::class) === true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        /** @var Event|null $event */
        $event = $this->route('event');
        $event?->loadMissing('translations');

        $rules = [
            'featured_media_id' => ['nullable', Rule::exists('media', 'id')->whereNull('deleted_at')],
            'category' => ['required', Rule::enum(EventCategory::class)],
            'status' => ['required', Rule::enum(ContentStatus::class)],
            'published_at' => [
                Rule::requiredIf($this->input('status') === ContentStatus::Published->value),
                'nullable',
                'date',
            ],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after_or_equal:starts_at'],
            'booking_mode' => ['required', Rule::enum(BookingMode::class)],
            'external_url' => [
                Rule::requiredIf(in_array($this->input('booking_mode'), ['external', 'both'], true)),
                'nullable',
                'url:http,https',
                'max:2048',
            ],
            'is_featured' => ['required', 'boolean'],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'display_order' => ['required', 'integer', 'min:0', 'max:100000'],
            'translations' => ['required', 'array'],
        ];

        foreach (array_keys(config('cms.locales')) as $locale) {
            $translationId = $event?->translations->firstWhere('locale', $locale)?->id;

            $rules["translations.{$locale}"] = ['required', 'array'];
            $rules["translations.{$locale}.title"] = ['required', 'string', 'max:255'];
            $rules["translations.{$locale}.slug"] = [
                'required',
                'string',
                'max:255',
                'alpha_dash:ascii',
                Rule::unique('event_translations', 'slug')
                    ->where('locale', $locale)
                    ->ignore($translationId),
            ];
            $rules["translations.{$locale}.short_description"] = ['nullable', 'string', 'max:2000'];
            $rules["translations.{$locale}.description"] = ['nullable', 'string'];
            $rules["translations.{$locale}.location"] = ['nullable', 'string', 'max:255'];
            $rules["translations.{$locale}.price_label"] = ['nullable', 'string', 'max:255'];
            $rules["translations.{$locale}.seo_title"] = ['nullable', 'string', 'max:255'];
            $rules["translations.{$locale}.seo_description"] = ['nullable', 'string', 'max:2000'];
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        $translations = (array) $this->input('translations', []);

        foreach (array_keys(config('cms.locales')) as $locale) {
            $translations[$locale]['slug'] = Str::slug(
                $translations[$locale]['slug'] ?? $translations[$locale]['title'] ?? ''
            );
        }

        $this->merge([
            'translations' => $translations,
            'is_featured' => $this->boolean('is_featured'),
        ]);
    }
}
