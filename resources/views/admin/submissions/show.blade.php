<x-layouts.admin title="Submission details">
    <div class="mb-6">
        <a href="{{ route('admin.submissions.index') }}" class="text-sm font-medium text-amber-700">← Submissions</a>
        <h2 class="mt-2 text-2xl font-semibold">{{ $submission->name }}</h2>
        <p class="mt-1 text-sm text-slate-500">{{ $submission->type->label() }} · {{ $submission->created_at->format('d M Y H:i') }}</p>
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        <section class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6 lg:col-span-2">
            <div class="grid gap-4 sm:grid-cols-2">
                <div><p class="text-xs uppercase tracking-wide text-slate-400">Email</p><a class="mt-1 block font-medium text-amber-700" href="mailto:{{ $submission->email }}">{{ $submission->email }}</a></div>
                <div><p class="text-xs uppercase tracking-wide text-slate-400">Phone</p><p class="mt-1 font-medium">{{ $submission->phone ?: '—' }}</p></div>
            </div>
            @if ($submission->subject)<div><p class="text-xs uppercase tracking-wide text-slate-400">Subject</p><p class="mt-1">{{ $submission->subject }}</p></div>@endif
            @if ($submission->message)<div><p class="text-xs uppercase tracking-wide text-slate-400">Message</p><p class="mt-1 whitespace-pre-line text-sm text-slate-700">{{ $submission->message }}</p></div>@endif
            @if ($submission->details)
                <div><p class="text-xs uppercase tracking-wide text-slate-400">Request details</p><dl class="mt-2 divide-y divide-slate-100 rounded-lg border border-slate-200">@foreach ($submission->details as $label => $value)<div class="grid grid-cols-2 gap-3 px-4 py-2 text-sm"><dt class="font-medium">{{ str($label)->replace('_', ' ')->title() }}</dt><dd>{{ is_array($value) ? implode(', ', $value) : $value }}</dd></div>@endforeach</dl></div>
            @endif
            @if ($submission->hasAttachment())<a href="{{ route('admin.submissions.download', $submission) }}" class="inline-flex rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium">Download {{ $submission->attachment_name }}</a>@endif
        </section>

        <form method="POST" action="{{ route('admin.submissions.update', $submission) }}" class="h-fit rounded-2xl border border-slate-200 bg-white p-5">
            @csrf
            @method('PUT')
            <label for="status" class="block text-sm font-medium">Status</label>
            <select id="status" name="status" class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm">@foreach ($statuses as $status)<option value="{{ $status->value }}" @selected(old('status', $submission->status->value) === $status->value)>{{ $status->label() }}</option>@endforeach</select>
            <label for="internal_notes" class="mt-5 block text-sm font-medium">Internal notes</label>
            <textarea id="internal_notes" name="internal_notes" rows="8" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">{{ old('internal_notes', $submission->internal_notes) }}</textarea>
            <button class="mt-5 w-full rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white">Save review</button>
        </form>
    </div>
</x-layouts.admin>
