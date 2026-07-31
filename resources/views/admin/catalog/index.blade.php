<x-layouts.admin :title="$plural">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold">{{ $plural }}</h2>
            <p class="mt-1 text-sm text-slate-500">Manage bilingual {{ strtolower($plural) }} and publication state.</p>
        </div>
        <a href="{{ route("{$routePrefix}.create") }}" class="rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white">Create {{ strtolower($singular) }}</a>
    </div>

    <form method="GET" class="mb-5 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 sm:grid-cols-4">
        <input name="search" type="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search translated name" class="rounded-lg border border-slate-300 px-3 py-2.5 text-sm sm:col-span-2">
        <select name="status" class="rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm">
            <option value="">All statuses</option>
            <option value="draft" @selected(($filters['status'] ?? '') === 'draft')>Draft</option>
            <option value="published" @selected(($filters['status'] ?? '') === 'published')>Published</option>
        </select>
        <div class="flex gap-2">
            <select name="trashed" class="min-w-0 flex-1 rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm">
                <option value="">Current</option>
                <option value="with" @selected(($filters['trashed'] ?? '') === 'with')>With trash</option>
                <option value="only" @selected(($filters['trashed'] ?? '') === 'only')>Trash only</option>
            </select>
            <button class="rounded-lg border border-slate-300 px-3 text-sm font-medium" type="submit">Filter</button>
        </div>
    </form>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        @if ($items->isEmpty())
            <div class="p-12 text-center text-sm text-slate-500">No {{ strtolower($plural) }} match the current filters.</div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                        <tr><th class="px-5 py-3">{{ $singular }}</th><th class="px-5 py-3">Status</th><th class="px-5 py-3">Order</th><th class="px-5 py-3">Updated</th><th class="px-5 py-3 text-right">Actions</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($items as $item)
                            <tr>
                                <td class="px-5 py-4">
                                    <p class="font-medium">{{ data_get($item->translation('al'), $translationTitleColumn) ?? 'Untitled' }}</p>
                                    <p class="text-xs text-slate-500">{{ data_get($item->translation('en'), $translationTitleColumn) }}</p>
                                </td>
                                <td class="px-5 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $item->status->value === 'published' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">{{ $item->status->label() }}</span></td>
                                <td class="px-5 py-4">{{ $item->display_order }}</td>
                                <td class="px-5 py-4 text-slate-500">{{ $item->updated_at->diffForHumans() }}</td>
                                <td class="px-5 py-4 text-right">
                                    @if ($item->trashed())
                                        @can('restore', $item)
                                            <form method="POST" action="{{ route("{$routePrefix}.restore", ['id' => $item->id]) }}" class="inline">
                                                @csrf
                                                <button class="font-medium text-emerald-700">Restore</button>
                                            </form>
                                        @endcan
                                    @else
                                        <a href="{{ route("{$routePrefix}.edit", $item) }}" class="font-medium text-slate-700">Edit</a>
                                        @can('delete', $item)
                                            <form method="POST" action="{{ route("{$routePrefix}.destroy", $item) }}" class="ml-3 inline" onsubmit="return confirm('Move this item to trash?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="font-medium text-red-700">Trash</button>
                                            </form>
                                        @endcan
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-200 px-5 py-4">{{ $items->links() }}</div>
        @endif
    </div>
</x-layouts.admin>
