<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class NotFoundPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_missing_routes_and_content_render_localized_404_pages(): void
    {
        foreach (['' => ['sq', 'Faqja nuk u gjet', '/'], '/en' => ['en', 'Page not found', '/en']] as $prefix => [$language, $title, $home]) {
            foreach (['/not-a-real-page', '/events/not-a-real-event', '/missing/nested/path', '/admin'] as $path) {
                if ($prefix === '' && $path === '/admin') {
                    continue;
                }

                $this->get($prefix.$path)->assertNotFound()
                    ->assertSee('lang="'.$language.'"', false)
                    ->assertSee($title)
                    ->assertSee('href="'.url($home).'"', false)
                    ->assertSee('name="robots" content="noindex, nofollow"', false)
                    ->assertDontSee('resources/css/public.css');
            }
        }

        $this->get('/admin')->assertRedirect(route('login'));
    }

    public function test_error_view_does_not_query_database_or_require_a_vite_manifest(): void
    {
        DB::listen(fn () => $this->fail('The static error view must not query the database.'));

        $html = view('errors.404')->render();

        $this->assertStringContainsString('Faqja nuk u gjet', $html);
        $this->assertStringNotContainsString('/build/', $html);
    }

    public function test_api_not_found_responses_remain_json(): void
    {
        $this->getJson('/api/not-a-real-endpoint')->assertNotFound()->assertJsonStructure(['message']);
    }
}
