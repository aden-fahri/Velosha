<?php

namespace Tests\Unit\Helpdesk;

use App\Support\Helpdesk\TicketSanitizer;
use PHPUnit\Framework\TestCase;

class TicketSanitizerTest extends TestCase
{
    public function test_sanitizes_email_addresses(): void
    {
        $input = 'Hubungi saya di budi@mail.com atau info.user+race@velosha.co.id segera';
        $cleaned = TicketSanitizer::clean($input);

        $this->assertStringNotContainsString('budi@mail.com', $cleaned);
        $this->assertStringNotContainsString('info.user+race@velosha.co.id', $cleaned);
        $this->assertStringContainsString('[REDACTED_EMAIL]', $cleaned);
    }

    public function test_sanitizes_phone_numbers_and_long_digit_runs(): void
    {
        $input = 'Nomor WA saya 0812-3456-7890 dan no kantor +62 811 0000 001';
        $cleaned = TicketSanitizer::clean($input);

        $this->assertStringNotContainsString('0812-3456-7890', $cleaned);
        $this->assertStringNotContainsString('+62 811 0000 001', $cleaned);
        $this->assertStringContainsString('[REDACTED_PHONE]', $cleaned);
    }

    public function test_sanitizes_urls_with_tokens(): void
    {
        $input = 'Cek link saya: https://velosha.id/reset-password?token=secret123456789&session=abc untuk detailnya';
        $cleaned = TicketSanitizer::clean($input);

        $this->assertStringNotContainsString('secret123456789', $cleaned);
        $this->assertStringContainsString('[REDACTED_URL]', $cleaned);
    }

    public function test_preserves_innocent_short_numbers(): void
    {
        $input = 'Tiket nomor 42 kategori 10K dan bib 50';
        $cleaned = TicketSanitizer::clean($input);

        $this->assertStringContainsString('42', $cleaned);
        $this->assertStringContainsString('10K', $cleaned);
        $this->assertStringContainsString('50', $cleaned);
    }

    public function test_handles_combined_sensitive_subject_sample(): void
    {
        $input = 'Kendala BIB: telepon saya 0812-3456-7890 atau email budi@mail.com mohon solusinya';
        $cleaned = TicketSanitizer::clean($input);

        $this->assertSame('Kendala BIB: telepon saya [REDACTED_PHONE] atau email [REDACTED_EMAIL] mohon solusinya', $cleaned);
    }
}
