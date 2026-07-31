<x-layouts.public :title="$translation->seo_title ?: $translation->title" :description="$translation->seo_description ?: $translation->short_description" :language-urls="$languageUrls">
    <article class="mx-auto max-w-4xl px-5 py-16 lg:px-8">
        <p class="text-sm font-semibold text-amber-700">{{ $event->starts_at->format('d M Y · H:i') }} – {{ $event->ends_at->format('d M Y · H:i') }}</p>
        <h1 class="mt-4 text-4xl font-semibold tracking-tight sm:text-5xl">{{ $translation->title }}</h1>
        <p class="mt-4 text-lg text-slate-600">{{ $translation->location }}</p>
        <dl class="mt-6 flex flex-wrap gap-3 text-sm">
            @if ($event->capacity)<div class="rounded-full bg-slate-100 px-4 py-2"><dt class="inline font-semibold">{{ __('cms.capacity') }}:</dt> <dd class="inline">{{ $event->capacity }}</dd></div>@endif
            @if ($translation->price_label)<div class="rounded-full bg-slate-100 px-4 py-2"><dt class="sr-only">{{ __('cms.price') }}</dt><dd>{{ $translation->price_label }}</dd></div>@endif
        </dl>
        @if ($event->featuredMedia)<img src="{{ $event->featuredMedia->url() }}" alt="" class="mt-9 max-h-[32rem] w-full rounded-3xl object-cover">@endif
        <div class="prose-content mt-10 text-slate-700">{!! $translation->description !!}</div>

        @if ($event->booking_mode->allowsExternal() && $event->external_url)
            <a href="{{ $event->external_url }}" target="_blank" rel="noopener" class="mt-8 inline-block rounded-lg border border-slate-950 px-5 py-3 text-sm font-semibold">Open external registration</a>
        @endif

        @if ($event->booking_mode->allowsInternal())
            <section class="mt-12 rounded-3xl border border-slate-200 bg-white p-6 sm:p-8">
                <h2 class="text-2xl font-semibold">{{ __('cms.join_us') }}</h2>
                <p class="mt-2 text-sm text-slate-500">{{ __('cms.request_confirmation_notice') }}</p>
                @include('public.submissions._form', [
                    'action' => route('public.events.request', [app()->getLocale(), $translation->slug]),
                    'formType' => 'event',
                ])
            </section>
        @endif
    </article>
</x-layouts.public>
