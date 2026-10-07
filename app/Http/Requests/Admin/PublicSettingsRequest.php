<?php

namespace App\Http\Requests\Admin;

class PublicSettingsRequest extends SiteSettingRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-website-content') === true;
    }

    public function rules(): array
    {
        $rules = array_filter(parent::rules(), fn ($key) => in_array($key, ['email', 'phone', 'facebook_url', 'x_url', 'instagram_url', 'linkedin_url', 'map_url', 'translations']) || str_starts_with($key, 'translations.'), ARRAY_FILTER_USE_KEY);
        $rules['translations'] = ['required', 'array:al,en'];
        foreach (['al', 'en'] as $locale) {
            $rules['translations.'.$locale] = ['required', 'array:address,opening_hours,footer_text'];
        }

        return $rules;
    }
}
