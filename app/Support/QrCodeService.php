<?php

namespace App\Support;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

/**
 * Generator QR offline (chillerlan/php-qrcode) — menggantikan
 * api.qrserver.com agar tiket & pembayaran tetap tampil tanpa internet.
 */
class QrCodeService
{
    /**
     * Render QR sebagai data URI SVG siap pakai di <img src="...">.
     */
    public static function uri(string $data, int $eccLevel = QRCode::ECC_M): string
    {
        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_MARKUP_SVG,
            'eccLevel' => $eccLevel,
            'outputBase64' => false,
            'imageBase64' => false,
        ]);

        $svg = (new QRCode($options))->render($data);

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
