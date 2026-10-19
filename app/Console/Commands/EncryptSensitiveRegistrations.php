<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class EncryptSensitiveRegistrations extends Command
{
    protected $signature = 'registrations:encrypt-sensitive {--dry-run}';

    protected $description = 'Enkripsi ulang data sensitif registrasi lama (idempoten).';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $columns = ['identity_number', 'emergency_contact_phone', 'medical_notes'];

        $total = DB::table('registrations')->count();
        $this->info("Memproses {$total} baris...");

        DB::table('registrations')->orderBy('id')->chunk(100, function ($rows) use ($columns, $dryRun) {
            foreach ($rows as $row) {
                $updates = [];

                foreach ($columns as $column) {
                    $value = $row->{$column};

                    if ($value === null || $value === '') {
                        continue;
                    }

                    try {
                        Crypt::decryptString($value);
                        // sudah terenkripsi, lewati
                        continue;
                    } catch (\Throwable $e) {
                        // belum terenkripsi, enkripsi
                    }

                    $updates[$column] = Crypt::encryptString($value);
                }

                if ($updates !== [] && ! $dryRun) {
                    DB::table('registrations')->where('id', $row->id)->update($updates);
                }
            }
        });

        $this->info($dryRun ? 'Dry-run selesai.' : 'Selesai.');

        return self::SUCCESS;
    }
}