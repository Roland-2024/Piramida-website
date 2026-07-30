<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SectionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PageSectionRequest;
use App\Models\Media;
use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PageSectionController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', PageSection::class);

        $filters = $request->validate([
            'page_id' => ['nullable', 'integer', 'exists:pages,id'],
            'type' => ['nullable', Rule::enum(SectionType::class)],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
            'trashed' => ['nullable', Rule::in(['with', 'only'])],
        ]);

        $sections = PageSection::query()
            ->with(['translations', 'page.translations'])
            ->when($filters['page_id'] ?? null, fn (Builder $query, int $pageId) => $query->where('page_id', $pageId))
            ->when($filters['type'] ?? null, fn (Builder $query, string $type) => $query->where('type', $type))
            ->when(
                $filters['status'] ?? null,
                fn (Builder $query, string $status) => $query->where('is_active', $status === 'active')
            )
            ->when(($filters['trashed'] ?? null) === 'with', fn (Builder $query) => $query->withTrashed())
            ->when(($filters['trashed'] ?? null) === 'only', fn (Builder $query) => $query->onlyTrashed())
            ->orderBy('page_id')
            ->orderBy('display_order')
            ->orderBy('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.sections.index', [
            'sections' => $sections,
            'filters' => $filters,
            ...$this->formOptions(),
        ]);
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', PageSection::class);

        return view('admin.sections.create', [
            'selectedPageId' => $request->integer('page_id') ?: null,
            ...$this->formOptions(),
        ]);
    }

    public function store(PageSectionRequest $request): RedirectResponse
    {
        $section = DB::transaction(function () use ($request): PageSection {
            $data = $this->normalize($request->validated());
            $translations = $data['translations'];
            unset($data['translations']);

            $section = PageSection::query()->create([
                ...$data,
                'created_by' => $request->user()->id,
                'updated_by' => $request->user()->id,
            ]);
            $section->syncTranslations($translations);

            return $section;
        });

        return redirect()->route('admin.sections.show', $section)->with('success', 'Page section created.');
    }

    public function show(PageSection $section): View
    {
        Gate::authorize('view', $section);
        $section->load(['translations', 'page.translations', 'primaryMedia', 'secondaryMedia', 'createdBy', 'updatedBy']);

        return view('admin.sections.show', compact('section'));
    }

    public function edit(PageSection $section): View
    {
        Gate::authorize('update', $section);
        $section->load('translations');

        return view('admin.sections.edit', [
            'section' => $section,
            ...$this->formOptions(),
        ]);
    }

    public function update(PageSectionRequest $request, PageSection $section): RedirectResponse
    {
        DB::transaction(function () use ($request, $section): void {
            $data = $this->normalize($request->validated());
            $translations = $data['translations'];
            unset($data['translations']);

            $section->update([...$data, 'updated_by' => $request->user()->id]);
            $section->syncTranslations($translations);
        });

        return redirect()->route('admin.sections.show', $section)->with('success', 'Page section updated.');
    }

    public function destroy(PageSection $section): RedirectResponse
    {
        Gate::authorize('delete', $section);
        $section->delete();

        return redirect()->route('admin.sections.index')->with('success', 'Page section moved to trash.');
    }

    public function restore(int $section): RedirectResponse
    {
        $section = PageSection::onlyTrashed()->findOrFail($section);
        Gate::authorize('restore', $section);
        $section->restore();

        return redirect()->route('admin.sections.index')->with('success', 'Page section restored.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(): array
    {
        return [
            'pages' => Page::query()->with('translations')->orderBy('display_order')->get(),
            'sectionTypes' => SectionType::cases(),
            'mediaItems' => Media::query()->latest()->get(),
            'locales' => config('cms.locales'),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalize(array $data): array
    {
        $data['structured_data'] = filled($data['structured_data'] ?? null)
            ? json_decode($data['structured_data'], true, flags: JSON_THROW_ON_ERROR)
            : null;

        return $data;
    }
}
