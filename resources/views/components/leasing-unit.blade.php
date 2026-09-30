@props(['unit', 'space' => null])
@php
    $translation = $space?->translation(app()->getLocale(), false);
    $url = $translation ? route('public.spaces.show', [app()->getLocale(), $translation->slug]) : null;
    $status = $url ? __('cms.unit_available') : __('cms.unit_unavailable');
@endphp
<g id="{{ $unit->svg_id }}" class="floor-plan-unit" data-unit="{{ $unit->code }}" data-status="{{ $url ? 'available' : 'unavailable' }}" @if (!$url) role="img" aria-label="{{ $unit->code }} — {{ $status }}" aria-disabled="true" @endif>
    <title>{{ $unit->code }} — {{ $status }}</title>
    @if ($url)<a href="{{ $url }}" aria-label="{{ $unit->code }} — {{ $status }}">@endif
    {{ $slot }}
    @if ($url)</a>@endif
</g>
