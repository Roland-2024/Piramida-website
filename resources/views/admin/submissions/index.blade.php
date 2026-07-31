<x-layouts.admin title="Submissions">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold">Submissions</h2>
            <p class="mt-1 text-sm text-slate-500">Contact, registration, application, booking, and leasing requests.</p>
        </div>
        <a href="{{ route('admin.submissions.export', request()->query()) }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold">Export CSV</a>
    </div>

    <form method="GET" class="mb-5 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 sm:grid-cols-4">
        <input name="search" type="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search name, email, or subject" class="rounded-lg border border-slate-300 px-3 py-2.5 text-sm sm:col-span-2">
        <select name="type" class="rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm">
            <option value="">All types</option>
            @foreach ($types as $type)<option value="{{ $type->value }}" @selected(($filters['type'] ?? '') === $type->value)>{{ $type->label() }}</option>@endforeach
        </select>
        <div class="flex gap-2">
            <select name="status" class="min-w-0 flex-1 rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm">
                <option value="">All statuses</option>
                @foreach ($statuses as $status)<option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>{{ $status->label() }}</option>@endforeach
            </select>
            <button class="rounded-lg border border-slate-300 px-3 text-sm font-medium">Filter</button>
        </div>
    </form>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        @forelse ($submissions as $submission)
            <a href="{{ route('admin.submissions.show', $submission) }}" class="grid gap-2 border-b border-slate-100 px-5 py-4 last:border-0 hover:bg-slate-50 md:grid-cols-5 md:items-center">
                <div class="md:col-span-2"><p class="font-medium">{{ $submission->name }}</p><p class="text-xs text-slate-500">{{ $submission->email }}</p></div>
                <p class="text-sm">{{ $submission->type->label() }}</p>
                <p><span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium">{{ $submission->status->label() }}</span></p>
                <p class="text-sm text-slate-500 md:text-right">{{ $submission->created_at->diffForHumans() }}</p>
            </a>
        @empty
            <p class="p-12 text-center text-sm text-slate-500">No submissions match the current filters.</p>
        @endforelse
        @if ($submissions->hasPages())<div class="border-t border-slate-200 px-5 py-4">{{ $submissions->links() }}</div>@endif
    </div>
</x-layouts.admin>
