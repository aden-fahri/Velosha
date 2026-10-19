<?php

namespace App\Console\Commands;

use App\Actions\CompleteRegistrationAction;
use App\Models\Registration;
use Illuminate\Console\Command;

class CompleteRegistrationCommand extends Command
{
    protected $signature = 'registration:complete {registrationId}';

    protected $description = 'Selesaikan pendaftaran (simulasi pembayaran) — hanya untuk testing.';

    public function handle(CompleteRegistrationAction $action): int
    {
        abort_unless(config('payments.simulation'), 403);

        $registration = Registration::find($this->argument('registrationId'));

        if (! $registration instanceof Registration) {
            $this->error('Pendaftaran tidak ditemukan.');

            return self::FAILURE;
        }

        try {
            $action->execute($registration);
        } catch (\RuntimeException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info('OK');

        return self::SUCCESS;
    }
}