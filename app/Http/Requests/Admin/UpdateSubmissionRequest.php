<?php

namespace App\Http\Requests\Admin;

use App\Enums\SubmissionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-submissions') === true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(SubmissionStatus::class)],
            'internal_notes' => ['nullable', 'string', 'max:10000'],
        ];
    }
}
