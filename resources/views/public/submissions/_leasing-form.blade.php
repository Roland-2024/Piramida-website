<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="mt-6 space-y-10" data-leasing-form data-area="{{ $item->area_sqm }}">
    @csrf

    <fieldset>
        <legend class="text-xl font-semibold">1. {{ __('cms.applicant_information') }}</legend>
        <div class="mt-5 grid gap-5 sm:grid-cols-2">
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
                    <label for="leasing_{{ $name }}" class="block text-sm font-medium">{{ __('cms.'.$label) }} *</label>
                    @if ($type === 'select')
                        <select id="leasing_{{ $name }}" name="{{ $name }}" required class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm">
                            @foreach (['corporation', 'llc', 'partnership', 'sole_proprietor', 'ngo', 'other'] as $entityType)
                                <option value="{{ $entityType }}" @selected(old($name) === $entityType)>{{ __('cms.entity_'.$entityType) }}</option>
                            @endforeach
                        </select>
                    @else
                        <input id="leasing_{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name) }}" @if($type === 'number') min="0" @endif required class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                    @endif
                    @error($name)<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            @endforeach
        </div>
    </fieldset>

    <fieldset>
        <legend class="text-xl font-semibold">2. {{ __('cms.contact_person') }}</legend>
        <div class="mt-5 grid gap-5 sm:grid-cols-2">
            @foreach ([
                ['contact_first_name', 'first_name', 'text'],
                ['contact_last_name', 'last_name', 'text'],
                ['contact_position', 'position', 'text'],
                ['contact_phone', 'phone', 'tel'],
                ['contact_mobile', 'mobile', 'tel'],
                ['contact_email', 'email', 'email'],
            ] as [$name, $label, $type])
                <div>
                    <label for="leasing_{{ $name }}" class="block text-sm font-medium">{{ __('cms.'.$label) }} *</label>
                    <input id="leasing_{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name) }}" required class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                    @error($name)<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            @endforeach
        </div>
    </fieldset>

    <fieldset>
        <legend class="text-xl font-semibold">3. {{ __('cms.your_offer') }}</legend>
        <div class="mt-5 grid gap-5 sm:grid-cols-2">
            <div>
                <label for="leasing_offer_per_sqm" class="block text-sm font-medium">{{ __('cms.offer_per_sqm') }} * <span class="font-normal text-slate-500">({{ __('cms.minimum') }} 22.00 EUR)</span></label>
                <input id="leasing_offer_per_sqm" name="offer_per_sqm" type="number" min="22" step="0.01" value="{{ old('offer_per_sqm', 22) }}" required class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                @error('offer_per_sqm')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="leasing_monthly_rent" class="block text-sm font-medium">{{ __('cms.monthly_rent') }} ({{ $item->area_sqm }} m²)</label>
                <output id="leasing_monthly_rent" class="mt-2 block w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold"></output>
            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend class="text-xl font-semibold">4. {{ __('cms.required_documents') }}</legend>
        <p class="mt-2 text-sm text-slate-500">{{ __('cms.document_requirements') }}</p>
        <div class="mt-5 space-y-4">
            @foreach (\App\Http\Requests\PublicSite\StoreSubmissionRequest::leasingDocumentLabels() as $field => $label)
                <div class="grid gap-2 rounded-xl border border-slate-200 p-4 sm:grid-cols-[1fr_1.2fr] sm:items-center">
                    <label for="leasing_{{ $field }}" class="text-sm font-medium">4.{{ $loop->iteration }} {{ __('cms.document_'.$field) }} @if($field !== 'other_documents') *@endif</label>
                    <div>
                        <input id="leasing_{{ $field }}" name="{{ $field }}" type="file" accept=".pdf,.doc,.docx" @required($field !== 'other_documents') class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        @error($field)<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            @endforeach
        </div>
    </fieldset>

    <div class="hidden" aria-hidden="true"><label>Website<input name="website" tabindex="-1" autocomplete="off"></label></div>
    <label class="flex items-start gap-3 text-sm text-slate-600"><input name="privacy" type="checkbox" value="1" @checked(old('privacy')) required class="mt-1 rounded border-slate-300"><span>{{ __('cms.leasing_privacy_consent') }}</span></label>
    <button class="rounded-lg bg-slate-950 px-6 py-3 text-sm font-semibold text-white">{{ __('cms.submit_request') }}</button>
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
