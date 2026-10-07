<?php

namespace App\Http\Requests\Admin;

use App\Support\WebsiteContent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WebsiteContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-website-content') === true;
    }

    public function rules(): array
    {
        $rules = ['group' => ['required', Rule::in(['cms', 'website', 'seo', 'validation', 'pagination', 'images'])]];
        if ($this->input('group') === 'images') {
            $keys = array_map('sha1', config('website_images', []));
            $rules['images'] = ['required', 'array:'.implode(',', $keys)];
            $rules['images.*'] = ['nullable', 'integer', Rule::exists('media', 'id')->where(fn ($query) => $query->whereNull('deleted_at')->where('disk', 'public')->where('mime_type', 'like', 'image/%'))];
        } else {
            $defaults = ['al' => WebsiteContent::defaults('al'), 'en' => WebsiteContent::defaults('en')];
            $keys = array_filter(array_keys($defaults['en']), fn ($key) => str_starts_with($key, $this->input('group').'.'));
            $rules['texts'] = ['required', 'array:'.implode(',', array_map('sha1', $keys))];
            foreach ($keys as $key) {
                $id = sha1($key);
                $rules['texts.'.$id] = ['sometimes', 'array:al,en'];
                foreach (['al', 'en'] as $locale) {
                    $rules["texts.{$id}.{$locale}"] = ['sometimes', 'nullable', 'string', 'max:10000', function ($attribute, $value, $fail) use ($key, $locale, $defaults) {
                        preg_match_all('/:[a-zA-Z_]+/', $defaults[$locale][$key] ?? '', $matches);
                        foreach (array_unique($matches[0]) as $placeholder) {
                            if (! str_contains($value ?? '', $placeholder)) {
                                $fail("Keep the placeholder {$placeholder} in this text.");
                            }
                        }
                    }];
                }
            }
        }

        return $rules;
    }
}
