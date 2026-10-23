<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DesignSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_layout_uses_self_hosted_fonts(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertDontSee('fonts.googleapis.com')
            ->assertSee('fonts/velosha/bricolage-grotesque-latin-wght-normal.woff2', false)
            ->assertSee('fonts/velosha/inter-latin-wght-normal.woff2', false)
            ->assertSee('Lewati ke konten');
    }

    public function test_font_faces_and_motion_guards_exist(): void
    {
        $css = file_get_contents(resource_path('css/app.css'));

        $this->assertStringContainsString("font-family: 'Bricolage Grotesque'", $css);
        $this->assertStringContainsString("font-family: 'Inter'", $css);
        $this->assertStringContainsString("font-family: 'JetBrains Mono'", $css);
        $this->assertMatchesRegularExpression('/@media \(prefers-reduced-motion: no-preference\)\s*\{\s*\.band-in\s*\{/', $css);
        $this->assertMatchesRegularExpression('/@media \(prefers-reduced-motion: no-preference\)\s*\{\s*\.float\s*\{/', $css);

        foreach ([
            'bricolage-grotesque-latin-wght-normal.woff2',
            'inter-latin-wght-normal.woff2',
            'jetbrains-mono-latin-700-normal.woff2',
        ] as $file) {
            $this->assertFileExists(public_path('fonts/velosha/'.$file));
        }
    }

    public function test_primary_color_pairs_meet_wcag_aa(): void
    {
        $css = file_get_contents(resource_path('css/app.css'));

        foreach ([
            ['ink', 'paper'],
            ['ink', 'orange'],
            ['ink', 'butter'],
            ['mute', 'paper2'],
            ['red', 'red-l'],
            ['orange-d', 'card'],
        ] as [$foreground, $background]) {
            $ratio = $this->contrastRatio($this->token($css, $foreground), $this->token($css, $background));

            $this->assertGreaterThanOrEqual(4.5, $ratio, "{$foreground} on {$background} is {$ratio}:1");
        }
    }

    private function token(string $css, string $name): string
    {
        preg_match('/--'.preg_quote($name, '/').':\s*(#[0-9A-Fa-f]{6})/', $css, $matches);

        $this->assertNotEmpty($matches, "Missing CSS token --{$name}");

        return $matches[1];
    }

    private function contrastRatio(string $foreground, string $background): float
    {
        $light = max($this->luminance($foreground), $this->luminance($background));
        $dark = min($this->luminance($foreground), $this->luminance($background));

        return ($light + 0.05) / ($dark + 0.05);
    }

    private function luminance(string $hex): float
    {
        $channels = array_map(
            fn (string $channel): float => hexdec($channel) / 255,
            str_split(ltrim($hex, '#'), 2)
        );

        $linear = array_map(
            fn (float $channel): float => $channel <= 0.03928
                ? $channel / 12.92
                : (($channel + 0.055) / 1.055) ** 2.4,
            $channels
        );

        return 0.2126 * $linear[0] + 0.7152 * $linear[1] + 0.0722 * $linear[2];
    }
}
