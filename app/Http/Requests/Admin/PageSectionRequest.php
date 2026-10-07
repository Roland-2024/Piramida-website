<?php

namespace App\Http\Requests\Admin;

use App\Enums\SectionType;
use App\Models\PageSection;
use App\Rules\SafeUrl;
use App\Rules\YouTubeUrl;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PageSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $section = $this->route('section');

        return $this->user()?->can($section ? 'update' : 'create', $section ?: PageSection::class) === true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $rules = [
            'page_id' => ['required', Rule::exists('pages', 'id')->whereNull('deleted_at')],
            'internal_name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::enum(SectionType::class)],
            'primary_media_id' => ['nullable', Rule::exists('media', 'id')->whereNull('deleted_at')],
            'secondary_media_id' => ['nullable', Rule::exists('media', 'id')->whereNull('deleted_at')],
            'gallery_media_ids' => ['nullable', 'array', 'max:30'],
            'gallery_media_ids.*' => ['integer', 'distinct', Rule::exists('media', 'id')->whereNull('deleted_at')->where('disk', 'public')->where(fn ($query) => $query->where('mime_type', 'like', 'image/%'))],
            'video_url' => ['bail', 'nullable', 'string', 'max:2048', new SafeUrl],
            'primary_button_url' => ['nullable', 'string', 'max:2048', new SafeUrl],
            'secondary_button_url' => ['nullable', 'string', 'max:2048', new SafeUrl],
            'display_order' => ['required', 'integer', 'min:0', 'max:100000'],
            'is_active' => ['required', 'boolean'],
            'structured_data' => ['nullable', 'json'],
            'translations' => ['required', 'array'],
        ];

        foreach (array_keys(config('cms.locales')) as $locale) {
            $rules["translations.{$locale}"] = ['required', 'array'];
            $rules["translations.{$locale}.title"] = ['nullable', 'string', 'max:255'];
            $rules["translations.{$locale}.subtitle"] = ['nullable', 'string', 'max:255'];
            $rules["translations.{$locale}.description"] = ['nullable', 'string'];
            $rules["translations.{$locale}.primary_button_label"] = ['nullable', 'string', 'max:255'];
            $rules["translations.{$locale}.secondary_button_label"] = ['nullable', 'string', 'max:255'];
        }

        if ($this->input('internal_name') === 'About - History') {
            $rules['video_url'][] = function ($attribute, $value, $fail): void {
                if (! YouTubeUrl::videoId($value) && ! preg_match('/\.(mp4|webm)$/i', parse_url($value, PHP_URL_PATH) ?? '')) {
                    $fail('Use a YouTube link or an MP4/WebM video URL.');
                }
            };
        }

        return $rules;
    }
}
