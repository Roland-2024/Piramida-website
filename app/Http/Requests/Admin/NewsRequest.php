<?php

namespace App\Http\Requests\Admin;

use App\Enums\ContentStatus;
use App\Models\News;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class NewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        $news = $this->route('news');

        return $this->user()?->can($news ? 'update' : 'create', $news ?: News::class) === true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        /** @var News|null $news */
        $news = $this->route('news');
        $news?->loadMissing('translations');

        $rules = [
            'featured_media_id' => ['nullable', Rule::exists('media', 'id')->whereNull('deleted_at')],
            'status' => ['required', Rule::enum(ContentStatus::class)],
            'published_at' => [
                Rule::requiredIf($this->input('status') === ContentStatus::Published->value),
                'nullable',
                'date',
            ],
            'author_name' => ['nullable', 'string', 'max:255'],
            'translations' => ['required', 'array'],
        ];

        foreach (array_keys(config('cms.locales')) as $locale) {
            $translationId = $news?->translations->firstWhere('locale', $locale)?->id;

            $rules["translations.{$locale}"] = ['required', 'array'];
            $rules["translations.{$locale}.title"] = ['required', 'string', 'max:255'];
            $rules["translations.{$locale}.slug"] = [
                'required',
                'string',
                'max:255',
                'alpha_dash:ascii',
                Rule::unique('news_translations', 'slug')
                    ->where('locale', $locale)
                    ->ignore($translationId),
            ];
            $rules["translations.{$locale}.excerpt"] = ['nullable', 'string', 'max:2000'];
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
