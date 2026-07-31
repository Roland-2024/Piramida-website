<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ContentStatus;
use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

abstract class TranslatedContentController extends Controller
{
    /** @var class-string<Model> */
    protected string $modelClass;

    protected string $routeParameter;

    protected string $routePrefix;

    protected string $singular;

    protected string $plural;

    protected string $translationTitleColumn = 'title';

    protected bool $withMedia = true;

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', $this->modelClass);

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::enum(ContentStatus::class)],
            'trashed' => ['nullable', Rule::in(['with', 'only'])],
        ]);

        $modelClass = $this->modelClass;
        $titleColumn = $this->translationTitleColumn;
        $items = $modelClass::query()
            ->with(['translations', 'updatedBy'])
            ->when($filters['search'] ?? null, function (Builder $query, string $search) use ($titleColumn): void {
                $query->whereHas(
                    'translations',
                    fn (Builder $query) => $query->where($titleColumn, 'like', "%{$search}%")
                );
            })
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when(($filters['trashed'] ?? null) === 'with', fn (Builder $query) => $query->withTrashed())
            ->when(($filters['trashed'] ?? null) === 'only', fn (Builder $query) => $query->onlyTrashed())
            ->orderBy('display_order')
            ->latest('updated_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.catalog.index', [
            'items' => $items,
            'filters' => $filters,
            ...$this->viewMeta(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', $this->modelClass);

        return view('admin.catalog.create', [
            ...$this->viewMeta(),
            ...$this->formOptions(),
        ]);
    }

    public function edit(Request $request): View
    {
        $item = $this->routeModel($request);
        Gate::authorize('update', $item);
        $item->load(['translations', 'gallery']);

        return view('admin.catalog.edit', [
            'item' => $item,
            ...$this->viewMeta(),
            ...$this->formOptions(),
        ]);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $item = $this->routeModel($request);
        Gate::authorize('delete', $item);
        $item->delete();

        return redirect()->route("{$this->routePrefix}.index")
            ->with('success', "{$this->singular} moved to trash.");
    }

    public function restore(Request $request): RedirectResponse
    {
        $modelClass = $this->modelClass;
        $item = $modelClass::onlyTrashed()->findOrFail($request->route('id'));
        Gate::authorize('restore', $item);
        $item->restore();

        return redirect()->route("{$this->routePrefix}.index")
            ->with('success', "{$this->singular} restored.");
    }

    protected function storeContent(FormRequest $request): RedirectResponse
    {
        $item = DB::transaction(function () use ($request): Model {
            $data = $request->validated();
            $translations = $data['translations'];
            $galleryMediaIds = $data['gallery_media_ids'] ?? [];
            unset($data['translations'], $data['gallery_media_ids']);

            $modelClass = $this->modelClass;
            $item = $modelClass::query()->create([
                ...$data,
                'created_by' => $request->user()->id,
                'updated_by' => $request->user()->id,
            ]);
            $item->syncTranslations($translations);

            if (method_exists($item, 'syncGallery')) {
                $item->syncGallery($galleryMediaIds);
            }

            return $item;
        });

        return redirect()->route("{$this->routePrefix}.edit", $item)
            ->with('success', "{$this->singular} created.");
    }

    protected function updateContent(FormRequest $request): RedirectResponse
    {
        $item = $this->routeModel($request);

        DB::transaction(function () use ($request, $item): void {
            $data = $request->validated();
            $translations = $data['translations'];
            $galleryMediaIds = $data['gallery_media_ids'] ?? [];
            unset($data['translations'], $data['gallery_media_ids']);

            $item->update([...$data, 'updated_by' => $request->user()->id]);
            $item->syncTranslations($translations);

            if (method_exists($item, 'syncGallery')) {
                $item->syncGallery($galleryMediaIds);
            }
        });

        return redirect()->route("{$this->routePrefix}.edit", $item)
            ->with('success', "{$this->singular} updated.");
    }

    /**
     * @return array<string, mixed>
     */
    protected function formOptions(): array
    {
        return [
            'statuses' => ContentStatus::cases(),
            'mediaItems' => Media::query()->latest()->get(),
            'locales' => config('cms.locales'),
            'globalFields' => $this->globalFields(),
            'translationFields' => $this->translationFields(),
            'withMedia' => $this->withMedia,
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    abstract protected function globalFields(): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    abstract protected function translationFields(): array;

    protected function routeModel(Request $request): Model
    {
        $item = $request->route($this->routeParameter);
        abort_unless($item instanceof $this->modelClass, 404);

        return $item;
    }

    /**
     * @return array<string, string>
     */
    private function viewMeta(): array
    {
        return [
            'routePrefix' => $this->routePrefix,
            'singular' => $this->singular,
            'plural' => $this->plural,
            'translationTitleColumn' => $this->translationTitleColumn,
        ];
    }
}
