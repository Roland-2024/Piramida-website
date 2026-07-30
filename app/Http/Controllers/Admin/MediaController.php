<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMediaRequest;
use App\Http\Requests\Admin\UpdateMediaRequest;
use App\Models\Media;
use App\Services\MediaUploader;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MediaController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Media::class);

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', Rule::in(['image', 'document'])],
            'trashed' => ['nullable', Rule::in(['with', 'only'])],
        ]);

        $mediaItems = Media::query()
            ->when($filters['search'] ?? null, fn (Builder $query, string $search) => $query->where('original_name', 'like', "%{$search}%"))
            ->when(($filters['type'] ?? null) === 'image', fn (Builder $query) => $query->where('mime_type', 'like', 'image/%'))
            ->when(($filters['type'] ?? null) === 'document', fn (Builder $query) => $query->where('mime_type', 'not like', 'image/%'))
            ->when(($filters['trashed'] ?? null) === 'with', fn (Builder $query) => $query->withTrashed())
            ->when(($filters['trashed'] ?? null) === 'only', fn (Builder $query) => $query->onlyTrashed())
            ->latest()
            ->paginate(24)
            ->withQueryString();

        return view('admin.media.index', compact('mediaItems', 'filters'));
    }

    public function create(): View
    {
        Gate::authorize('create', Media::class);

        return view('admin.media.create');
    }

    public function store(StoreMediaRequest $request, MediaUploader $uploader): RedirectResponse
    {
        $media = $uploader->upload(
            $request->file('file'),
            $request->user(),
            $request->safe()->only(['alt_text_al', 'alt_text_en']),
        );

        return redirect()->route('admin.media.edit', $media)->with('success', 'Media uploaded.');
    }

    public function edit(Media $medium): View
    {
        Gate::authorize('update', $medium);

        return view('admin.media.edit', ['media' => $medium]);
    }

    public function update(UpdateMediaRequest $request, Media $medium): RedirectResponse
    {
        $medium->update([
            ...$request->validated(),
            'updated_by' => $request->user()->id,
        ]);

        return redirect()->route('admin.media.index')->with('success', 'Media metadata updated.');
    }

    public function destroy(Media $medium): RedirectResponse
    {
        Gate::authorize('delete', $medium);
        $this->ensureNotReferenced($medium);
        $medium->delete();

        return redirect()->route('admin.media.index')->with('success', 'Media moved to trash.');
    }

    public function restore(int $medium): RedirectResponse
    {
        $media = Media::onlyTrashed()->findOrFail($medium);
        Gate::authorize('restore', $media);
        $media->restore();

        return redirect()->route('admin.media.index')->with('success', 'Media restored.');
    }

    public function forceDestroy(int $medium): RedirectResponse
    {
        $media = Media::onlyTrashed()->findOrFail($medium);
        Gate::authorize('forceDelete', $media);
        $this->ensureNotReferenced($media);

        $disk = $media->disk;
        $path = $media->path;
        $media->forceDelete();
        Storage::disk($disk)->delete($path);

        return redirect()->route('admin.media.index')->with('success', 'Media permanently deleted.');
    }

    private function ensureNotReferenced(Media $media): void
    {
        if ($media->isReferenced()) {
            throw ValidationException::withMessages([
                'media' => 'This file is still referenced by content and cannot be deleted.',
            ]);
        }
    }
}
