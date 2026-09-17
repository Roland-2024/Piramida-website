@php $cardTranslation = $record->translation(app()->getLocale(), false); @endphp
<a href="{{ route($route, [app()->getLocale(), $cardTranslation->slug]) }}" class="content-card">
    @if ($record->featuredMedia)<img src="{{ $record->featuredMedia->url() }}" alt="{{ app()->getLocale() === 'en' ? $record->featuredMedia->alt_text_en : $record->featuredMedia->alt_text_al }}" loading="lazy">@endif
    <h3>{{ $cardTranslation->title ?? $cardTranslation->name }}</h3>
    @if ($record instanceof \App\Models\Event)<p class="text-xs">{{ $record->starts_at->format('d M Y') }} – {{ $record->ends_at->format('d M Y') }}</p>@endif
    <p class="muted text-sm">{{ $cardTranslation->short_description ?? $cardTranslation->excerpt }}</p>
    <span class="mt-5 inline-block text-sm">{{ __('cms.read_more') }} ↗</span>
</a>
