<x-layouts.admin title="Edit event">
    <div class="mb-6"><a href="{{ route('admin.events.show', $event) }}" class="text-sm font-medium text-amber-700">← View event</a><h2 class="mt-2 text-2xl font-semibold">Edit {{ $event->translation('al')?->title }}</h2></div>
    <form method="POST" action="{{ route('admin.events.update', $event) }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">@csrf @method('PUT') @include('admin.events._form')</form>
</x-layouts.admin>
