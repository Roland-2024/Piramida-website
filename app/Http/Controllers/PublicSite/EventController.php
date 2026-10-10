<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        return view('public.events.index', [
            'events' => Event::query()
                ->published()
                ->whereHas('translations', fn (Builder $query) => $query->where('locale', app()->getLocale()))
                ->latest('starts_at')
                ->with(['translations', 'featuredMedia'])
                ->orderBy('id')
                ->paginate(3),
            'languageUrls' => collect(config('cms.locales'))
                ->mapWithKeys(fn (string $name, string $locale) => [
                    $locale => route('public.events.index', $locale),
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
            'latestEvents' => Event::query()->published()->whereKeyNot($event->id)
                ->whereHas('translations', fn (Builder $query) => $query->where('locale', $locale))
                ->with(['translations', 'featuredMedia'])->latest('starts_at')->orderBy('id')->limit(10)->get(),
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
