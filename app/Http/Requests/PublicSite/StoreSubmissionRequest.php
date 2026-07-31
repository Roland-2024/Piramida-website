<?php

namespace App\Http\Requests\PublicSite;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'message' => ['nullable', 'string', 'max:10000'],
            'privacy' => ['accepted'],
            'website' => ['nullable', 'string', 'max:0'],
        ];

        return match ($this->route()?->getName()) {
            'public.contact.store' => [
                ...$rules,
                'subject' => ['required', 'string', 'max:255'],
                'message' => ['required', 'string', 'max:10000'],
            ],
            'public.events.request' => [
                ...$rules,
                'attendees' => ['required', 'integer', 'min:1', 'max:20'],
            ],
            'public.programs.request' => [
                ...$rules,
                'organization' => ['nullable', 'string', 'max:255'],
                'participant_age' => ['nullable', 'integer', 'min:1', 'max:120'],
            ],
            'public.spaces.request' => [
                ...$rules,
                'organization' => ['nullable', 'string', 'max:255'],
                'requested_start_at' => ['required', 'date', 'after_or_equal:today'],
                'requested_end_at' => ['required', 'date', 'after_or_equal:requested_start_at'],
                'attendees' => ['nullable', 'integer', 'min:1', 'max:100000'],
            ],
            'public.careers.apply' => [
                ...$rules,
                'message' => ['required', 'string', 'max:10000'],
                'attachment' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            ],
            default => $rules,
        };
    }
}
