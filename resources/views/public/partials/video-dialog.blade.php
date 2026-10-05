<dialog id="{{ $id }}" class="homepage-video-dialog" data-video-dialog aria-label="{{ __('cms.play_video') }}">
    <a class="homepage-video-fallback" href="{{ $url }}" target="_blank" rel="noopener noreferrer">{{ $youtubeId ? 'YouTube' : __('cms.play_video') }} ↗</a>
    <form method="dialog"><button class="homepage-video-close" aria-label="{{ __('cms.close') }}">&times;</button></form>
    @if($youtubeId)
        <iframe data-src="https://www.youtube-nocookie.com/embed/{{ $youtubeId }}?autoplay=1" title="{{ __('cms.play_video') }}" allow="autoplay; encrypted-media; picture-in-picture" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
    @else
        <video data-src="{{ $url }}" controls playsinline preload="none" aria-label="{{ __('cms.play_video') }}"></video>
    @endif
</dialog>
