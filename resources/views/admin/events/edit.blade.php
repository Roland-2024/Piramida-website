<x-layouts.admin title="Edit event">
    <div class="mb-6"><a href="{{ route('admin.events.show', $event) }}" class="text-sm font-medium text-amber-700">← View event</a><h2 class="mt-2 text-2xl font-semibold">Edit {{ $event->translation('al')?->title }}</h2></div>
    @if ($event->translations->contains(fn ($translation) => $translation->wordpress_id !== null))
        <p role="note" class="mb-6 rounded-xl border border-amber-300 bg-amber-50 p-4 text-amber-950">This event is managed in WordPress. You can edit it here, but the next synchronization may replace your changes. Make permanent changes at the source.</p>
    @endif
    <form method="POST" action="{{ route('admin.events.update', $event) }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">@csrf @method('PUT') @include('admin.events._form')</form>
</x-layouts.admin>
