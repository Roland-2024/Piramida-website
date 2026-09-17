<x-layouts.public :title="__('cms.events')" :language-urls="$languageUrls" :styles="['event']" body-class="event-background-page" :footer="false">
    <h1 class="sr-only">{{ __('cms.events') }}</h1>
    <nav class="template-event-filters" aria-label="{{ __('cms.events') }}">
        <a href="{{ route('public.events.index', app()->getLocale()) }}" @if($period === 'upcoming') aria-current="page" @endif>{{ __('cms.upcoming') }}</a>
        <a href="{{ route('public.events.index', [app()->getLocale(), 'period' => 'past']) }}" @if($period === 'past') aria-current="page" @endif>{{ __('cms.past') }}</a>
    </nav>
    @if ($events->isEmpty())<p class="template-empty">{{ __('cms.no_content') }}</p>@else
    <section id="eventsSection" tabindex="0" aria-label="{{ __('cms.events') }}" class="relative h-[100vh]">
        <div id="eventsTrack" class="relative w-full h-full">
            @foreach ($events->getCollection()->chunk(3) as $group)
                <div class="events-slide {{ $loop->first ? 'is-active' : 'is-next' }} absolute inset-0 w-full h-full flex md:block flex-col items-center justify-center gap-4 py-20 md:py-0" @if(!$loop->first) inert @endif>
                    @foreach ($group as $event)
                        @php $item = $event->translation(app()->getLocale(), false); $offset = 2 - $loop->index; @endphp
                        <article class="slot-diagonal absolute overflow-hidden" style="right:calc(var(--card-w) * {{ $offset }});bottom:calc(var(--card-h) * {{ $offset }});left:auto;top:auto;width:var(--card-w);height:var(--card-h);">
                            <a href="{{ route('public.events.show', [app()->getLocale(), $item->slug]) }}" aria-label="{{ $item->title }}">
                                @if ($event->featuredMedia)<img src="{{ $event->featuredMedia->url() }}" alt="" class="absolute inset-0 w-full h-full object-cover">@endif
                            </a>
                            <div class="slot-title title_40">{{ $item->title }}</div>
                            <div class="slot-date title_16">{{ $event->starts_at->format('d M Y · H:i') }}</div>
                        </article>
                    @endforeach
                </div>
            @endforeach
        </div>
        <div id="eventsInfo" class="events-info" aria-hidden="true"><div class="events-info-title"></div><div class="events-info-date"></div></div>
        @if($events->count() > 3)<div class="template-event-controls"><button data-event-step="-1" aria-label="{{ __('cms.previous') }}">↑</button><button data-event-step="1" aria-label="{{ __('cms.next') }}">↓</button></div>@endif
    </section>
    <section id="eventsMobile" aria-label="{{ __('cms.events') }}">
        @foreach ($events as $event)
            @php $item = $event->translation(app()->getLocale(), false); @endphp
            <div class="reel-item">
                <div class="reel-image-wrap"><a href="{{ route('public.events.show', [app()->getLocale(), $item->slug]) }}" aria-label="{{ $item->title }}">@if($event->featuredMedia)<img src="{{ $event->featuredMedia->url() }}" alt="">@endif</a></div>
                <div class="reel-date">{{ $event->starts_at->format('d M · H:i') }}</div><div class="reel-title">{{ $item->title }}</div>
            </div>
        @endforeach
    </section>
    @endif
    <div class="template-pagination">{{ $events->links() }}</div>
</x-layouts.public>
