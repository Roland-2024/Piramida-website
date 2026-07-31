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
    <div><label for="request_phone" class="block text-sm font-medium">{{ __('cms.phone') }}</label><input id="request_phone" name="phone" value="{{ old('phone') }}" @required(in_array($formType, ['event_space', 'leasing'], true)) class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">@error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>

    @if ($formType === 'contact')
        <div><label for="request_subject" class="block text-sm font-medium">{{ __('cms.subject') }}</label><input id="request_subject" name="subject" value="{{ old('subject') }}" required class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm"></div>
    @elseif ($formType === 'event')
        <div><label for="request_attendees" class="block text-sm font-medium">{{ __('cms.attendees') }}</label><input id="request_attendees" name="attendees" type="number" min="1" max="20" value="{{ old('attendees', 1) }}" required class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm"></div>
    @elseif ($formType === 'event_space')
        <div><label for="request_attendees" class="block text-sm font-medium">{{ __('cms.attendees') }}</label><input id="request_attendees" name="attendees" type="number" min="1" value="{{ old('attendees') }}" required class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">@error('attendees')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
        <div><label for="request_event_type" class="block text-sm font-medium">{{ __('cms.event_type') }}</label><input id="request_event_type" name="event_type" value="{{ old('event_type') }}" required class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">@error('event_type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
        <div><label for="request_date" class="block text-sm font-medium">{{ __('cms.preferred_date') }}</label><input id="request_date" name="preferred_date" type="date" value="{{ old('preferred_date') }}" required class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">@error('preferred_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
        <div><label for="request_time" class="block text-sm font-medium">{{ __('cms.preferred_time') }}</label><input id="request_time" name="preferred_time" type="time" value="{{ old('preferred_time') }}" required class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">@error('preferred_time')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
    @elseif ($formType === 'leasing')
        <div><label for="request_organization" class="block text-sm font-medium">{{ __('cms.organization') }}</label><input id="request_organization" name="organization" value="{{ old('organization') }}" required class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">@error('organization')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
    @elseif ($formType === 'career')
        <div><label for="request_attachment" class="block text-sm font-medium">{{ __('cms.cv') }}</label><input id="request_attachment" name="attachment" type="file" accept=".pdf,.doc,.docx" required class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">@error('attachment')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
    @endif

    <div class="sm:col-span-2"><label for="request_message" class="block text-sm font-medium">{{ $formType === 'career' ? __('cms.cover_message') : __('cms.message') }}</label><textarea id="request_message" name="message" rows="5" @required(in_array($formType, ['contact', 'career', 'leasing'], true)) class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">{{ old('message') }}</textarea>@error('message')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
    <div class="hidden" aria-hidden="true"><label>Website<input name="website" tabindex="-1" autocomplete="off"></label></div>
    <label class="flex items-start gap-3 text-sm text-slate-600 sm:col-span-2"><input name="privacy" type="checkbox" value="1" @checked(old('privacy')) required class="mt-1 rounded border-slate-300"><span>{{ __('cms.privacy_consent') }}</span></label>
    <div class="sm:col-span-2"><button class="rounded-lg bg-slate-950 px-5 py-3 text-sm font-semibold text-white">{{ __('cms.submit_request') }}</button></div>
</form>
