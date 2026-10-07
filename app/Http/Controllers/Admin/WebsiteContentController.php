<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PublicSettingsRequest;
use App\Http\Requests\Admin\WebsiteContentRequest;
use App\Models\Media;
use App\Models\SiteSetting;
use App\Support\WebsiteContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class WebsiteContentController extends Controller
{
    public function edit(Request $request)
    {
        Gate::authorize('manage-website-content');
        $groups = ['cms' => 'Shared labels, navigation and forms', 'website' => 'Page and template texts', 'seo' => 'Default SEO and social text', 'validation' => 'Validation messages', 'pagination' => 'Pagination', 'images' => 'Images and logos', 'contact' => 'Public contact and footer'];
        $group = $request->query('group', 'cms');
        abort_unless(isset($groups[$group]), 404);
        $defaults = ['al' => WebsiteContent::defaults('al'), 'en' => WebsiteContent::defaults('en')];
        $keys = array_filter(array_keys($defaults['en']), fn ($key) => str_starts_with($key, $group.'.'));
        $texts = DB::table('website_texts')->get()->groupBy('key')->map(fn ($rows) => $rows->pluck('text', 'locale'));

        return view('admin.website-content.edit', [
            'group' => $group, 'groups' => $groups, 'defaults' => $defaults, 'keys' => $keys, 'texts' => $texts,
            'images' => DB::table('website_images')->pluck('media_id', 'key'),
            'media' => $group === 'images' ? Media::where('disk', 'public')->where('mime_type', 'like', 'image/%')->orderBy('original_name')->get() : collect(),
            'settings' => SiteSetting::current()->load('translations'),
        ]);
    }

    public function update(WebsiteContentRequest $request)
    {
        $data = $request->validated();
        DB::transaction(function () use ($data) {
            if ($data['group'] === 'images') {
                foreach ($data['images'] as $key => $id) {
                    if ($id) {
                        DB::table('website_images')->updateOrInsert(['key' => $key], ['media_id' => $id]);
                    } else {
                        DB::table('website_images')->where('key', $key)->delete();
                    }
                }
            } else {
                foreach (array_keys(WebsiteContent::defaults('en')) as $key) {
                    foreach ($data['texts'][sha1($key)] ?? [] as $locale => $text) {
                        DB::table('website_texts')->updateOrInsert(['key' => $key, 'locale' => $locale], ['text' => $text ?? '']);
                    }
                }
            }
        });

        return back()->with('success', 'Website content updated.');
    }

    public function updateContact(PublicSettingsRequest $request)
    {
        DB::transaction(function () use ($request) {
            $settings = SiteSetting::current();
            $data = $request->validated();
            $translations = $data['translations'];
            unset($data['translations']);
            $settings->update($data);
            $settings->syncTranslations($translations);
        });

        return back()->with('success', 'Public contact details updated.');
    }
}
