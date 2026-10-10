<x-layouts.public :title="__('cms.events')" :language-urls="$languageUrls" :styles="['event']" body-class="event-background-page" :footer="false">
    <h1 class="sr-only">{{ __('cms.events') }}</h1>
    <div id="eventsArchive" data-loading="{{ __('cms.events_loading') }}" data-error="{{ __('cms.events_load_error') }}">
    @if ($events->isEmpty())<p class="template-empty">{{ __('cms.no_content') }}</p>@else
    <section id="eventsSection" tabindex="0" aria-label="{{ __('cms.events') }}" class="relative h-[100vh]">
        <div id="eventsTrack" class="relative w-full h-full">
            @foreach ($events->getCollection()->chunk(3) as $group)
                @php $reversed = (intdiv($events->firstItem() - 1, 3) + $loop->index) % 2 === 1; @endphp
                <div class="events-slide {{ $loop->first ? 'is-active' : 'is-next' }} {{ $reversed ? 'is-reversed' : '' }} absolute inset-0 w-full h-full flex md:block flex-col items-center justify-center gap-4 py-20 md:py-0" @if(!$loop->first) inert @endif>
                    @foreach ($group as $event)
                        @php $item = $event->translation(app()->getLocale(), false); $offset = 2 - $loop->index; @endphp
                        <article class="slot-diagonal absolute overflow-hidden" style="--card-offset:{{ $offset }};">
                            <a href="{{ route('public.events.show', [app()->getLocale(), $item->slug]) }}" aria-label="{{ $item->title }}">
                                @if ($event->featuredMedia)<img src="{{ $event->featuredMedia->displayUrl() }}" alt="" class="absolute inset-0 w-full h-full object-cover">@endif
                                <x-event-ended-overlay :event="$event" />
                            </a>
                            <div class="slot-title title_40">{{ $item->title }}</div>
                            <div class="slot-date title_16">{{ $event->starts_at->format('d M Y · H:i') }}</div>
                        </article>
                    @endforeach
                </div>
            @endforeach
        </div>
        <div id="eventsInfo" class="events-info" aria-hidden="true"><div class="events-info-title"></div><div class="events-info-date"></div></div>
    </section>
    <section id="eventsMobile" aria-label="{{ __('cms.events') }}">
        @foreach ($events as $event)
            @php $item = $event->translation(app()->getLocale(), false); @endphp
            <div class="reel-item">
                <div class="reel-image-wrap"><a href="{{ route('public.events.show', [app()->getLocale(), $item->slug]) }}" aria-label="{{ $item->title }}">@if($event->featuredMedia)<img src="{{ $event->featuredMedia->displayUrl() }}" alt="">@endif<x-event-ended-overlay :event="$event" /></a></div>
                <div class="reel-date">{{ $event->starts_at->format('d M · H:i') }}</div><div class="reel-title">{{ $item->title }}</div>
            </div>
        @endforeach
    </section>
    @endif
    <div class="events-load-control">
        <span id="eventsLoadStatus" role="status"></span>
        @if($events->hasMorePages())<a id="eventsLoadMore" href="{{ $events->nextPageUrl() }}" rel="next">{{ __('cms.events_load_more') }}</a>@endif
    </div>
    </div>
</x-layouts.public>
