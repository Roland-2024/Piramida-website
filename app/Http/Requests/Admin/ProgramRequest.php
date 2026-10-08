<?php

namespace App\Http\Requests\Admin;

use App\Models\Program;
use Illuminate\Validation\Rule;

class ProgramRequest extends TranslatedContentRequest
{
    protected string $modelClass = Program::class;

    protected string $routeParameter = 'program';

    public function rules(): array
    {
        $rules = $this->contentRules('program_translations', [
            'category' => ['required', Rule::in(['education', 'innovation', 'art_culture'])],
        ], [
            'description' => ['nullable', 'string'],
        ]);
        unset($rules['gallery_media_ids'], $rules['gallery_media_ids.*']);

        return $rules;
    }
}
