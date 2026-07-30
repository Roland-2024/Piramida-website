<?php

namespace App\Providers;

use App\Models\Event;
use App\Models\Media;
use App\Models\News;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\User;
use App\Policies\ContentPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('manage-users', fn (User $user): bool => $user->isAdmin());

        Gate::policy(Page::class, ContentPolicy::class);
        Gate::policy(PageSection::class, ContentPolicy::class);
        Gate::policy(News::class, ContentPolicy::class);
        Gate::policy(Event::class, ContentPolicy::class);
        Gate::policy(Media::class, ContentPolicy::class);
    }
}
