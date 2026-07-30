<x-layouts.admin title="Dashboard">
    <div class="mb-6">
        <h2 class="text-2xl font-semibold tracking-tight">Overview</h2>
        <p class="mt-1 text-sm text-slate-500">A quick view of managed content and dashboard accounts.</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['label' => 'Pages', 'value' => $metrics['pages']],
            ['label' => 'News articles', 'value' => $metrics['news']],
            ['label' => 'Events', 'value' => $metrics['events']],
            ['label' => 'Dashboard users', 'value' => $metrics['users']],
            ['label' => 'Admins', 'value' => $metrics['admins']],
            ['label' => 'Editors', 'value' => $metrics['editors']],
            ['label' => 'Published content', 'value' => $metrics['published']],
            ['label' => 'Draft content', 'value' => $metrics['drafts']],
            ['label' => 'Upcoming events', 'value' => $metrics['upcoming_events']],
        ] as $metric)
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">{{ $metric['label'] }}</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight">{{ number_format($metric['value']) }}</p>
            </article>
        @endforeach
    </div>

    <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4"><h3 class="font-semibold">Recently updated content</h3></div>
        @forelse ($recentContent as $item)
            <a href="{{ $item['url'] }}" class="flex items-center justify-between gap-4 border-b border-slate-100 px-5 py-4 last:border-0 hover:bg-slate-50">
                <div><p class="font-medium">{{ $item['title'] }}</p><p class="text-xs text-slate-500">{{ $item['type'] }}</p></div>
                <span class="text-xs text-slate-500">{{ $item['updated_at']->diffForHumans() }}</span>
            </a>
        @empty
            <p class="px-5 py-10 text-center text-sm text-slate-500">No content has been created yet.</p>
        @endforelse
    </section>

</x-layouts.admin>
