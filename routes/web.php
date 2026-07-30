<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PageSectionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\PublicSite\EventController as PublicEventController;
use App\Http\Controllers\PublicSite\HomeController;
use App\Http\Controllers\PublicSite\NewsController as PublicNewsController;
use App\Http\Controllers\PublicSite\PageController as PublicPageController;
use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/al');

Route::prefix('{locale}')
    ->name('public.')
    ->whereIn('locale', array_keys(config('cms.locales')))
    ->middleware(SetLocale::class)
    ->group(function (): void {
        Route::get('/', HomeController::class)->name('home');
        Route::get('/news', [PublicNewsController::class, 'index'])->name('news.index');
        Route::get('/news/{slug}', [PublicNewsController::class, 'show'])->name('news.show');
        Route::get('/events', [PublicEventController::class, 'index'])->name('events.index');
        Route::get('/events/{slug}', [PublicEventController::class, 'show'])->name('events.show');
        Route::get('/{slug}', [PublicPageController::class, 'show'])->name('pages.show');
    });

Route::middleware('guest')->group(function (): void {
    Route::get('/admin/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/admin/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');

    Route::get('/admin/forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');
    Route::post('/admin/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');
    Route::get('/admin/reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('/admin/reset-password', [NewPasswordController::class, 'store'])
        ->name('password.update');
});

Route::post('/admin/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', EnsureUserIsActive::class])
    ->group(function (): void {
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::post('pages/{page}/restore', [PageController::class, 'restore'])->name('pages.restore');
        Route::resource('pages', PageController::class);

        Route::post('sections/{section}/restore', [PageSectionController::class, 'restore'])->name('sections.restore');
        Route::resource('sections', PageSectionController::class);

        Route::post('news/{news}/restore', [NewsController::class, 'restore'])->name('news.restore');
        Route::resource('news', NewsController::class);

        Route::post('events/{event}/restore', [EventController::class, 'restore'])->name('events.restore');
        Route::resource('events', EventController::class);

        Route::post('media/{medium}/restore', [MediaController::class, 'restore'])->name('media.restore');
        Route::delete('media/{medium}/force', [MediaController::class, 'forceDestroy'])->name('media.force-destroy');
        Route::resource('media', MediaController::class)->except('show');

        Route::resource('users', UserController::class)
            ->only(['index', 'create', 'store', 'edit', 'update'])
            ->middleware('can:manage-users');
    });
