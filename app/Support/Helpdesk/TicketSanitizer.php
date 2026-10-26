<?php

namespace App\Support\Helpdesk;

/**
 * @file TicketSanitizer.php
 * @class TicketSanitizer
 * @brief Utilitas sanitasi dan redaksi data sensitif (PII) pada modul Helpdesk.
 */
class TicketSanitizer
{
    /**
     * Membersihkan teks dari PII (Personally Identifiable Information):
     * - Alamat email
     * - Nomor telepon & format digit panjang (>= 8 digit, dengan atau tanpa pemisah)
     * - URL bertoken / query string sensitif
     * - Spasi berlebih
     *
     * @param string|null $text Teks input yang akan disanitasi
     * @return string Teks hasil redaksi PII
     */
    public static function clean(?string $text): string
    {
        if ($text === null || trim($text) === '') {
            return '';
        }

        // 1. Redact Email addresses (e.g. budi@mail.com, user.name+tag@sub.domain.co.id)
        $cleaned = preg_replace('/[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+/u', '[REDACTED_EMAIL]', $text);

        // 2. Redact URLs with query parameters/tokens (e.g. https://domain.com/reset?token=xyz123)
        $cleaned = preg_replace('/https?:\/\/[^\s]+(\?[^\s]+)/iu', '[REDACTED_URL]', $cleaned);

        // 3. Redact Indonesian & International Phone numbers / long digit runs (e.g. 0812-3456-7890, +62 811 0000 001, 16-digit card/NIK)
        // Pola: mencocokkan blok digit dan pemisah strip/spasi yang memiliki minimal 8 digit angka
        $cleaned = preg_replace_callback('/(?:\+?\d[\d\s\-\.\(\)]{6,}\d)/u', function ($matches) {
            $digitsOnly = preg_replace('/\D/', '', $matches[0]);
            if (strlen($digitsOnly) >= 8) {
                return '[REDACTED_PHONE]';
            }
            return $matches[0];
        }, $cleaned);

        // 4. Bersihkan whitespace berulang
        return trim(preg_replace('/\s+/', ' ', $cleaned));
    }
}
