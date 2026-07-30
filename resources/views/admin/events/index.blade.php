<x-layouts.admin title="Events">
    <div class="mb-6 flex flex-wrap justify-between gap-4"><div><h2 class="text-2xl font-semibold">Events</h2><p class="mt-1 text-sm text-slate-500">Upcoming and past bilingual events.</p></div><a href="{{ route('admin.events.create') }}" class="rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white">Create event</a></div>
    <form method="GET" class="mb-5 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 sm:grid-cols-5">
        <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search title" class="rounded-lg border border-slate-300 px-3 py-2.5 text-sm sm:col-span-2">
        <select name="status" class="rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm"><option value="">All statuses</option><option value="draft" @selected(($filters['status'] ?? '') === 'draft')>Draft</option><option value="published" @selected(($filters['status'] ?? '') === 'published')>Published</option></select>
        <select name="period" class="rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm"><option value="">All dates</option><option value="upcoming" @selected(($filters['period'] ?? '') === 'upcoming')>Upcoming</option><option value="past" @selected(($filters['period'] ?? '') === 'past')>Past</option></select>
        <div class="flex gap-2"><select name="trashed" class="min-w-0 flex-1 rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm"><option value="">Current</option><option value="with" @selected(($filters['trashed'] ?? '') === 'with')>With trash</option><option value="only" @selected(($filters['trashed'] ?? '') === 'only')>Trash only</option></select><button class="rounded-lg border border-slate-300 px-3 text-sm">Filter</button></div>
    </form>
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
        @forelse ($events as $event)
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 last:border-0">
                <div><p class="font-medium">{{ $event->translation('al')?->title }}</p><p class="text-xs text-slate-500">{{ $event->starts_at->format('d M Y H:i') }} – {{ $event->ends_at->format('d M Y H:i') }} · {{ $event->status->label() }}</p></div>
                <div class="flex gap-3 text-sm">@if ($event->trashed()) @can('restore', $event)<form method="POST" action="{{ route('admin.events.restore', $event->id) }}">@csrf<button class="font-medium text-emerald-700">Restore</button></form>@endcan @else<a href="{{ route('admin.events.show', $event) }}" class="font-medium text-amber-700">View</a><a href="{{ route('admin.events.edit', $event) }}" class="font-medium">Edit</a>@endif</div>
            </div>
        @empty
            <p class="p-12 text-center text-sm text-slate-500">No events match the filters.</p>
        @endforelse
        @if ($events->hasPages())<div class="border-t border-slate-200 px-5 py-4">{{ $events->links() }}</div>@endif
    </div>
</x-layouts.admin>
