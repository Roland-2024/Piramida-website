<x-layouts.public :title="__('cms.contact')" :language-urls="$languageUrls">
    <section class="mx-auto max-w-7xl px-5 py-16 lg:px-8">
        <p class="text-sm font-semibold uppercase tracking-widest text-amber-700">{{ __('cms.contact') }}</p>
        <h1 class="mt-3 text-4xl font-semibold">{{ __('cms.lets_get_in_touch') }}</h1>
        <p class="mt-3 max-w-2xl text-slate-600">{{ __('cms.contact_intro') }}</p>
        <div class="mt-9 grid gap-8 lg:grid-cols-[0.8fr_1.2fr]">
            <aside class="rounded-3xl bg-slate-950 p-7 text-white">
                @if ($siteSettings?->phone)<a href="tel:{{ $siteSettings->phone }}" class="block text-lg font-semibold">{{ $siteSettings->phone }}</a>@endif
                @if ($siteSettings?->email)<a href="mailto:{{ $siteSettings->email }}" class="mt-3 block text-lg font-semibold text-amber-300">{{ $siteSettings->email }}</a>@endif
                @if ($siteSettings?->translation()?->address)<p class="mt-8 leading-7 text-slate-300">{{ $siteSettings->translation()->address }}</p>@endif
                @if ($siteSettings?->translation()?->opening_hours)<p class="mt-5 whitespace-pre-line text-sm leading-6 text-slate-400">{{ $siteSettings->translation()->opening_hours }}</p>@endif
                <div class="mt-8 flex flex-wrap gap-4 text-sm text-slate-300">
                    @foreach ([['Facebook', $siteSettings?->facebook_url], ['X', $siteSettings?->x_url], ['LinkedIn', $siteSettings?->linkedin_url], ['Instagram', $siteSettings?->instagram_url]] as [$label, $url])
                        @if ($url)<a href="{{ $url }}" target="_blank" rel="noopener" class="hover:text-white">{{ $label }}</a>@endif
                    @endforeach
                </div>
            </aside>
            <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8">
                @include('public.submissions._form', [
                    'action' => route('public.contact.store', app()->getLocale()),
                    'formType' => 'contact',
                ])
            </div>
        </div>
        @if ($siteSettings?->map_url)
            <iframe src="{{ $siteSettings->map_url }}" title="Piramida location" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="mt-8 h-96 w-full rounded-3xl border-0"></iframe>
        @endif
    </section>
</x-layouts.public>
