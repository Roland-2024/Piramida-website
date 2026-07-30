<x-layouts.admin title="Media">
    <div class="mb-6 flex flex-wrap justify-between gap-4"><div><h2 class="text-2xl font-semibold">Media library</h2><p class="mt-1 text-sm text-slate-500">Reusable images and PDF documents.</p></div><a href="{{ route('admin.media.create') }}" class="rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white">Upload media</a></div>
    @error('media')<div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $message }}</div>@enderror
    <form method="GET" class="mb-5 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 sm:grid-cols-4">
        <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search filename" class="rounded-lg border border-slate-300 px-3 py-2.5 text-sm sm:col-span-2">
        <select name="type" class="rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm"><option value="">All file types</option><option value="image" @selected(($filters['type'] ?? '') === 'image')>Images</option><option value="document" @selected(($filters['type'] ?? '') === 'document')>Documents</option></select>
        <div class="flex gap-2"><select name="trashed" class="min-w-0 flex-1 rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm"><option value="">Current</option><option value="with" @selected(($filters['trashed'] ?? '') === 'with')>With trash</option><option value="only" @selected(($filters['trashed'] ?? '') === 'only')>Trash only</option></select><button class="rounded-lg border border-slate-300 px-3 text-sm">Filter</button></div>
    </form>
    @if ($mediaItems->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center text-sm text-slate-500">No media files match the filters.</div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($mediaItems as $media)
                <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex h-44 items-center justify-center bg-slate-100">
                        @if (str_starts_with($media->mime_type, 'image/'))<img src="{{ $media->url() }}" alt="{{ $media->alt_text_al }}" class="h-full w-full object-cover">@else<span class="text-sm font-semibold text-slate-500">PDF</span>@endif
                    </div>
                    <div class="p-4"><p class="truncate text-sm font-medium" title="{{ $media->original_name }}">{{ $media->original_name }}</p><p class="mt-1 text-xs text-slate-500">{{ number_format($media->size / 1024, 1) }} KB</p>
                        <div class="mt-4 flex items-center gap-3 text-sm">
                            @if ($media->trashed())
                                @can('restore', $media)<form method="POST" action="{{ route('admin.media.restore', $media->id) }}">@csrf<button class="font-medium text-emerald-700">Restore</button></form>@endcan
                                @can('forceDelete', $media)<form method="POST" action="{{ route('admin.media.force-destroy', $media->id) }}" data-confirm="Permanently delete this file? This cannot be undone.">@csrf @method('DELETE')<button class="font-medium text-red-700">Delete permanently</button></form>@endcan
                            @else
                                <a href="{{ route('admin.media.edit', $media) }}" class="font-medium text-amber-700">Edit</a>
                                @can('delete', $media)<form method="POST" action="{{ route('admin.media.destroy', $media) }}" data-confirm="Move this file to trash?">@csrf @method('DELETE')<button class="font-medium text-red-700">Trash</button></form>@endcan
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
        <div class="mt-5">{{ $mediaItems->links() }}</div>
    @endif
</x-layouts.admin>
