<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'period' => ['nullable', Rule::in(['upcoming', 'past'])],
        ]);
        $period = $filters['period'] ?? 'upcoming';

        return view('public.events.index', [
            'events' => Event::query()
                ->published()
                ->whereHas('translations', fn (Builder $query) => $query->where('locale', app()->getLocale()))
                ->when($period === 'upcoming', fn (Builder $query) => $query->upcoming()->orderBy('starts_at'))
                ->when($period === 'past', fn (Builder $query) => $query->past()->latest('starts_at'))
                ->with(['translations', 'featuredMedia'])
                ->paginate(9)
                ->withQueryString(),
            'period' => $period,
            'languageUrls' => collect(config('cms.locales'))
                ->mapWithKeys(fn (string $name, string $locale) => [
                    $locale => route('public.events.index', [$locale, 'period' => $period]),
                ])
                ->all(),
        ]);
    }

    public function show(string $locale, string $slug): View
    {
        $event = Event::query()
            ->published()
            ->whereHas('translations', fn (Builder $query) => $query
                ->where('locale', $locale)
                ->where('slug', $slug))
            ->with(['translations', 'featuredMedia'])
            ->firstOrFail();

        return view('public.events.show', [
            'event' => $event,
            'translation' => $event->translation($locale, false),
            'languageUrls' => collect(config('cms.locales'))
                ->mapWithKeys(function (string $name, string $targetLocale) use ($event): array {
                    $translation = $event->translation($targetLocale, false);

                    return [
                        $targetLocale => $translation
                            ? route('public.events.show', [$targetLocale, $translation->slug])
                            : route('public.events.index', $targetLocale),
                    ];
                })
                ->all(),
        ]);
    }
}
