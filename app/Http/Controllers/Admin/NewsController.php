<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ContentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NewsRequest;
use App\Models\Media;
use App\Models\News;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', News::class);

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::enum(ContentStatus::class)],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'trashed' => ['nullable', Rule::in(['with', 'only'])],
        ]);

        $articles = News::query()
            ->with(['translations', 'updatedBy'])
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->whereHas('translations', fn (Builder $query) => $query->where('title', 'like', "%{$search}%"));
            })
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['date_from'] ?? null, fn (Builder $query, string $date) => $query->whereDate('published_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn (Builder $query, string $date) => $query->whereDate('published_at', '<=', $date))
            ->when(($filters['trashed'] ?? null) === 'with', fn (Builder $query) => $query->withTrashed())
            ->when(($filters['trashed'] ?? null) === 'only', fn (Builder $query) => $query->onlyTrashed())
            ->latest('published_at')
            ->latest('updated_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.news.index', compact('articles', 'filters'));
    }

    public function create(): View
    {
        Gate::authorize('create', News::class);

        return view('admin.news.create', $this->formOptions());
    }

    public function store(NewsRequest $request): RedirectResponse
    {
        $article = DB::transaction(function () use ($request): News {
            $data = $request->validated();
            $translations = $data['translations'];
            $galleryMediaIds = $data['gallery_media_ids'] ?? [];
            unset($data['translations'], $data['gallery_media_ids']);

            $article = News::query()->create([
                ...$data,
                'created_by' => $request->user()->id,
                'updated_by' => $request->user()->id,
            ]);
            $article->syncTranslations($translations);
            $article->syncGallery($galleryMediaIds);

            return $article;
        });

        return redirect()->route('admin.news.show', $article)->with('success', 'News article created.');
    }

    public function show(News $news): View
    {
        Gate::authorize('view', $news);
        $news->load(['translations', 'featuredMedia', 'gallery', 'createdBy', 'updatedBy']);

        return view('admin.news.show', ['article' => $news]);
    }

    public function edit(News $news): View
    {
        Gate::authorize('update', $news);
        $news->load(['translations', 'gallery']);

        return view('admin.news.edit', [
            'article' => $news,
            ...$this->formOptions(),
        ]);
    }

    public function update(NewsRequest $request, News $news): RedirectResponse
    {
        DB::transaction(function () use ($request, $news): void {
            $data = $request->validated();
            $translations = $data['translations'];
            $galleryMediaIds = $data['gallery_media_ids'] ?? [];
            unset($data['translations'], $data['gallery_media_ids']);

            $news->update([...$data, 'updated_by' => $request->user()->id]);
            $news->syncTranslations($translations);
            $news->syncGallery($galleryMediaIds);
        });

        return redirect()->route('admin.news.show', $news)->with('success', 'News article updated.');
    }

    public function destroy(News $news): RedirectResponse
    {
        Gate::authorize('delete', $news);
        $news->delete();

        return redirect()->route('admin.news.index')->with('success', 'News article moved to trash.');
    }

    public function restore(int $news): RedirectResponse
    {
        $news = News::onlyTrashed()->findOrFail($news);
        Gate::authorize('restore', $news);
        $news->restore();

        return redirect()->route('admin.news.index')->with('success', 'News article restored.');
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
