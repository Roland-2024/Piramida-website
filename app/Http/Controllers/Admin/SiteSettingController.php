<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SiteSettingRequest;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    public function edit(): View
    {
        Gate::authorize('manage-settings');
        $settings = SiteSetting::current();
        $settings->load('translations');

        return view('admin.settings.edit', [
            'settings' => $settings,
            'locales' => config('cms.locales'),
        ]);
    }

    public function update(SiteSettingRequest $request): RedirectResponse
    {
        $settings = SiteSetting::current();

        DB::transaction(function () use ($request, $settings): void {
            $data = $request->validated();
            $translations = $data['translations'];
            unset($data['translations']);

            $settings->update($data);
            $settings->syncTranslations($translations);
        });

        return back()->with('success', 'Site settings updated.');
    }
}
