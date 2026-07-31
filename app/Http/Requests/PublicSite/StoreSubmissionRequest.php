<?php

namespace App\Http\Requests\PublicSite;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $common = [
            'privacy' => ['accepted'],
            'website' => ['nullable', 'string', 'max:0'],
        ];

        $person = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'message' => ['nullable', 'string', 'max:10000'],
        ];

        return match ($this->route()?->getName()) {
            'public.contact.store' => [
                ...$common,
                ...$person,
                'message' => ['required', 'string', 'max:10000'],
            ],
            'public.events.request' => [
                ...$common,
                ...$person,
                'phone' => ['required', 'string', 'max:50'],
            ],
            'public.spaces.event-request' => [
                ...$common,
                ...$person,
                'phone' => ['required', 'string', 'max:50'],
                'event_type' => ['required', 'string', 'max:255'],
                'preferred_date' => ['required', 'date', 'after_or_equal:today'],
                'preferred_time' => ['required', 'date_format:H:i'],
                'attendees' => ['required', 'integer', 'min:1', 'max:100000'],
            ],
            'public.spaces.leasing-request' => $this->leasingRules($common),
            'public.careers.apply' => [
                ...$common,
                'first_name' => ['required', 'string', 'max:100'],
                'last_name' => ['required', 'string', 'max:100'],
                'email' => ['required', 'email:rfc', 'max:255'],
                'phone' => ['required', 'string', 'max:50'],
                'message' => ['nullable', 'string', 'max:10000'],
                'attachment' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            ],
            default => [...$common, ...$person],
        };
    }

    /**
     * @param  array<string, array<int, mixed>>  $common
     * @return array<string, array<int, mixed>>
     */
    private function leasingRules(array $common): array
    {
        $rules = [
            ...$common,
            'company_name' => ['required', 'string', 'max:255'],
            'nipt' => ['required', 'string', 'max:50'],
            'entity_type' => ['required', Rule::in(['corporation', 'llc', 'partnership', 'sole_proprietor', 'ngo', 'other'])],
            'established_year' => ['required', 'integer', 'min:1800', 'max:'.now()->year],
            'company_address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'employee_count' => ['required', 'integer', 'min:1', 'max:1000000'],
            'annual_turnover' => ['required', 'numeric', 'min:0', 'max:999999999999.99'],
            'contact_first_name' => ['required', 'string', 'max:100'],
            'contact_last_name' => ['required', 'string', 'max:100'],
            'contact_position' => ['required', 'string', 'max:150'],
            'contact_phone' => ['required', 'string', 'max:50'],
            'contact_mobile' => ['required', 'string', 'max:50'],
            'contact_email' => ['required', 'email:rfc', 'max:255'],
            'offer_per_sqm' => ['required', 'numeric', 'min:22', 'max:999999.99'],
        ];

        foreach (array_keys(self::leasingDocumentLabels()) as $field) {
            $rules[$field] = [
                $field === 'other_documents' ? 'nullable' : 'required',
                'file',
                'mimes:pdf,doc,docx',
                'max:4096',
            ];
        }

        return $rules;
    }

    /** @return array<string, string> */
    public static function leasingDocumentLabels(): array
    {
        return [
            'company_profile' => 'Company profile',
            'qkb_extract' => 'QKB extract',
            'employee_certificate' => 'Employee-count certificate',
            'criminal_record_certificate' => 'Criminal-record certificate',
            'no_lawsuits_certificate' => 'No-lawsuits certificate',
            'no_liquidation_certificate' => 'No-liquidation certificate',
            'tax_clearance_certificate' => 'Tax-clearance certificate',
            'financial_statements' => 'Financial statements',
            'financial_offer' => 'Financial offer',
            'concept_document' => 'Concept',
            'other_documents' => 'Other document',
        ];
    }
}
