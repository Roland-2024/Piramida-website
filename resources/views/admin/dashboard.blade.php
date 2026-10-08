<x-layouts.admin title="Dashboard">
    <section class="admin-welcome mb-7">
        <div>
            <p class="mb-3 text-xs font-semibold uppercase tracking-widest">Piramida · Content studio</p>
            <h2 class="text-3xl font-semibold tracking-tight sm:text-4xl">Make space for your next story.</h2>
            <p class="mt-3 max-w-xl text-sm leading-6 text-slate-300">Manage your pages, share what’s happening and keep Piramida up to date. Everything you need, in one place.</p>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('admin.news.create') }}" class="admin-primary-link">+ Create article</a>
                <a href="{{ route('admin.media.index') }}" class="rounded-xl border border-white/25 px-4 py-3 text-sm font-semibold text-white hover:bg-white/10">Open media library ↗</a>
            </div>
        </div>
        <img src="{{ asset('template/images/logo piramida.svg') }}" width="180" height="80" alt="" class="hidden shrink-0 lg:block">
    </section>
    <div class="mb-4"><h2 class="text-xl font-semibold tracking-tight">Workspace overview</h2><p class="mt-1 text-sm text-slate-500">Your content and activity at a glance.</p></div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['label' => 'Pages', 'value' => $metrics['pages']],
            ['label' => 'Carousel posts', 'value' => $metrics['programs']],
            ['label' => 'News articles', 'value' => $metrics['news']],
            ['label' => 'Events', 'value' => $metrics['events']],
            ['label' => 'Attractions', 'value' => $metrics['attractions']],
            ['label' => 'Businesses', 'value' => $metrics['businesses']],
            ['label' => 'Event spaces', 'value' => $metrics['event_spaces']],
            ['label' => 'Leasing spaces', 'value' => $metrics['leasing_spaces']],
            ['label' => 'Careers', 'value' => $metrics['careers']],
            ...($metrics['new_submissions'] !== null ? [['label' => 'New submissions', 'value' => $metrics['new_submissions']]] : []),
            ['label' => 'Dashboard users', 'value' => $metrics['users']],
            ['label' => 'Admins', 'value' => $metrics['admins']],
            ['label' => 'Editors', 'value' => $metrics['editors']],
            ['label' => 'Published content', 'value' => $metrics['published']],
            ['label' => 'Draft content', 'value' => $metrics['drafts']],
            ['label' => 'Upcoming events', 'value' => $metrics['upcoming_events']],
        ] as $metric)
            <article class="admin-metric rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
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
