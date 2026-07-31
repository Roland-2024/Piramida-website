@if (session('success'))
    <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
@endif
@if ($errors->any())
    <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ __('cms.correct_form') }}</div>
@endif

<form method="POST" action="{{ $action }}" enctype="{{ $formType === 'career' ? 'multipart/form-data' : 'application/x-www-form-urlencoded' }}" class="mt-6 grid gap-5 sm:grid-cols-2">
    @csrf
    <div><label for="request_name" class="block text-sm font-medium">{{ __('cms.name') }}</label><input id="request_name" name="name" value="{{ old('name') }}" required class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">@error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
    <div><label for="request_email" class="block text-sm font-medium">{{ __('cms.email') }}</label><input id="request_email" name="email" type="email" value="{{ old('email') }}" required class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">@error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
    <div><label for="request_phone" class="block text-sm font-medium">{{ __('cms.phone') }}</label><input id="request_phone" name="phone" value="{{ old('phone') }}" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm"></div>

    @if ($formType === 'contact')
        <div><label for="request_subject" class="block text-sm font-medium">{{ __('cms.subject') }}</label><input id="request_subject" name="subject" value="{{ old('subject') }}" required class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm"></div>
    @elseif ($formType === 'event')
        <div><label for="request_attendees" class="block text-sm font-medium">{{ __('cms.attendees') }}</label><input id="request_attendees" name="attendees" type="number" min="1" max="20" value="{{ old('attendees', 1) }}" required class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm"></div>
    @elseif ($formType === 'program')
        <div><label for="request_organization" class="block text-sm font-medium">{{ __('cms.organization') }}</label><input id="request_organization" name="organization" value="{{ old('organization') }}" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm"></div>
        <div><label for="request_age" class="block text-sm font-medium">{{ __('cms.participant_age') }}</label><input id="request_age" name="participant_age" type="number" min="1" max="120" value="{{ old('participant_age') }}" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm"></div>
    @elseif ($formType === 'space')
        <div><label for="request_organization" class="block text-sm font-medium">{{ __('cms.organization') }}</label><input id="request_organization" name="organization" value="{{ old('organization') }}" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm"></div>
        <div><label for="request_start" class="block text-sm font-medium">{{ __('cms.requested_start') }}</label><input id="request_start" name="requested_start_at" type="datetime-local" value="{{ old('requested_start_at') }}" required class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm"></div>
        <div><label for="request_end" class="block text-sm font-medium">{{ __('cms.requested_end') }}</label><input id="request_end" name="requested_end_at" type="datetime-local" value="{{ old('requested_end_at') }}" required class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm"></div>
        <div><label for="request_attendees" class="block text-sm font-medium">{{ __('cms.attendees') }}</label><input id="request_attendees" name="attendees" type="number" min="1" value="{{ old('attendees') }}" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm"></div>
    @elseif ($formType === 'career')
        <div><label for="request_attachment" class="block text-sm font-medium">{{ __('cms.cv') }}</label><input id="request_attachment" name="attachment" type="file" accept=".pdf,.doc,.docx" required class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">@error('attachment')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
    @endif

    <div class="sm:col-span-2"><label for="request_message" class="block text-sm font-medium">{{ $formType === 'career' ? __('cms.cover_message') : __('cms.message') }}</label><textarea id="request_message" name="message" rows="5" @required(in_array($formType, ['contact', 'career'], true)) class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">{{ old('message') }}</textarea>@error('message')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
    <div class="hidden" aria-hidden="true"><label>Website<input name="website" tabindex="-1" autocomplete="off"></label></div>
    <label class="flex items-start gap-3 text-sm text-slate-600 sm:col-span-2"><input name="privacy" type="checkbox" value="1" @checked(old('privacy')) required class="mt-1 rounded border-slate-300"><span>{{ __('cms.privacy_consent') }}</span></label>
    <div class="sm:col-span-2"><button class="rounded-lg bg-slate-950 px-5 py-3 text-sm font-semibold text-white">{{ __('cms.submit_request') }}</button></div>
</form>
