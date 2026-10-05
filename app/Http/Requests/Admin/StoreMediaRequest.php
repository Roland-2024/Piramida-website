<?php

namespace App\Http\Requests\Admin;

use App\Models\Media;
use Illuminate\Foundation\Http\FormRequest;

class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Media::class) === true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'max:10240',
                'mimes:jpg,jpeg,png,webp,gif,pdf,mp4,webm',
                'extensions:jpg,jpeg,png,webp,gif,pdf,mp4,webm',
                'mimetypes:image/jpeg,image/png,image/webp,image/gif,application/pdf,video/mp4,video/webm',
            ],
            'alt_text_al' => ['nullable', 'string', 'max:255'],
            'alt_text_en' => ['nullable', 'string', 'max:255'],
        ];
    }
}
