<?php

namespace App\Http\Requests\Admin;

use App\Enums\ContentStatus;
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
            'status' => ['required', Rule::enum(ContentStatus::class)],
            'published_at' => [
                Rule::requiredIf($this->input('status') === ContentStatus::Published->value),
                'nullable',
                'date',
            ],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after_or_equal:starts_at'],
            'external_url' => ['nullable', 'url:http,https', 'max:2048'],
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

        $this->merge(['translations' => $translations]);
    }
}
