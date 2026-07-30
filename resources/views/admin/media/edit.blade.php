<x-layouts.admin title="Edit media">
    <div class="mb-6"><a href="{{ route('admin.media.index') }}" class="text-sm font-medium text-amber-700">← Media</a><h2 class="mt-2 text-2xl font-semibold">{{ $media->original_name }}</h2></div>
    <div class="grid gap-5 lg:grid-cols-3">
        <section class="rounded-2xl border border-slate-200 bg-white p-5">
            @if (str_starts_with($media->mime_type, 'image/'))
                <img src="{{ $media->url() }}" alt="{{ $media->alt_text_al }}" class="max-h-80 w-full rounded-lg object-contain">
            @else
                <a href="{{ $media->url() }}" target="_blank" rel="noopener" class="flex min-h-48 items-center justify-center rounded-lg bg-slate-100 text-sm font-medium text-amber-700">Open PDF</a>
            @endif
            <dl class="mt-4 space-y-2 text-xs text-slate-500"><div><dt class="inline font-medium">Type:</dt> <dd class="inline">{{ $media->mime_type }}</dd></div><div><dt class="inline font-medium">Size:</dt> <dd class="inline">{{ number_format($media->size / 1024, 1) }} KB</dd></div>@if ($media->width)<div><dt class="inline font-medium">Dimensions:</dt> <dd class="inline">{{ $media->width }} × {{ $media->height }}</dd></div>@endif</dl>
        </section>
        <form method="POST" action="{{ route('admin.media.update', $media) }}" class="rounded-2xl border border-slate-200 bg-white p-5 lg:col-span-2">
            @csrf @method('PUT')
            <div><label for="alt_text_al" class="block text-sm font-medium">Alternative text · Shqip</label><input id="alt_text_al" name="alt_text_al" value="{{ old('alt_text_al', $media->alt_text_al) }}" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm"></div>
            <div class="mt-5"><label for="alt_text_en" class="block text-sm font-medium">Alternative text · English</label><input id="alt_text_en" name="alt_text_en" value="{{ old('alt_text_en', $media->alt_text_en) }}" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm"></div>
            <div class="mt-8 flex justify-end gap-3 border-t border-slate-200 pt-6"><a href="{{ route('admin.media.index') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium">Cancel</a><button class="rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white">Save metadata</button></div>
        </form>
    </div>
</x-layouts.admin>
