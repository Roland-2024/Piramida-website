<x-layouts.admin title="Page section details">
    <div class="mb-6 flex flex-wrap justify-between gap-4">
        <div><a href="{{ route('admin.sections.index') }}" class="text-sm font-medium text-amber-700">← Page sections</a><h2 class="mt-2 text-2xl font-semibold">{{ $section->internal_name }}</h2><p class="mt-1 text-sm text-slate-500">{{ $section->page->translation('al')?->title }} · {{ $section->type->label() }}</p></div>
        <div class="flex gap-2"><a href="{{ route('admin.sections.edit', $section) }}" class="rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white">Edit section</a>@can('delete', $section)<form method="POST" action="{{ route('admin.sections.destroy', $section) }}" data-confirm="Move this section to trash?">@csrf @method('DELETE')<button class="rounded-lg border border-red-300 px-4 py-2.5 text-sm text-red-700">Trash</button></form>@endcan</div>
    </div>
    <div class="grid gap-5 lg:grid-cols-3">
        <section class="rounded-2xl border border-slate-200 bg-white p-5 lg:col-span-2">
            @foreach (config('cms.locales') as $locale => $name)
                @php $translation = $section->translations->firstWhere('locale', $locale); @endphp
                <div class="{{ ! $loop->first ? 'mt-6 border-t border-slate-200 pt-6' : '' }}"><h3 class="font-semibold">{{ $name }}: {{ $translation?->title ?: 'No title' }}</h3><p class="mt-2 text-sm text-slate-600">{{ $translation?->subtitle }}</p></div>
            @endforeach
        </section>
        <aside class="rounded-2xl border border-slate-200 bg-white p-5 text-sm"><dl class="space-y-3"><div><dt class="text-slate-500">Status</dt><dd>{{ $section->is_active ? 'Active' : 'Inactive' }}</dd></div><div><dt class="text-slate-500">Display order</dt><dd>{{ $section->display_order }}</dd></div><div><dt class="text-slate-500">Updated by</dt><dd>{{ $section->updatedBy?->name ?? 'System' }}</dd></div></dl></aside>
    </div>
</x-layouts.admin>
