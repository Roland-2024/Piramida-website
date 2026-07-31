@php
    $title = data_get($translation, $translationTitleColumn);
    $bookingMode = data_get($item, 'booking_mode');
@endphp

<x-layouts.public :title="$translation->seo_title ?: $title" :description="$translation->seo_description ?: $translation->short_description" :language-urls="$languageUrls">
    <article class="mx-auto max-w-4xl px-5 py-16 lg:px-8">
        @if (isset($item->category) && $item->category instanceof \BackedEnum)<p class="text-sm font-semibold uppercase text-amber-700">{{ $item->category->label() }}</p>@endif
        @if (isset($item->type) && $item->type instanceof \BackedEnum)<p class="text-sm font-semibold uppercase text-amber-700">{{ $item->type->label() }}</p>@endif
        <h1 class="mt-3 text-4xl font-semibold tracking-tight sm:text-5xl">{{ $title }}</h1>
        @if ($translation->location)<p class="mt-4 text-lg text-slate-600">{{ $translation->location }}</p>@endif
        @if (method_exists($item, 'featuredMedia') && $item->featuredMedia)<img src="{{ $item->featuredMedia->url() }}" alt="" class="mt-9 max-h-[32rem] w-full rounded-3xl object-cover">@endif
        <div class="prose-content mt-10 text-slate-700">{!! $translation->description !!}</div>
        @if ($translation->requirements)<div class="prose-content mt-8 rounded-2xl bg-white p-6"><h2 class="mb-3 text-xl font-semibold">Requirements</h2>{!! $translation->requirements !!}</div>@endif
        @if ($translation->features)<div class="prose-content mt-8 rounded-2xl bg-white p-6"><h2 class="mb-3 text-xl font-semibold">Features</h2>{!! $translation->features !!}</div>@endif

        @if ($bookingMode?->allowsExternal() && $item->external_url)
            <a href="{{ $item->external_url }}" target="_blank" rel="noopener" class="mt-8 inline-block rounded-lg border border-slate-950 px-5 py-3 text-sm font-semibold">Open external form</a>
        @endif

        @if ($bookingMode?->allowsInternal())
            <section class="mt-12 rounded-3xl border border-slate-200 bg-white p-6 sm:p-8">
                <h2 class="text-2xl font-semibold">{{ $item instanceof \App\Models\Career ? __('cms.apply') : __('cms.send_request') }}</h2>
                <p class="mt-2 text-sm text-slate-500">{{ __('cms.request_confirmation_notice') }}</p>
                @include('public.submissions._form', [
                    'action' => $item instanceof \App\Models\Program
                        ? route('public.programs.request', [app()->getLocale(), $translation->slug])
                        : ($item instanceof \App\Models\Space
                            ? route('public.spaces.request', [app()->getLocale(), $translation->slug])
                            : route('public.careers.apply', [app()->getLocale(), $translation->slug])),
                    'formType' => $item instanceof \App\Models\Career ? 'career' : ($item instanceof \App\Models\Space ? 'space' : 'program'),
                ])
            </section>
        @endif
    </article>
</x-layouts.public>
