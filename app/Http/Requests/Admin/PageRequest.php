<?php

namespace App\Http\Requests\Admin;

use App\Enums\ContentStatus;
use App\Models\Page;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PageRequest extends FormRequest
{
    public function authorize(): bool
    {
        $page = $this->route('page');

        return $this->user()?->can($page ? 'update' : 'create', $page ?: Page::class) === true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        /** @var Page|null $page */
        $page = $this->route('page');
        $page?->loadMissing('translations');

        $rules = [
            'featured_media_id' => ['nullable', Rule::exists('media', 'id')->whereNull('deleted_at')],
            'is_homepage' => ['required', 'boolean'],
            'status' => ['required', Rule::enum(ContentStatus::class)],
            'published_at' => [
                Rule::requiredIf($this->input('status') === ContentStatus::Published->value),
                'nullable',
                'date',
            ],
            'display_order' => ['required', 'integer', 'min:0', 'max:100000'],
            'translations' => ['required', 'array'],
        ];

        foreach (array_keys(config('cms.locales')) as $locale) {
            $translationId = $page?->translations->firstWhere('locale', $locale)?->id;

            $rules["translations.{$locale}"] = ['required', 'array'];
            $rules["translations.{$locale}.title"] = ['required', 'string', 'max:255'];
            $rules["translations.{$locale}.slug"] = [
                'required',
                'string',
                'max:255',
                'alpha_dash:ascii',
                Rule::unique('page_translations', 'slug')
                    ->where('locale', $locale)
                    ->ignore($translationId),
            ];
            $rules["translations.{$locale}.short_description"] = ['nullable', 'string', 'max:2000'];
            $rules["translations.{$locale}.content"] = ['nullable', 'string'];
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
