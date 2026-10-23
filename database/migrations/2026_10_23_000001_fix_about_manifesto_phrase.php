<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const OLD_PHRASE = 'spreadsheet, kotak doubtful, dan informasi yang tercecer di grup WhatsApp';

    private const NEW_PHRASE = 'spreadsheet, catatan manual, dan informasi yang tercecer di grup WhatsApp';

    public function up(): void
    {
        DB::table('about_contents')
            ->where('manifesto_body', 'like', '%'.self::OLD_PHRASE.'%')
            ->update([
                'manifesto_body' => DB::raw(
                    "REPLACE(manifesto_body, '".self::OLD_PHRASE."', '".self::NEW_PHRASE."')"
                ),
            ]);
    }

    public function down(): void
    {
    }
};
