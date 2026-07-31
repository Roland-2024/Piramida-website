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
                ['instagram_url', 'Instagram URL', 'url'],
                ['linkedin_url', 'LinkedIn URL', 'url'],
            ] as [$name, $label, $type])
                <div><label for="{{ $name }}" class="block text-sm font-medium">{{ $label }}</label><input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name, $settings->{$name}) }}" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">@error($name)<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror</div>
            @endforeach
        </div>

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
