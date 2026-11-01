<?php

namespace Tests\Feature\Security;

use App\Support\HtmlSanitizer;
use Tests\TestCase;

class HtmlSanitizerTest extends TestCase
{
    public function test_strips_script_tags_and_their_contents(): void
    {
        $dirty = '<p>Normal text <script>alert("xss")</script> more text</p>';
        $clean = HtmlSanitizer::clean($dirty);

        $this->assertStringNotContainsString('<script>', $clean);
        $this->assertStringNotContainsString('alert("xss")', $clean);
        $this->assertStringContainsString('Normal text', $clean);
        $this->assertStringContainsString('more text', $clean);
    }

    public function test_strips_inline_event_handlers(): void
    {
        $dirty = '<p><strong onclick="alert(1)" onmouseover="evil()">Click</strong></p>';
        $clean = HtmlSanitizer::clean($dirty);

        $this->assertStringNotContainsString('onclick', $clean);
        $this->assertStringNotContainsString('onmouseover', $clean);
        $this->assertStringContainsString('<strong>Click</strong>', $clean);
    }

    public function test_strips_javascript_and_data_schemes_in_links(): void
    {
        $dirty = '<a href="javascript:alert(1)">Bad Link</a> <a href="data:text/html;base64,PHNjcmlwdD4=">Data Link</a>';
        $clean = HtmlSanitizer::clean($dirty);

        $this->assertStringNotContainsString('href="javascript:', $clean);
        $this->assertStringNotContainsString('href="data:', $clean);
        $this->assertStringContainsString('Bad Link', $clean);
        $this->assertStringContainsString('Data Link', $clean);
    }

    public function test_enforces_noopener_noreferrer_and_external_target_on_valid_links(): void
    {
        $valid = '<p><a href="https://example.com/route">Official Route</a></p>';
        $clean = HtmlSanitizer::clean($valid);

        $this->assertStringContainsString('href="https://example.com/route"', $clean);
        $this->assertStringContainsString('rel="noopener noreferrer"', $clean);
        $this->assertStringContainsString('target="_blank"', $clean);
    }

    public function test_strips_dangerous_tags_iframe_style_and_object(): void
    {
        $dirty = '<div><iframe src="https://attacker.com"></iframe><style>body { display:none; }</style><object data="test"></object>Safe text</div>';
        $clean = HtmlSanitizer::clean($dirty);

        $this->assertStringNotContainsString('<iframe', $clean);
        $this->assertStringNotContainsString('<style', $clean);
        $this->assertStringNotContainsString('<object', $clean);
        $this->assertStringContainsString('Safe text', $clean);
    }

    public function test_allows_safe_tags_and_formatting(): void
    {
        $safe = '<h2>Rute Event</h2><p>Rute ini melalui <strong>Tawangmangu</strong> dan <em>Candi Cetho</em>.<br>Ketinggian: 1.200m.</p><ul><li>Pos 1: KM 20</li><li>Pos 2: KM 45</li></ul>';
        $clean = HtmlSanitizer::clean($safe);

        $this->assertStringContainsString('<h2>Rute Event</h2>', $clean);
        $this->assertStringContainsString('<strong>Tawangmangu</strong>', $clean);
        $this->assertStringContainsString('<em>Candi Cetho</em>', $clean);
        $this->assertStringContainsString('<ul><li>Pos 1: KM 20</li><li>Pos 2: KM 45</li></ul>', $clean);
    }
}
