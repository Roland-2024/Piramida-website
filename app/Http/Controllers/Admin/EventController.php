<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingMode;
use App\Enums\ContentStatus;
use App\Enums\EventCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EventRequest;
use App\Models\Event;
use App\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Event::class);

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::enum(ContentStatus::class)],
            'period' => ['nullable', Rule::in(['upcoming', 'past'])],
            'trashed' => ['nullable', Rule::in(['with', 'only'])],
        ]);

        $events = Event::query()
            ->with(['translations', 'updatedBy'])
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->whereHas('translations', fn (Builder $query) => $query->where('title', 'like', "%{$search}%"));
            })
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when(($filters['period'] ?? null) === 'upcoming', fn (Builder $query) => $query->upcoming())
            ->when(($filters['period'] ?? null) === 'past', fn (Builder $query) => $query->past())
            ->when(($filters['trashed'] ?? null) === 'with', fn (Builder $query) => $query->withTrashed())
            ->when(($filters['trashed'] ?? null) === 'only', fn (Builder $query) => $query->onlyTrashed())
            ->orderBy('starts_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.events.index', compact('events', 'filters'));
    }

    public function create(): View
    {
        Gate::authorize('create', Event::class);

        return view('admin.events.create', $this->formOptions());
    }

    public function store(EventRequest $request): RedirectResponse
    {
        $event = DB::transaction(function () use ($request): Event {
            $data = $request->validated();
            $translations = $data['translations'];
            $galleryMediaIds = $data['gallery_media_ids'] ?? [];
            unset($data['translations'], $data['gallery_media_ids']);

            $event = Event::query()->create([
                ...$data,
                'created_by' => $request->user()->id,
                'updated_by' => $request->user()->id,
            ]);
            $event->syncTranslations($translations);
            $event->syncGallery($galleryMediaIds);

            return $event;
        });

        return redirect()->route('admin.events.show', $event)->with('success', 'Event created.');
    }

    public function show(Event $event): View
    {
        Gate::authorize('view', $event);
        $event->load(['translations', 'featuredMedia', 'gallery', 'createdBy', 'updatedBy']);

        return view('admin.events.show', compact('event'));
    }

    public function edit(Event $event): View
    {
        Gate::authorize('update', $event);
        $event->load(['translations', 'gallery']);

        return view('admin.events.edit', [
            'event' => $event,
            ...$this->formOptions(),
        ]);
    }

    public function update(EventRequest $request, Event $event): RedirectResponse
    {
        DB::transaction(function () use ($request, $event): void {
            $data = $request->validated();
            $translations = $data['translations'];
            $galleryMediaIds = $data['gallery_media_ids'] ?? [];
            unset($data['translations'], $data['gallery_media_ids']);

            $event->update([...$data, 'updated_by' => $request->user()->id]);
            $event->syncTranslations($translations);
            $event->syncGallery($galleryMediaIds);
        });

        return redirect()->route('admin.events.show', $event)->with('success', 'Event updated.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        Gate::authorize('delete', $event);
        $event->delete();

        return redirect()->route('admin.events.index')->with('success', 'Event moved to trash.');
    }

    public function restore(int $event): RedirectResponse
    {
        $event = Event::onlyTrashed()->findOrFail($event);
        Gate::authorize('restore', $event);
        $event->restore();

        return redirect()->route('admin.events.index')->with('success', 'Event restored.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(): array
    {
        return [
            'statuses' => ContentStatus::cases(),
            'categories' => EventCategory::cases(),
            'bookingModes' => BookingMode::cases(),
            'mediaItems' => Media::query()->latest()->get(),
            'locales' => config('cms.locales'),
        ];
    }
}
