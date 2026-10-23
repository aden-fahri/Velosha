<?php

namespace Tests\Feature;

use App\Models\AboutContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutContentPhraseTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_copy_uses_the_agreed_phrase(): void
    {
        $body = AboutContent::defaults()['manifesto_body'];

        $this->assertStringContainsString('spreadsheet, catatan manual, dan informasi yang tercecer di grup WhatsApp', $body);
        $this->assertStringNotContainsString('kotak doubtful', $body);
    }

    public function test_migration_replaces_only_the_legacy_phrase_and_is_idempotent(): void
    {
        $content = AboutContent::create(array_merge(AboutContent::defaults(), [
            'manifesto_body' => 'Sebelumnya kami memakai spreadsheet, kotak doubtful, dan informasi yang tercecer di grup WhatsApp untuk semua operasi.',
        ]));

        $migration = require database_path('migrations/2026_10_23_000001_fix_about_manifesto_phrase.php');
        $migration->up();

        $this->assertDatabaseHas('about_contents', [
            'id' => $content->id,
            'manifesto_body' => 'Sebelumnya kami memakai spreadsheet, catatan manual, dan informasi yang tercecer di grup WhatsApp untuk semua operasi.',
        ]);

        $migration->up();

        $this->assertSame(1, AboutContent::count());
        $this->assertDatabaseHas('about_contents', [
            'id' => $content->id,
            'manifesto_body' => 'Sebelumnya kami memakai spreadsheet, catatan manual, dan informasi yang tercecer di grup WhatsApp untuk semua operasi.',
        ]);
    }
}
