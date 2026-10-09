<div role="group" aria-label="{{ $label }}" data-gallery-picker @if($single) data-single-image @endif data-field-name="{{ $name }}" data-library-url="{{ route('admin.media.picker') }}" data-upload-url="{{ route('admin.media.store') }}">
    <p class="mb-2 text-sm font-medium">{{ $label }}</p>
    @unless($single)<input type="hidden" name="gallery_media_ids" value="">@endunless
    <div data-gallery-selected class="{{ $single ? 'image-picker-selected' : 'gallery-picker-grid' }}">
        @if($single && !$ids)<input type="hidden" name="{{ $name }}" value=""><img data-image-preview hidden alt="{{ $label }} preview">@endif
        @foreach($ids as $id)
            @if($image = $images->firstWhere('id', $id))
                <div><img @if($single) data-image-preview @endif src="{{ $image->displayUrl(480) }}" alt="{{ $image->original_name }}" loading="lazy"><input type="hidden" name="{{ $single ? $name : 'gallery_media_ids[]' }}" value="{{ $id }}"></div>
            @endif
        @endforeach
    </div>
    <button type="button" data-gallery-open class="mt-3 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium">{{ $single ? 'Set / change image' : 'Add images' }}</button>
    <p class="mt-2 text-xs text-slate-500">{{ $single ? 'Choose from Media or upload a new image. Edit image details to change its alt text.' : 'Only this record’s selected images appear here. Move images with the arrows.' }} Removal detaches an image; it does not delete the file. Save the record to apply changes.</p>
    <p data-gallery-status role="status" class="mt-2 text-sm text-red-700"></p>
    @error($name)<p class="text-sm text-red-600">{{ $message }}</p>@enderror
    @error($name.'.*')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
    <dialog data-gallery-dialog class="gallery-picker-dialog" aria-label="Select {{ strtolower($label) }}">
        <div class="flex items-center justify-between gap-4 border-b p-5">
            <h2 class="text-xl font-semibold">Media library</h2>
            <button type="button" data-gallery-close aria-label="Close media library" class="rounded border px-3 py-2">Close</button>
        </div>
        <div class="space-y-4 p-5">
            <label class="block text-sm">Search images<input data-gallery-search type="search" class="mt-1 block w-full rounded-lg border border-slate-300 p-3"></label>
            <label class="block text-sm">Upload {{ $single ? 'image' : 'images' }} (JPG, PNG, WebP, GIF; max 10 MB each)<input data-gallery-upload type="file" @unless($single) multiple @endunless accept="image/jpeg,image/png,image/webp,image/gif" class="mt-2 block w-full"></label>
            <p data-gallery-upload-status role="status" class="text-sm text-slate-600"></p>
            <div data-gallery-library class="gallery-picker-grid">
                @foreach($images as $image)
                    <label data-media-id="{{ $image->id }}" data-media-name="{{ $image->original_name }}" data-media-url="{{ $image->displayUrl(480) }}" data-media-edit="{{ route('admin.media.edit', $image) }}" class="gallery-library-item">
                        <img src="{{ $image->displayUrl(480) }}" alt="" loading="lazy">
                        {{-- Library choices are staged, not submitted until Apply updates the record field. --}}
                        <span><input type="{{ $single ? 'radio' : 'checkbox' }}" @if($single) name="picker_{{ $name }}" form="media-picker-controls" @endif value="{{ $image->id }}"> {{ $image->original_name }}</span>
                    </label>
                @endforeach
            </div>
            <button type="button" data-gallery-more hidden class="rounded border px-4 py-2">Load more images</button>
        </div>
        <div class="sticky bottom-0 flex justify-end border-t bg-white p-5"><button type="button" data-gallery-apply class="rounded-lg bg-slate-950 px-5 py-2.5 text-sm font-semibold text-white">{{ $single ? 'Use selected image' : 'Use selected images' }}</button></div>
    </dialog>
    <noscript><p class="text-sm text-amber-700">Enable JavaScript to add, remove or reorder images. Existing selections are preserved.</p></noscript>
</div>
