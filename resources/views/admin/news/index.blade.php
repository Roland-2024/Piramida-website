<x-layouts.admin title="News">
    <div class="mb-6 flex flex-wrap justify-between gap-4"><div><h2 class="text-2xl font-semibold">News</h2><p class="mt-1 text-sm text-slate-500">Bilingual articles with scheduled publication.</p></div><a href="{{ route('admin.news.create') }}" class="rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white">Create article</a></div>
    <form method="GET" class="mb-5 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 lg:grid-cols-6">
        <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search title" class="rounded-lg border border-slate-300 px-3 py-2.5 text-sm lg:col-span-2">
        <select name="status" class="rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm"><option value="">All statuses</option><option value="draft" @selected(($filters['status'] ?? '') === 'draft')>Draft</option><option value="published" @selected(($filters['status'] ?? '') === 'published')>Published</option></select>
        <input name="date_from" type="date" value="{{ $filters['date_from'] ?? '' }}" class="rounded-lg border border-slate-300 px-3 py-2.5 text-sm" aria-label="Published from">
        <input name="date_to" type="date" value="{{ $filters['date_to'] ?? '' }}" class="rounded-lg border border-slate-300 px-3 py-2.5 text-sm" aria-label="Published to">
        <div class="flex gap-2"><select name="trashed" class="min-w-0 flex-1 rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm"><option value="">Current</option><option value="with" @selected(($filters['trashed'] ?? '') === 'with')>With trash</option><option value="only" @selected(($filters['trashed'] ?? '') === 'only')>Trash only</option></select><button class="rounded-lg border border-slate-300 px-3 text-sm">Filter</button></div>
    </form>
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
        @forelse ($articles as $article)
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 last:border-0">
                <div><p class="font-medium">{{ $article->translation('al')?->title }}</p><p class="text-xs text-slate-500">{{ $article->published_at?->format('d M Y H:i') ?? 'Not scheduled' }} · {{ $article->status->label() }}</p></div>
                <div class="flex gap-3 text-sm">@if ($article->trashed()) @can('restore', $article)<form method="POST" action="{{ route('admin.news.restore', $article->id) }}">@csrf<button class="font-medium text-emerald-700">Restore</button></form>@endcan @else<a href="{{ route('admin.news.show', $article) }}" class="font-medium text-amber-700">View</a><a href="{{ route('admin.news.edit', $article) }}" class="font-medium">Edit</a>@endif</div>
            </div>
        @empty
            <p class="p-12 text-center text-sm text-slate-500">No news articles match the filters.</p>
        @endforelse
        @if ($articles->hasPages())<div class="border-t border-slate-200 px-5 py-4">{{ $articles->links() }}</div>@endif
    </div>
</x-layouts.admin>
