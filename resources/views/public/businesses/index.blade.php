<x-layouts.public :title="__('cms.businesses')" :language-urls="$languageUrls">
    <section class="bg-slate-950 py-16 text-white">
        <div class="mx-auto max-w-7xl px-5 lg:px-8">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-lime-300">Piramida</p>
            <h1 class="mt-3 text-4xl font-semibold tracking-tight sm:text-5xl">{{ __('cms.businesses') }}</h1>
            <p class="mt-4 max-w-2xl text-slate-300">{{ __('cms.businesses_intro') }}</p>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-5 py-12 lg:px-8">
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse ($items as $business)
                @php
                    $translation = $business->translation(app()->getLocale(), false);
                    $images = collect([$business->featuredMedia])
                        ->filter()
                        ->merge($business->gallery)
                        ->unique('id');
                    $imageAlt = fn ($media) => app()->getLocale() === 'en'
                        ? $media->alt_text_en
                        : $media->alt_text_al;
                @endphp

                <article>
                    <button type="button" onclick="document.getElementById('business-{{ $business->id }}').showModal()" aria-haspopup="dialog" class="group block w-full text-left">
                        <div class="aspect-[4/5] overflow-hidden rounded-2xl bg-slate-100">
                            @if ($business->featuredMedia)
                                <img src="{{ $business->featuredMedia->url() }}" alt="{{ $imageAlt($business->featuredMedia) }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                            @else
                                <div class="flex h-full items-center justify-center text-sm text-slate-400">{{ __('cms.no_image') }}</div>
                            @endif
                        </div>
                        <div class="mt-4 flex items-center gap-3">
                            @if ($business->logoMedia)
                                <img src="{{ $business->logoMedia->url() }}" alt="" class="size-10 rounded-full object-cover">
                            @else
                                <span class="size-10 rounded-full bg-slate-200"></span>
                            @endif
                            <span>
                                <strong class="block font-semibold">{{ $translation?->name }}</strong>
                                <span class="block text-sm text-slate-500">{{ $business->category->label() }}</span>
                            </span>
                        </div>
                    </button>

                    <dialog id="business-{{ $business->id }}" class="m-auto max-h-[calc(100vh-2rem)] w-[min(56rem,calc(100%-2rem))] overflow-y-auto rounded-3xl p-0 shadow-2xl backdrop:bg-slate-950/75">
                        <article class="bg-white p-5 sm:p-8">
                            <form method="dialog" class="flex justify-end">
                                <button type="submit" aria-label="{{ __('cms.close') }}" class="flex size-10 items-center justify-center rounded-full border border-slate-200 text-xl hover:bg-slate-50">×</button>
                            </form>

                            <header class="flex items-center gap-3">
                                @if ($business->logoMedia)
                                    <img src="{{ $business->logoMedia->url() }}" alt="" class="size-11 rounded-full object-cover">
                                @endif
                                <div>
                                    <h2 class="text-xl font-semibold">{{ $translation?->name }}</h2>
                                    <p class="text-sm text-slate-500">{{ $business->category->label() }}</p>
                                </div>
                            </header>

                            @if ($images->isNotEmpty())
                                <div class="mt-6 flex snap-x gap-4 overflow-x-auto rounded-2xl">
                                    @foreach ($images as $image)
                                        <img src="{{ $image->url() }}" alt="{{ $imageAlt($image) }}" class="aspect-[16/10] w-full shrink-0 snap-center rounded-2xl object-cover">
                                    @endforeach
                                </div>
                            @endif

                            @if ($translation?->description)
                                <div class="prose-content mt-6 text-slate-700">{!! $translation->description !!}</div>
                            @elseif ($translation?->short_description)
                                <p class="mt-6 text-slate-700">{{ $translation->short_description }}</p>
                            @endif

                            <dl class="mt-6 space-y-3 text-sm">
                                @if ($translation?->address)<div><dt class="font-semibold">{{ __('cms.location') }}</dt><dd class="text-slate-600">{{ $translation->address }}</dd></div>@endif
                                @if ($translation?->opening_hours)<div><dt class="font-semibold">{{ __('cms.opening_hours') }}</dt><dd class="whitespace-pre-line text-slate-600">{{ $translation->opening_hours }}</dd></div>@endif
                            </dl>

                            <div class="mt-7 flex flex-wrap gap-3">
                                @if ($business->website_url)<a href="{{ $business->website_url }}" target="_blank" rel="noopener" class="rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white">{{ __('cms.website') }}</a>@endif
                                @if ($business->phone)<a href="tel:{{ $business->phone }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold">{{ $business->phone }}</a>@endif
                                <a href="{{ route('public.businesses.show', [app()->getLocale(), $translation?->slug]) }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold">{{ __('cms.full_page') }}</a>
                            </div>
                        </article>
                    </dialog>
                </article>
            @empty
                <p class="text-sm text-slate-500">{{ __('cms.no_content') }}</p>
            @endforelse
        </div>

        <div class="mt-10">{{ $items->links() }}</div>
    </section>
</x-layouts.public>
