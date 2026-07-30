<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ContentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PageRequest;
use App\Models\Media;
use App\Models\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PageController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Page::class);

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::enum(ContentStatus::class)],
            'trashed' => ['nullable', Rule::in(['with', 'only'])],
        ]);

        $pages = Page::query()
            ->with(['translations', 'updatedBy'])
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->whereHas('translations', fn (Builder $query) => $query->where('title', 'like', "%{$search}%"));
            })
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when(($filters['trashed'] ?? null) === 'with', fn (Builder $query) => $query->withTrashed())
            ->when(($filters['trashed'] ?? null) === 'only', fn (Builder $query) => $query->onlyTrashed())
            ->orderBy('display_order')
            ->latest('updated_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.pages.index', compact('pages', 'filters'));
    }

    public function create(): View
    {
        Gate::authorize('create', Page::class);

        return view('admin.pages.create', $this->formOptions());
    }

    public function store(PageRequest $request): RedirectResponse
    {
        $page = DB::transaction(function () use ($request): Page {
            $data = $request->validated();
            $translations = $data['translations'];
            unset($data['translations']);

            if ($data['is_homepage']) {
                Page::query()->where('is_homepage', true)->lockForUpdate()->update(['is_homepage' => false]);
            }

            $page = Page::query()->create([
                ...$data,
                'created_by' => $request->user()->id,
                'updated_by' => $request->user()->id,
            ]);
            $page->syncTranslations($translations);

            return $page;
        });

        return redirect()->route('admin.pages.show', $page)->with('success', 'Page created.');
    }

    public function show(Page $page): View
    {
        Gate::authorize('view', $page);
        $page->load(['translations', 'featuredMedia', 'sections.translations', 'createdBy', 'updatedBy']);

        return view('admin.pages.show', compact('page'));
    }

    public function edit(Page $page): View
    {
        Gate::authorize('update', $page);
        $page->load('translations');

        return view('admin.pages.edit', [
            'page' => $page,
            ...$this->formOptions(),
        ]);
    }

    public function update(PageRequest $request, Page $page): RedirectResponse
    {
        DB::transaction(function () use ($request, $page): void {
            $data = $request->validated();
            $translations = $data['translations'];
            unset($data['translations']);

            if ($data['is_homepage']) {
                Page::query()
                    ->whereKeyNot($page->id)
                    ->where('is_homepage', true)
                    ->lockForUpdate()
                    ->update(['is_homepage' => false]);
            }

            $page->update([...$data, 'updated_by' => $request->user()->id]);
            $page->syncTranslations($translations);
        });

        return redirect()->route('admin.pages.show', $page)->with('success', 'Page updated.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        Gate::authorize('delete', $page);
        $page->delete();

        return redirect()->route('admin.pages.index')->with('success', 'Page moved to trash.');
    }

    public function restore(int $page): RedirectResponse
    {
        $page = Page::onlyTrashed()->findOrFail($page);
        Gate::authorize('restore', $page);
        $page->restore();

        return redirect()->route('admin.pages.index')->with('success', 'Page restored.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(): array
    {
        return [
            'statuses' => ContentStatus::cases(),
            'mediaItems' => Media::query()->latest()->get(),
            'locales' => config('cms.locales'),
        ];
    }
}
