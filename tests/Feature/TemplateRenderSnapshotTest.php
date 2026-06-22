<?php

namespace Tests\Feature;

use App\Models\DesignTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TemplateRenderSnapshotTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A fixed template exercising a representative mix of:
     *  - simple {{ $details[...] ?? "fallback" }} variable substitution
     *  - an @if(... ?? false)/@else/@endif conditional block
     *  - an @for(...) gallery loop
     */
    private function fixtureTemplate(): string
    {
        return <<<'HTML'
<html><body>
<h1>{{ $details["bride_name"] ?? "B" }} & {{ $details["groom_name"] ?? "G" }}</h1>
<p>{{ $details["venue"] ?? "Venue" }}</p>
@if($details["show_reception"] ?? false)
<div class="reception">Reception ON: {{ $details["reception_time"] ?? "" }}</div>
@else
<div class="no-reception">No reception</div>
@endif
<div class="gallery">
@for($i = 1; $i <= 3; $i++)
<div class="gallery-item"><img src="{{ $details["gallery_photo_" . $i] ?? false }}"></div>
@endfor
</div>
</body></html>
HTML;
    }

    /**
     * @return array<string, mixed>
     */
    private function fixtureVariables(): array
    {
        return [
            'bride_name' => 'Aisha',
            'groom_name' => 'Zaid',
            'venue' => 'Grand Hall',
            'show_reception' => true,
            'reception_time' => '8PM',
            'gallery_photo_1' => 'http://example.test/1.jpg',
            'gallery_photo_2' => 'http://example.test/2.jpg',
            'gallery_photo_3' => 'http://example.test/3.jpg',
        ];
    }

    private function admin(): User
    {
        return User::factory()->create(['type' => 'admin']);
    }

    private function user(): User
    {
        return User::factory()->create(['type' => 'user']);
    }

    public function test_user_template_preview_renders_expected_html(): void
    {
        $template = DesignTemplate::factory()->create([
            'full_html_template' => $this->fixtureTemplate(),
            'default_variables' => $this->fixtureVariables(),
        ]);

        $response = $this->actingAs($this->user())
            ->get(route('user.templates.preview', $template));

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('<h1>Aisha & Zaid</h1>', $html);
        $this->assertStringContainsString('Grand Hall', $html);

        $this->assertStringContainsString('Reception ON: 8PM', $html);
        $this->assertStringNotContainsString('No reception', $html);

        $this->assertSame(3, substr_count($html, 'class="gallery-item fade-in"'));
        $this->assertStringContainsString('src="http://example.test/1.jpg" alt="Gallery Photo 1"', $html);
        $this->assertStringContainsString('src="http://example.test/2.jpg" alt="Gallery Photo 2"', $html);
        $this->assertStringContainsString('src="http://example.test/3.jpg" alt="Gallery Photo 3"', $html);

        $this->assertStringNotContainsString('@if', $html);
        $this->assertStringNotContainsString('@for', $html);
        $this->assertStringNotContainsString('@endif', $html);
        $this->assertStringNotContainsString('$details', $html);
    }

    public function test_user_template_preview_excludes_conditional_block_when_false(): void
    {
        $variables = $this->fixtureVariables();
        unset($variables['show_reception']);

        $template = DesignTemplate::factory()->create([
            'full_html_template' => $this->fixtureTemplate(),
            'default_variables' => $variables,
        ]);

        $response = $this->actingAs($this->user())
            ->get(route('user.templates.preview', $template));

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('No reception', $html);
        $this->assertStringNotContainsString('Reception ON:', $html);
    }

    public function test_admin_full_preview_renders_expected_html(): void
    {
        $template = DesignTemplate::factory()->create([
            'full_html_template' => $this->fixtureTemplate(),
            'default_variables' => $this->fixtureVariables(),
        ]);

        $response = $this->actingAs($this->admin())
            ->get(route('admin.templates.full-preview', $template));

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('<h1>Aisha & Zaid</h1>', $html);
        $this->assertStringContainsString('Grand Hall', $html);

        $this->assertStringContainsString('Reception ON: 8PM', $html);
        $this->assertStringNotContainsString('No reception', $html);

        $this->assertSame(3, substr_count($html, 'class="gallery-item fade-in"'));
        $this->assertStringContainsString('src="http://example.test/1.jpg" alt="Gallery Photo 1"', $html);
        $this->assertStringContainsString('src="http://example.test/2.jpg" alt="Gallery Photo 2"', $html);
        $this->assertStringContainsString('src="http://example.test/3.jpg" alt="Gallery Photo 3"', $html);

        $this->assertStringNotContainsString('@if', $html);
        $this->assertStringNotContainsString('@for', $html);
        $this->assertStringNotContainsString('@endif', $html);
        $this->assertStringNotContainsString('$details', $html);
    }

    public function test_admin_full_preview_excludes_conditional_block_when_false(): void
    {
        $variables = $this->fixtureVariables();
        unset($variables['show_reception']);

        $template = DesignTemplate::factory()->create([
            'full_html_template' => $this->fixtureTemplate(),
            'default_variables' => $variables,
        ]);

        $response = $this->actingAs($this->admin())
            ->get(route('admin.templates.full-preview', $template));

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('No reception', $html);
        $this->assertStringNotContainsString('Reception ON:', $html);
    }
}
