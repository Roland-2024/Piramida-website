<?php

use App\Http\Controllers\Admin\AttractionController;
use App\Http\Controllers\Admin\BusinessController;
use App\Http\Controllers\Admin\CareerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PageSectionController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\SpaceController;
use App\Http\Controllers\Admin\SubmissionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\PublicSite\AttractionController as PublicAttractionController;
use App\Http\Controllers\PublicSite\BusinessController as PublicBusinessController;
use App\Http\Controllers\PublicSite\CareerController as PublicCareerController;
use App\Http\Controllers\PublicSite\EventController as PublicEventController;
use App\Http\Controllers\PublicSite\HomeController;
use App\Http\Controllers\PublicSite\NewsController as PublicNewsController;
use App\Http\Controllers\PublicSite\PageController as PublicPageController;
use App\Http\Controllers\PublicSite\SpaceController as PublicSpaceController;
use App\Http\Controllers\PublicSite\SubmissionController as PublicSubmissionController;
use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\SetLocale;
use App\Models\Attraction;
use App\Models\Business;
use App\Models\Career;
use App\Models\Space;
use Illuminate\Support\Facades\Route;

Route::model('attraction', Attraction::class);
Route::model('business', Business::class);
Route::model('space', Space::class);
Route::model('career', Career::class);

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
        Route::post('/events/{slug}/request', [PublicSubmissionController::class, 'storeEvent'])->middleware('throttle:10,1')->name('events.request');
        Route::get('/attractions', [PublicAttractionController::class, 'index'])->name('attractions.index');
        Route::get('/attractions/{slug}', [PublicAttractionController::class, 'show'])->name('attractions.show');
        Route::get('/businesses', [PublicBusinessController::class, 'index'])->name('businesses.index');
        Route::get('/businesses/{slug}', [PublicBusinessController::class, 'show'])->name('businesses.show');
        Route::get('/spaces', [PublicSpaceController::class, 'index'])->name('spaces.index');
        Route::get('/spaces/{slug}', [PublicSpaceController::class, 'show'])->name('spaces.show');
        Route::post('/spaces/{slug}/event-request', [PublicSubmissionController::class, 'storeEventSpace'])->middleware('throttle:10,1')->name('spaces.event-request');
        Route::post('/spaces/{slug}/leasing-request', [PublicSubmissionController::class, 'storeLeasing'])->middleware('throttle:10,1')->name('spaces.leasing-request');
        Route::get('/careers', [PublicCareerController::class, 'index'])->name('careers.index');
        Route::get('/careers/{slug}', [PublicCareerController::class, 'show'])->name('careers.show');
        Route::post('/careers/{slug}/apply', [PublicSubmissionController::class, 'storeCareer'])->middleware('throttle:10,1')->name('careers.apply');
        Route::get('/contact', [PublicSubmissionController::class, 'contact'])->name('contact');
        Route::post('/contact', [PublicSubmissionController::class, 'storeContact'])->middleware('throttle:10,1')->name('contact.store');
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

        Route::post('events/sync-wordpress', [EventController::class, 'syncWordPress'])->name('events.sync-wordpress');
        Route::post('events/{event}/restore', [EventController::class, 'restore'])->name('events.restore');
        Route::resource('events', EventController::class);

        Route::post('attractions/{id}/restore', [AttractionController::class, 'restore'])->name('attractions.restore');
        Route::resource('attractions', AttractionController::class)->except('show');

        Route::post('businesses/{id}/restore', [BusinessController::class, 'restore'])->name('businesses.restore');
        Route::resource('businesses', BusinessController::class)->except('show');

        Route::post('spaces/{id}/restore', [SpaceController::class, 'restore'])->name('spaces.restore');
        Route::resource('spaces', SpaceController::class)->except('show');

        Route::post('careers/{id}/restore', [CareerController::class, 'restore'])->name('careers.restore');
        Route::resource('careers', CareerController::class)->except('show');

        Route::get('submissions/export', [SubmissionController::class, 'export'])->name('submissions.export');
        Route::get('submissions/{submission}/attachment', [SubmissionController::class, 'download'])->name('submissions.download');
        Route::get('submissions/{submission}/attachments/{attachment}', [SubmissionController::class, 'downloadAttachment'])->name('submissions.attachments.download');
        Route::resource('submissions', SubmissionController::class)->only(['index', 'show', 'update'])
            ->middleware('can:manage-submissions');

        Route::get('settings', [SiteSettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SiteSettingController::class, 'update'])->name('settings.update');

        Route::post('media/{medium}/restore', [MediaController::class, 'restore'])->name('media.restore');
        Route::delete('media/{medium}/force', [MediaController::class, 'forceDestroy'])->name('media.force-destroy');
        Route::resource('media', MediaController::class)->except('show');

        Route::resource('users', UserController::class)
            ->only(['index', 'create', 'store', 'edit', 'update'])
            ->middleware('can:manage-users');
    });
