<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SpaceType;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Attraction;
use App\Models\Business;
use App\Models\Career;
use App\Models\Event;
use App\Models\News;
use App\Models\Page;
use App\Models\Program;
use App\Models\Space;
use App\Models\Submission;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'metrics' => [
                'pages' => Page::query()->count(),
                'programs' => Program::query()->count(),
                'news' => News::query()->count(),
                'events' => Event::query()->count(),
                'attractions' => Attraction::query()->count(),
                'businesses' => Business::query()->count(),
                'event_spaces' => Space::query()->where('type', SpaceType::EventSpace)->count(),
                'leasing_spaces' => Space::query()->where('type', SpaceType::Leasing)->count(),
                'careers' => Career::query()->count(),
                'new_submissions' => auth()->user()->isAdmin()
                    ? Submission::query()->where('status', 'new')->count()
                    : null,
                'users' => User::query()->count(),
                'admins' => User::query()->where('role', UserRole::Admin)->count(),
                'editors' => User::query()->where('role', UserRole::Editor)->count(),
                'published' => Page::query()->published()->count()
                    + Program::query()->published()->count()
                    + News::query()->published()->count()
                    + Event::query()->published()->count()
                    + Attraction::query()->published()->count()
                    + Business::query()->published()->count()
                    + Space::query()->published()->count()
                    + Career::query()->published()->count(),
                'drafts' => Page::query()->where('status', 'draft')->count()
                    + Program::query()->where('status', 'draft')->count()
                    + News::query()->where('status', 'draft')->count()
                    + Event::query()->where('status', 'draft')->count()
                    + Attraction::query()->where('status', 'draft')->count()
                    + Business::query()->where('status', 'draft')->count()
                    + Space::query()->where('status', 'draft')->count()
                    + Career::query()->where('status', 'draft')->count(),
                'upcoming_events' => Event::query()->published()->upcoming()->count(),
            ],
            'recentContent' => collect([
                Page::class => ['Page', 'pages'],
                News::class => ['News', 'news'],
                Event::class => ['Event', 'events'],
                Program::class => ['Carousel post', 'programs'],
                Attraction::class => ['Attraction', 'attractions'],
                Business::class => ['Business', 'businesses'],
                Space::class => ['Space', 'spaces'],
                Career::class => ['Career', 'careers'],
            ])->flatMap(fn (array $meta, string $model) => $model::query()
                ->with('translations')->latest('updated_at')->orderByDesc('id')->limit(8)->get()
                ->map(fn ($item) => [
                    'type' => $meta[0],
                    'title' => $item->translation('al')?->title ?? $item->translation('al')?->name,
                    'updated_at' => $item->updated_at,
                    'url' => route('admin.'.($item instanceof Space && $item->type === SpaceType::Leasing ? 'leasing' : $meta[1]).'.edit', $item),
                ]))->sortByDesc('updated_at')->take(8),
        ]);
    }
}
