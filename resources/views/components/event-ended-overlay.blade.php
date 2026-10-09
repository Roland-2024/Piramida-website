@props(['event'])

@if ($event->ends_at->isPast())
    <span class="event-ended-overlay"><span>{{ __('cms.event_ended') }}</span></span>
@endif
