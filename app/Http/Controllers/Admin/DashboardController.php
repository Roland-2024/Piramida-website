<?php

namespace App\Http\Controllers\Admin;

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
                'news' => News::query()->count(),
                'events' => Event::query()->count(),
                'programs' => Program::query()->count(),
                'attractions' => Attraction::query()->count(),
                'businesses' => Business::query()->count(),
                'spaces' => Space::query()->count(),
                'careers' => Career::query()->count(),
                'new_submissions' => auth()->user()->isAdmin()
                    ? Submission::query()->where('status', 'new')->count()
                    : null,
                'users' => User::query()->count(),
                'admins' => User::query()->where('role', UserRole::Admin)->count(),
                'editors' => User::query()->where('role', UserRole::Editor)->count(),
                'published' => Page::query()->published()->count()
                    + News::query()->published()->count()
                    + Event::query()->published()->count()
                    + Program::query()->published()->count()
                    + Attraction::query()->published()->count()
                    + Business::query()->published()->count()
                    + Space::query()->published()->count()
                    + Career::query()->published()->count(),
                'drafts' => Page::query()->where('status', 'draft')->count()
                    + News::query()->where('status', 'draft')->count()
                    + Event::query()->where('status', 'draft')->count()
                    + Program::query()->where('status', 'draft')->count()
                    + Attraction::query()->where('status', 'draft')->count()
                    + Business::query()->where('status', 'draft')->count()
                    + Space::query()->where('status', 'draft')->count()
                    + Career::query()->where('status', 'draft')->count(),
                'upcoming_events' => Event::query()->published()->upcoming()->count(),
            ],
            'recentContent' => collect([
                Page::query()->with('translations')->latest('updated_at')->limit(5)->get()
                    ->map(fn (Page $page) => [
                        'type' => 'Page',
                        'title' => $page->translation('al')?->title,
                        'updated_at' => $page->updated_at,
                        'url' => route('admin.pages.show', $page),
                    ]),
                News::query()->with('translations')->latest('updated_at')->limit(5)->get()
                    ->map(fn (News $news) => [
                        'type' => 'News',
                        'title' => $news->translation('al')?->title,
                        'updated_at' => $news->updated_at,
                        'url' => route('admin.news.show', $news),
                    ]),
                Event::query()->with('translations')->latest('updated_at')->limit(5)->get()
                    ->map(fn (Event $event) => [
                        'type' => 'Event',
                        'title' => $event->translation('al')?->title,
                        'updated_at' => $event->updated_at,
                        'url' => route('admin.events.show', $event),
                    ]),
            ])->collapse()->sortByDesc('updated_at')->take(8),
        ]);
    }
}
