<x-layouts.admin title="Page sections">
    <div class="mb-6 flex flex-wrap justify-between gap-4">
        <div><h2 class="text-2xl font-semibold">Page sections</h2><p class="mt-1 text-sm text-slate-500">Ordered, reusable content blocks assigned to pages.</p></div>
        <a href="{{ route('admin.sections.create') }}" class="rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white">Create section</a>
    </div>
    <form method="GET" class="mb-5 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 sm:grid-cols-4">
        <select name="page_id" class="rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm"><option value="">All pages</option>@foreach ($pages as $page)<option value="{{ $page->id }}" @selected((string) ($filters['page_id'] ?? '') === (string) $page->id)>{{ $page->translation('al')?->title }}</option>@endforeach</select>
        <select name="type" class="rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm"><option value="">All types</option>@foreach ($sectionTypes as $type)<option value="{{ $type->value }}" @selected(($filters['type'] ?? '') === $type->value)>{{ $type->label() }}</option>@endforeach</select>
        <select name="status" class="rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm"><option value="">All statuses</option><option value="active" @selected(($filters['status'] ?? '') === 'active')>Active</option><option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Inactive</option></select>
        <div class="flex gap-2"><select name="trashed" class="min-w-0 flex-1 rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm"><option value="">Current</option><option value="with" @selected(($filters['trashed'] ?? '') === 'with')>With trash</option><option value="only" @selected(($filters['trashed'] ?? '') === 'only')>Trash only</option></select><button class="rounded-lg border border-slate-300 px-3 text-sm" type="submit">Filter</button></div>
    </form>
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
        @forelse ($sections as $section)
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 last:border-0">
                <div><p class="font-medium">{{ $section->internal_name }}</p><p class="text-xs text-slate-500">{{ $section->page?->translation('al')?->title }} · {{ $section->type->label() }} · order {{ $section->display_order }}</p></div>
                <div class="flex items-center gap-3 text-sm"><span class="{{ $section->is_active ? 'text-emerald-700' : 'text-slate-400' }}">{{ $section->is_active ? 'Active' : 'Inactive' }}</span>
                    @if ($section->trashed()) @can('restore', $section)<form method="POST" action="{{ route('admin.sections.restore', $section->id) }}">@csrf<button class="font-medium text-emerald-700">Restore</button></form>@endcan
                    @else <a href="{{ route('admin.sections.show', $section) }}" class="font-medium text-amber-700">View</a><a href="{{ route('admin.sections.edit', $section) }}" class="font-medium">Edit</a>@endif
                </div>
            </div>
        @empty
            <p class="p-12 text-center text-sm text-slate-500">No page sections match the filters.</p>
        @endforelse
        @if ($sections->hasPages())<div class="border-t border-slate-200 px-5 py-4">{{ $sections->links() }}</div>@endif
    </div>
</x-layouts.admin>
