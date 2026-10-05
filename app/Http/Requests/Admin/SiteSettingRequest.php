<?php

namespace App\Http\Requests\Admin;

use App\Models\SiteSetting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SiteSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-settings') === true;
    }

    public function rules(): array
    {
        $rules = [
            'postmark_enabled' => ['sometimes', 'boolean'],
            'postmark_username' => [Rule::requiredIf(fn () => $this->boolean('postmark_enabled') && ! SiteSetting::query()->value('postmark_username')), 'nullable', 'string', 'max:255', 'regex:/^[a-zA-Z0-9_-]+$/'],
            'postmark_password' => [Rule::requiredIf(fn () => $this->boolean('postmark_enabled') && ! SiteSetting::query()->value('postmark_password')), 'nullable', 'string', 'max:255', 'regex:/^[a-zA-Z0-9_-]+$/'],
            'mail_from_address' => ['required_if:postmark_enabled,1', 'nullable', 'email:rfc', 'max:255'],
            'mail_from_name' => ['required_if:postmark_enabled,1', 'nullable', 'string', 'max:255', 'not_regex:/[\r\n]/'],
            'notification_email' => ['required_if:postmark_enabled,1', 'nullable', 'email:rfc', 'max:255'],
            'email' => ['nullable', 'email:rfc', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'facebook_url' => ['nullable', 'url:http,https', 'max:2048'],
            'x_url' => ['nullable', 'url:http,https', 'max:2048'],
            'instagram_url' => ['nullable', 'url:http,https', 'max:2048'],
            'linkedin_url' => ['nullable', 'url:http,https', 'max:2048'],
            'map_url' => ['nullable', 'url:http,https', 'max:2048'],
            'translations' => ['required', 'array'],
        ];

        foreach (array_keys(config('cms.locales')) as $locale) {
            $rules["translations.{$locale}"] = ['required', 'array'];
            $rules["translations.{$locale}.address"] = ['nullable', 'string', 'max:1000'];
            $rules["translations.{$locale}.opening_hours"] = ['nullable', 'string', 'max:5000'];
            $rules["translations.{$locale}.footer_text"] = ['nullable', 'string', 'max:5000'];
        }

        return $rules;
    }
}
