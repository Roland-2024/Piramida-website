<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicTypographyTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_pillar_headings_use_the_shared_display_class_in_both_languages(): void
    {
        foreach (['/', '/en'] as $url) {
            $response = $this->get($url)->assertOk();
            $this->assertSame(4, preg_match_all('/<h3 class="title-36 text-lime-400 uppercase mb-2">/', $response->getContent()));
        }
        $css = file_get_contents(public_path('template/integration.css'));
        $this->assertStringContainsString('h1,h2,h3,h4,h5,h6 { font-family:var(--font-display) !important; }', $css);
        $this->assertStringContainsString('@media(min-width:768px) { .event-track .event-card .title-24 { font-size:20px; } }', $css);
    }
}
