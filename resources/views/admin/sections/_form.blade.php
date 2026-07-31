@php $editing = isset($section); @endphp

<div class="grid gap-5 lg:grid-cols-3">
    <div>
        <label for="page_id" class="block text-sm font-medium">Parent page</label>
        <select id="page_id" name="page_id" required class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm">
            @foreach ($pages as $pageOption)
                <option value="{{ $pageOption->id }}" @selected((string) old('page_id', $section->page_id ?? $selectedPageId ?? '') === (string) $pageOption->id)>{{ $pageOption->translation('al')?->title }}</option>
            @endforeach
        </select>
        @error('page_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="internal_name" class="block text-sm font-medium">Internal name</label>
        <input id="internal_name" name="internal_name" value="{{ old('internal_name', $section->internal_name ?? '') }}" required class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        @error('internal_name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="type" class="block text-sm font-medium">Section type</label>
        <select id="type" name="type" required class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm">
            @foreach ($sectionTypes as $type)
                <option value="{{ $type->value }}" @selected(old('type', $section->type->value ?? 'custom') === $type->value)>{{ $type->label() }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="display_order" class="block text-sm font-medium">Display order</label>
        <input id="display_order" name="display_order" type="number" min="0" value="{{ old('display_order', $section->display_order ?? 0) }}" required class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
    </div>
    <div class="flex items-end">
        <label class="flex w-full items-center gap-3 rounded-lg border border-slate-200 px-4 py-3">
            <input type="hidden" name="is_active" value="0">
            <input name="is_active" type="checkbox" value="1" @checked((bool) old('is_active', $section->is_active ?? true)) class="rounded border-slate-300 text-amber-500">
            <span class="text-sm font-medium">Active section</span>
        </label>
    </div>
    <div></div>
    <div class="lg:col-span-2">
        <label for="gallery_media_ids" class="block text-sm font-medium">Gallery / partner logos</label>
        <select id="gallery_media_ids" name="gallery_media_ids[]" multiple size="6" class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm">
            @foreach ($mediaItems as $media)
                <option value="{{ $media->id }}" @selected(in_array((string) $media->id, array_map('strval', old('gallery_media_ids', isset($section) ? $section->gallery->pluck('id')->all() : [])), true))>{{ $media->original_name }}</option>
            @endforeach
        </select>
        <p class="mt-2 text-xs text-slate-500">Used for Gallery sections and the partner logos in the design.</p>
    </div>
    <div>
        <label for="video_url" class="block text-sm font-medium">Video URL</label>
        <input id="video_url" name="video_url" type="url" value="{{ old('video_url', $section->video_url ?? '') }}" placeholder="https://…" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        <p class="mt-2 text-xs text-slate-500">For the About video section. Primary image is used as its poster.</p>
        @error('video_url')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="primary_media_id" class="block text-sm font-medium">Primary image</label>
        <select id="primary_media_id" name="primary_media_id" class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm">
            <option value="">No image</option>
            @foreach ($mediaItems as $media)<option value="{{ $media->id }}" @selected((string) old('primary_media_id', $section->primary_media_id ?? '') === (string) $media->id)>{{ $media->original_name }}</option>@endforeach
        </select>
    </div>
    <div>
        <label for="secondary_media_id" class="block text-sm font-medium">Secondary image</label>
        <select id="secondary_media_id" name="secondary_media_id" class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm">
            <option value="">No image</option>
            @foreach ($mediaItems as $media)<option value="{{ $media->id }}" @selected((string) old('secondary_media_id', $section->secondary_media_id ?? '') === (string) $media->id)>{{ $media->original_name }}</option>@endforeach
        </select>
    </div>
    <div></div>
    <div>
        <label for="primary_button_url" class="block text-sm font-medium">Primary button URL</label>
        <input id="primary_button_url" name="primary_button_url" value="{{ old('primary_button_url', $section->primary_button_url ?? '') }}" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
    </div>
    <div>
        <label for="secondary_button_url" class="block text-sm font-medium">Secondary button URL</label>
        <input id="secondary_button_url" name="secondary_button_url" value="{{ old('secondary_button_url', $section->secondary_button_url ?? '') }}" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
    </div>
    <div class="lg:col-span-3">
        <label for="structured_data" class="block text-sm font-medium">Optional structured data <span class="font-normal text-slate-400">(JSON)</span></label>
        <textarea id="structured_data" name="structured_data" rows="5" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 font-mono text-sm">{{ old('structured_data', isset($section) && $section->structured_data ? json_encode($section->structured_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '') }}</textarea>
        @error('structured_data') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-8 space-y-5">
    @foreach ($locales as $locale => $localeName)
        @php $translation = isset($section) ? $section->translations->firstWhere('locale', $locale) : null; @endphp
        <details open class="rounded-xl border border-slate-200">
            <summary class="cursor-pointer px-5 py-4 font-semibold">{{ $localeName }} <span class="text-xs font-normal uppercase text-slate-400">{{ $locale }}</span></summary>
            <div class="grid gap-5 border-t border-slate-200 p-5 lg:grid-cols-2">
                <div><label class="block text-sm font-medium">Title</label><input name="translations[{{ $locale }}][title]" value="{{ old("translations.{$locale}.title", $translation?->title) }}" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium">Subtitle</label><input name="translations[{{ $locale }}][subtitle]" value="{{ old("translations.{$locale}.subtitle", $translation?->subtitle) }}" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm"></div>
                <div class="lg:col-span-2"><label class="block text-sm font-medium">Description / body</label><textarea data-rich-text name="translations[{{ $locale }}][description]" rows="7" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 font-mono text-sm">{{ app(\App\Support\RichTextSanitizer::class)->sanitize(old("translations.{$locale}.description", $translation?->description)) }}</textarea></div>
                <div><label class="block text-sm font-medium">Primary button label</label><input name="translations[{{ $locale }}][primary_button_label]" value="{{ old("translations.{$locale}.primary_button_label", $translation?->primary_button_label) }}" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium">Secondary button label</label><input name="translations[{{ $locale }}][secondary_button_label]" value="{{ old("translations.{$locale}.secondary_button_label", $translation?->secondary_button_label) }}" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm"></div>
            </div>
        </details>
    @endforeach
</div>

<div class="mt-8 flex justify-end gap-3 border-t border-slate-200 pt-6">
    <a href="{{ route('admin.sections.index') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium">Cancel</a>
    <button class="rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white" type="submit">{{ $editing ? 'Save section' : 'Create section' }}</button>
</div>
