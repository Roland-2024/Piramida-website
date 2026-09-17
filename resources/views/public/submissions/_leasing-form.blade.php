<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="leasing-application-form" data-leasing-form data-area="{{ $item->area_sqm }}">
    @csrf

    <fieldset>
        <legend>1. {{ __('cms.applicant_information') }}</legend>
        <div class="leasing-form-grid leasing-form-grid-4">
            @foreach ([
                ['company_name', 'company_name', 'text'],
                ['nipt', 'nipt', 'text'],
                ['entity_type', 'entity_type', 'select'],
                ['established_year', 'established_year', 'number'],
                ['company_address', 'address', 'text'],
                ['city', 'city', 'text'],
                ['employee_count', 'employee_count', 'number'],
                ['annual_turnover', 'annual_turnover', 'number'],
            ] as [$name, $label, $type])
                <div>
                    <label for="leasing_{{ $name }}" class="leasing-field-label">{{ __('cms.'.$label) }} *</label>
                    @if ($type === 'select')
                        <select id="leasing_{{ $name }}" name="{{ $name }}" required>
                            @foreach (['corporation', 'llc', 'partnership', 'sole_proprietor', 'ngo', 'other'] as $entityType)
                                <option value="{{ $entityType }}" @selected(old($name) === $entityType)>{{ __('cms.entity_'.$entityType) }}</option>
                            @endforeach
                        </select>
                    @else
                        <input id="leasing_{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name) }}" @if($type === 'number') min="0" @endif required>
                    @endif
                    @error($name)<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            @endforeach
        </div>
    </fieldset>

    <fieldset>
        <legend>2. {{ __('cms.contact_person') }}</legend>
        <div class="leasing-form-grid leasing-form-grid-3">
            @foreach ([
                ['contact_first_name', 'first_name', 'text'],
                ['contact_last_name', 'last_name', 'text'],
                ['contact_position', 'position', 'text'],
                ['contact_phone', 'phone', 'tel'],
                ['contact_mobile', 'mobile', 'tel'],
                ['contact_email', 'email', 'email'],
            ] as [$name, $label, $type])
                <div>
                    <label for="leasing_{{ $name }}" class="leasing-field-label">{{ __('cms.'.$label) }} *</label>
                    <input id="leasing_{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name) }}" required>
                    @error($name)<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            @endforeach
        </div>
    </fieldset>

    <fieldset>
        <legend>3. {{ __('cms.your_offer') }}</legend>
        <div class="leasing-form-grid leasing-form-grid-3">
            <div>
                <label for="leasing_offer_per_sqm" class="leasing-field-label">{{ __('cms.offer_per_sqm') }} * <span class="font-normal text-slate-500">({{ __('cms.minimum') }} 22.00 EUR)</span></label>
                <input id="leasing_offer_per_sqm" name="offer_per_sqm" type="number" min="22" step="0.01" value="{{ old('offer_per_sqm', 22) }}" required>
                @error('offer_per_sqm')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="leasing_monthly_rent" class="leasing-field-label">{{ __('cms.monthly_rent') }} ({{ $item->area_sqm }} m²)</label>
                <output id="leasing_monthly_rent" ></output>
            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend>4. {{ __('cms.required_documents') }}</legend>
        <p class="mt-2 text-sm text-slate-500">{{ __('cms.document_requirements') }}</p>
        <div class="leasing-form-grid leasing-documents-grid">
            @foreach (\App\Http\Requests\PublicSite\StoreSubmissionRequest::leasingDocumentLabels() as $field => $label)
                <div class="leasing-document-field">
                    <label for="leasing_{{ $field }}" class="text-sm font-medium">4.{{ $loop->iteration }} {{ __('cms.document_'.$field) }} @if($field !== 'other_documents') *@endif</label>
                    <div>
                        <input id="leasing_{{ $field }}" name="{{ $field }}" type="file" accept=".pdf,.doc,.docx" @required($field !== 'other_documents') class="leasing-document-input">
                        @error($field)<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            @endforeach
        </div>
    </fieldset>

    <div class="hidden" aria-hidden="true"><label>Website<input name="website" tabindex="-1" autocomplete="off"></label></div>
    <label class="leasing-consent"><input name="privacy" type="checkbox" value="1" @checked(old('privacy')) required class="mt-1 rounded border-slate-300"><span>{{ __('cms.leasing_privacy_consent') }}</span></label>
    <div class="leasing-step-actions"><button type="button" class="leasing-back-button">{{ __('cms.previous') }}</button><button class="leasing-submit-button" data-next-label="{{ __('cms.next') }}" data-submit-label="{{ __('cms.submit_request') }}">{{ __('cms.submit_request') }}</button></div>
</form>

<script>
    (() => {
        const form = document.querySelector('[data-leasing-form]');
        if (!form) return;
        const offer = form.querySelector('#leasing_offer_per_sqm');
        const output = form.querySelector('#leasing_monthly_rent');
        const area = Number(form.dataset.area || 0);
        const update = () => output.textContent = `${(Number(offer.value || 0) * area).toFixed(2)} EUR`;
        offer.addEventListener('input', update);
        update();
    })();
</script>
