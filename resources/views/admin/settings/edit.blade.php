<x-layouts.admin title="Site settings">
    <div class="mb-6">
        <h2 class="text-2xl font-semibold">Site settings</h2>
        <p class="mt-1 text-sm text-slate-500">Global contact details, social links, opening hours, and footer content.</p>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf
        @method('PUT')
        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @foreach ([
                ['notification_email', 'Submission notification email', 'email'],
                ['email', 'Public email', 'email'],
                ['phone', 'Public phone', 'text'],
                ['facebook_url', 'Facebook URL', 'url'],
                ['x_url', 'X URL', 'url'],
                ['instagram_url', 'Instagram URL', 'url'],
                ['linkedin_url', 'LinkedIn URL', 'url'],
                ['map_url', 'Map embed URL', 'url'],
            ] as [$name, $label, $type])
                <div><label for="{{ $name }}" class="block text-sm font-medium">{{ $label }}</label><input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name, $settings->{$name}) }}" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">@error($name)<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror</div>
            @endforeach
        </div>

        <fieldset class="mt-8 space-y-5 rounded-xl border border-slate-200 p-5">
            <legend class="px-2 font-semibold">Postmark SMTP — notifications and password resets</legend>
            <p class="text-sm text-slate-500">smtp.postmarkapp.com · Port 587 · STARTTLS required. Enable SMTP in Postmark and verify your sender domain/address first. Use a transactional stream's SMTP Access Key and Secret Key, or the Server API Token in both fields. Credentials are encrypted and never displayed again.</p>
            <input type="hidden" name="postmark_enabled" value="0">
            <div class="rounded-lg bg-slate-50 p-4 text-sm text-slate-700">
                <p class="font-semibold">Have just one token?</p>
                <p class="mt-1">Paste your <strong>Server API Token</strong> into both credential fields below. Do not use the Account API Token. This sends through the default outbound transactional stream.</p>
                <p class="mt-2">For a specific transactional stream, use its SMTP Access Key and Secret Key instead. This dashboard uses SMTP, not the HTTP API; no extra PHP package is required.</p>
                <a href="https://postmarkapp.com/developer/user-guide/send-email-with-smtp" target="_blank" rel="noopener noreferrer" class="mt-2 inline-block underline">Official Postmark setup (opens in a new tab)</a>
            </div>
            <label class="flex items-center gap-2"><input type="checkbox" name="postmark_enabled" value="1" @checked(old('postmark_enabled', $settings->postmark_enabled))> Use Postmark for request notifications and password resets</label>
            <p class="text-sm text-slate-500">When disabled, the environment mailer remains in use. Saving does not send an email.</p>
            <div class="grid gap-5 md:grid-cols-2">
                @foreach (['postmark_username' => 'SMTP Access Key / Username', 'postmark_password' => 'SMTP Secret Key / Password'] as $name => $label)
                    <div><label for="{{ $name }}" class="block text-sm font-medium">{{ $label }}</label><input id="{{ $name }}" name="{{ $name }}" type="password" autocomplete="new-password" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm"><p class="mt-1 text-xs text-slate-500">{{ $settings->{$name} ? 'Configured. Leave blank to keep the saved value.' : 'Not configured.' }}</p>@error($name)<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                @endforeach
                @foreach (['mail_from_address' => 'Verified sender email', 'mail_from_name' => 'Sender name'] as $name => $label)
                    <div><label for="{{ $name }}" class="block text-sm font-medium">{{ $label }}</label><input id="{{ $name }}" name="{{ $name }}" type="{{ $name === 'mail_from_address' ? 'email' : 'text' }}" value="{{ old($name, $settings->{$name}) }}" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">@error($name)<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                @endforeach
            </div>
            @error('postmark_enabled')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </fieldset>

        <div class="mt-8 space-y-5">
            @foreach ($locales as $locale => $localeName)
                @php
                    $translation = $settings->translations->firstWhere('locale', $locale);
                @endphp
                <details open class="rounded-xl border border-slate-200">
                    <summary class="cursor-pointer px-5 py-4 font-semibold">{{ $localeName }} <span class="text-xs font-normal uppercase text-slate-400">{{ $locale }}</span></summary>
                    <div class="grid gap-5 border-t border-slate-200 p-5 lg:grid-cols-2">
                        <div><label class="block text-sm font-medium">Address</label><input name="translations[{{ $locale }}][address]" value="{{ old("translations.{$locale}.address", $translation?->address) }}" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm"></div>
                        <div><label class="block text-sm font-medium">Opening hours</label><textarea name="translations[{{ $locale }}][opening_hours]" rows="3" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">{{ old("translations.{$locale}.opening_hours", $translation?->opening_hours) }}</textarea></div>
                        <div class="lg:col-span-2"><label class="block text-sm font-medium">Footer text</label><textarea name="translations[{{ $locale }}][footer_text]" rows="3" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">{{ old("translations.{$locale}.footer_text", $translation?->footer_text) }}</textarea></div>
                    </div>
                </details>
            @endforeach
        </div>
        <div class="mt-8 flex justify-end border-t border-slate-200 pt-6"><button class="rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white">Save settings</button></div>
    </form>
</x-layouts.admin>
