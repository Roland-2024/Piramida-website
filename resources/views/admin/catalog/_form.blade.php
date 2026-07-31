@php
    $editing = isset($item);
    $titleField = $translationTitleColumn;
    $currentItem = $item ?? null;
    $fieldValue = function (array $field) use ($editing, $currentItem) {
        $value = old($field['name'], $editing ? data_get($currentItem, $field['name']) : ($field['default'] ?? ''));

        if ($value instanceof \BackedEnum) {
            return $value->value;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d\TH:i');
        }

        return $value;
    };
@endphp

<div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
    <div>
        <label for="status" class="block text-sm font-medium">Status</label>
        <select id="status" name="status" required class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm">
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(old('status', $item->status->value ?? 'draft') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
        @error('status') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="published_at" class="block text-sm font-medium">Publication date</label>
        <input id="published_at" name="published_at" type="datetime-local" value="{{ old('published_at', $editing ? $item->published_at?->format('Y-m-d\TH:i') : '') }}" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        @error('published_at') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="display_order" class="block text-sm font-medium">Display order</label>
        <input id="display_order" name="display_order" type="number" min="0" value="{{ old('display_order', $item->display_order ?? 0) }}" required class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        @error('display_order') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    @foreach ($globalFields as $field)
        @php
            $value = $fieldValue($field);
        @endphp
        <div class="{{ ($field['type'] ?? 'text') === 'checkbox' ? 'flex items-end' : '' }}">
            @if (($field['type'] ?? 'text') === 'select')
                <label for="{{ $field['name'] }}" class="block text-sm font-medium">{{ $field['label'] }}</label>
                <select id="{{ $field['name'] }}" name="{{ $field['name'] }}" @required($field['required'] ?? false) class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm">
                    @foreach ($field['options'] as $optionValue => $optionLabel)
                        <option value="{{ $optionValue }}" @selected((string) $value === (string) $optionValue)>{{ $optionLabel }}</option>
                    @endforeach
                </select>
            @elseif (($field['type'] ?? 'text') === 'checkbox')
                <label class="flex w-full items-center gap-3 rounded-lg border border-slate-200 px-4 py-3">
                    <input type="hidden" name="{{ $field['name'] }}" value="0">
                    <input name="{{ $field['name'] }}" type="checkbox" value="1" @checked((bool) $value) class="rounded border-slate-300 text-amber-500">
                    <span class="text-sm font-medium">{{ $field['label'] }}</span>
                </label>
            @elseif (($field['type'] ?? 'text') === 'textarea')
                <label for="{{ $field['name'] }}" class="block text-sm font-medium">{{ $field['label'] }}</label>
                <textarea id="{{ $field['name'] }}" name="{{ $field['name'] }}" rows="3" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">{{ $value }}</textarea>
            @else
                <label for="{{ $field['name'] }}" class="block text-sm font-medium">{{ $field['label'] }}</label>
                <input id="{{ $field['name'] }}" name="{{ $field['name'] }}" type="{{ $field['type'] ?? 'text' }}" value="{{ $value }}" @required($field['required'] ?? false) @if(isset($field['min'])) min="{{ $field['min'] }}" @endif @if(isset($field['step'])) step="{{ $field['step'] }}" @endif class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
            @endif
            @error($field['name']) <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    @endforeach
</div>

@if ($withMedia)
    <div class="mt-6 grid gap-5 lg:grid-cols-2">
        <div>
            <label for="featured_media_id" class="block text-sm font-medium">Featured image</label>
            <select id="featured_media_id" name="featured_media_id" class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm">
                <option value="">No image</option>
                @foreach ($mediaItems as $media)
                    <option value="{{ $media->id }}" @selected((string) old('featured_media_id', $item->featured_media_id ?? '') === (string) $media->id)>{{ $media->original_name }}</option>
                @endforeach
            </select>
            @error('featured_media_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="gallery_media_ids" class="block text-sm font-medium">Gallery images</label>
            @php
                $selectedGallery = collect(old('gallery_media_ids', $editing ? $item->gallery->modelKeys() : []))
                    ->map(fn ($id) => (string) $id)
                    ->all();
            @endphp
            <select id="gallery_media_ids" name="gallery_media_ids[]" multiple size="5" class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm">
                @foreach ($mediaItems as $media)
                    <option value="{{ $media->id }}" @selected(in_array((string) $media->id, $selectedGallery, true))>{{ $media->original_name }}</option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-slate-500">Use Ctrl/Cmd to select multiple images.</p>
            @error('gallery_media_ids.*') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>
@endif

<div class="mt-8 space-y-5">
    @foreach ($locales as $locale => $localeName)
        @php
            $translation = $editing ? $item->translations->firstWhere('locale', $locale) : null;
        @endphp
        <details open class="rounded-xl border border-slate-200">
            <summary class="cursor-pointer px-5 py-4 font-semibold">{{ $localeName }} <span class="text-xs font-normal uppercase text-slate-400">{{ $locale }}</span></summary>
            <div class="grid gap-5 border-t border-slate-200 p-5 lg:grid-cols-2">
                <div>
                    <label for="{{ $titleField }}_{{ $locale }}" class="block text-sm font-medium">{{ $titleField === 'name' ? 'Name' : 'Title' }}</label>
                    <input id="{{ $titleField }}_{{ $locale }}" name="translations[{{ $locale }}][{{ $titleField }}]" value="{{ old("translations.{$locale}.{$titleField}", data_get($translation, $titleField)) }}" required class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                    @error("translations.{$locale}.{$titleField}") <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="slug_{{ $locale }}" class="block text-sm font-medium">Slug</label>
                    <input id="slug_{{ $locale }}" name="translations[{{ $locale }}][slug]" value="{{ old("translations.{$locale}.slug", $translation?->slug) }}" placeholder="Generated when blank" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                    @error("translations.{$locale}.slug") <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                @foreach ($translationFields as $field)
                    @php
                        $name = "translations.{$locale}.{$field['name']}";
                        $value = old($name, data_get($translation, $field['name']));
                        $wide = in_array($field['type'], ['textarea', 'richtext'], true);
                    @endphp
                    <div class="{{ $wide ? 'lg:col-span-2' : '' }}">
                        <label for="{{ $field['name'] }}_{{ $locale }}" class="block text-sm font-medium">{{ $field['label'] }}</label>
                        @if ($field['type'] === 'textarea')
                            <textarea id="{{ $field['name'] }}_{{ $locale }}" name="translations[{{ $locale }}][{{ $field['name'] }}]" rows="3" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">{{ $value }}</textarea>
                        @elseif ($field['type'] === 'richtext')
                            <textarea data-rich-text id="{{ $field['name'] }}_{{ $locale }}" name="translations[{{ $locale }}][{{ $field['name'] }}]" rows="9" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 font-mono text-sm">{{ app(\App\Support\RichTextSanitizer::class)->sanitize($value) }}</textarea>
                        @else
                            <input id="{{ $field['name'] }}_{{ $locale }}" name="translations[{{ $locale }}][{{ $field['name'] }}]" type="{{ $field['type'] }}" value="{{ $value }}" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                        @endif
                        @error($name) <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                @endforeach
            </div>
        </details>
    @endforeach
</div>

<div class="mt-8 flex items-center justify-end gap-3 border-t border-slate-200 pt-6">
    <a href="{{ route("{$routePrefix}.index") }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium hover:bg-slate-50">Cancel</a>
    <button type="submit" class="rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">{{ $editing ? "Save {$singular}" : "Create {$singular}" }}</button>
</div>
