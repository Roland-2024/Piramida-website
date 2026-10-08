@props(['page'])
@php $slug = $page->carouselSlug(); @endphp
<p class="mb-5 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
    This page manages the carousel heading and SEO. Add, edit and order its dynamic slides in
    <a class="font-semibold underline" href="{{ $slug === 'business' ? route('admin.businesses.index') : route('admin.programs.index', ['category' => $slug === 'art' ? 'art_culture' : $slug]) }}">{{ $slug === 'business' ? 'Businesses' : 'Programs / Carousel posts' }}</a>.
    Legacy page sections are retained but no longer used.
</p>
