<?php

namespace App\Http\Requests\Admin;

use App\Enums\ContentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

abstract class TranslatedContentRequest extends FormRequest
{
    /** @var class-string<Model> */
    protected string $modelClass;

    protected string $routeParameter;

    public function authorize(): bool
    {
        $item = $this->route($this->routeParameter);

        return $this->user()?->can(
            $item instanceof Model ? 'update' : 'create',
            $item instanceof Model ? $item : $this->modelClass,
        ) === true;
    }

    /**
     * @param  array<string, array<int, mixed>>  $rootRules
     * @param  array<string, array<int, mixed>>  $translationFieldRules
     * @return array<string, array<int, mixed>>
     */
    protected function contentRules(
        string $translationTable,
        array $rootRules,
        array $translationFieldRules,
        string $titleField = 'title',
        bool $withMedia = true,
    ): array {
        $item = $this->route($this->routeParameter);
        $item = $item instanceof Model ? $item : null;
        $item?->loadMissing('translations');

        $rules = [
            'status' => ['required', Rule::enum(ContentStatus::class)],
            'published_at' => [
                Rule::requiredIf($this->input('status') === ContentStatus::Published->value),
                'nullable',
                'date',
            ],
            'display_order' => ['required', 'integer', 'min:0', 'max:100000'],
            'translations' => ['required', 'array'],
            ...$rootRules,
        ];

        if ($withMedia) {
            $rules['featured_media_id'] = ['nullable', Rule::exists('media', 'id')->whereNull('deleted_at')];
            $rules['gallery_media_ids'] = ['nullable', 'array', 'max:30'];
            $rules['gallery_media_ids.*'] = [
                'integer',
                'distinct',
                Rule::exists('media', 'id')->whereNull('deleted_at'),
            ];
        }

        foreach (array_keys(config('cms.locales')) as $locale) {
            $translationId = $item?->translations->firstWhere('locale', $locale)?->id;

            $rules["translations.{$locale}"] = ['required', 'array'];
            $rules["translations.{$locale}.{$titleField}"] = ['required', 'string', 'max:255'];
            $rules["translations.{$locale}.slug"] = [
                'required',
                'string',
                'max:255',
                'alpha_dash:ascii',
                Rule::unique($translationTable, 'slug')
                    ->where('locale', $locale)
                    ->ignore($translationId),
            ];

            foreach ($translationFieldRules as $field => $fieldRules) {
                $rules["translations.{$locale}.{$field}"] = $fieldRules;
            }
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        $translations = (array) $this->input('translations', []);

        foreach (array_keys(config('cms.locales')) as $locale) {
            $title = $translations[$locale]['title']
                ?? $translations[$locale]['name']
                ?? '';
            $translations[$locale]['slug'] = Str::slug(
                $translations[$locale]['slug'] ?? $title
            );
        }

        $this->merge([
            'translations' => $translations,
            'is_featured' => $this->boolean('is_featured'),
        ]);
    }
}
