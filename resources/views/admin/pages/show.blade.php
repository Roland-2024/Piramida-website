<x-layouts.admin title="Page details">
    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div>
            <a href="{{ route('admin.pages.index') }}" class="text-sm font-medium text-amber-700">← Pages</a>
            <h2 class="mt-2 text-2xl font-semibold">{{ $page->translation('al')?->title }}</h2>
            <p class="mt-1 text-sm text-slate-500">{{ $page->translation('en')?->title }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.sections.create', ['page_id' => $page->id]) }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium">Add section</a>
            <a href="{{ route('admin.pages.edit', $page) }}" class="rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white">Edit page</a>
            @can('delete', $page)
                <form method="POST" action="{{ route('admin.pages.destroy', $page) }}" data-confirm="Move this page to trash?">
                    @csrf @method('DELETE')
                    <button class="rounded-lg border border-red-300 px-4 py-2.5 text-sm font-medium text-red-700">Trash</button>
                </form>
            @endcan
        </div>
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        <section class="rounded-2xl border border-slate-200 bg-white p-5 lg:col-span-2">
            @foreach (config('cms.locales') as $locale => $name)
                @php $translation = $page->translations->firstWhere('locale', $locale); @endphp
                <div class="{{ ! $loop->first ? 'mt-6 border-t border-slate-200 pt-6' : '' }}">
                    <h3 class="font-semibold">{{ $name }}</h3>
                    <p class="mt-1 text-xs text-slate-500">/{{ $locale }}/{{ $translation?->slug }}</p>
                    <p class="mt-3 text-sm text-slate-600">{{ $translation?->short_description }}</p>
                </div>
            @endforeach
        </section>
        <aside class="rounded-2xl border border-slate-200 bg-white p-5 text-sm">
            <dl class="space-y-3">
                <div><dt class="text-slate-500">Status</dt><dd class="font-medium">{{ $page->status->label() }}</dd></div>
                <div><dt class="text-slate-500">Publication</dt><dd>{{ $page->published_at?->format('d M Y H:i') ?? 'Not scheduled' }}</dd></div>
                <div><dt class="text-slate-500">Display order</dt><dd>{{ $page->display_order }}</dd></div>
                <div><dt class="text-slate-500">Updated by</dt><dd>{{ $page->updatedBy?->name ?? 'System' }}</dd></div>
            </dl>
        </aside>
    </div>

    <section class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
            <h3 class="font-semibold">Sections</h3>
            <span class="text-sm text-slate-500">{{ $page->sections->count() }} total</span>
        </div>
        @forelse ($page->sections as $section)
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 last:border-0">
                <div><p class="font-medium">{{ $section->internal_name }}</p><p class="text-xs text-slate-500">{{ $section->type->label() }} · order {{ $section->display_order }}</p></div>
                <a href="{{ route('admin.sections.show', $section) }}" class="text-sm font-medium text-amber-700">View</a>
            </div>
        @empty
            <p class="p-8 text-center text-sm text-slate-500">No sections have been added.</p>
        @endforelse
    </section>
</x-layouts.admin>
