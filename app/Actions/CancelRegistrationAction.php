<?php

namespace App\Actions;

use App\Enums\RegistrationStatus;
use App\Models\EventCategory;
use App\Models\Registration;
use Illuminate\Support\Facades\DB;

class CancelRegistrationAction
{
    /**
     * Batalkan pendaftaran balap dan kembalikan kuota (jika sebelumnya sudah lunas/terhitung).
     */
    public function execute(Registration|int $registration): Registration
    {
        $registrationId = $registration instanceof Registration ? $registration->id : $registration;

        return DB::transaction(function () use ($registrationId) {
            /** @var Registration $reg */
            $reg = Registration::query()
                ->whereKey($registrationId)
                ->lockForUpdate()
                ->firstOrFail();

            $wasPaid = $reg->isPaid();

            $reg->status = RegistrationStatus::CANCELLED->value;
            $reg->save();

            // Jika sebelumnya sudah dihitung lunas, kembalikan slot kuota
            if ($wasPaid && $reg->event_category_id) {
                EventCategory::query()
                    ->whereKey($reg->event_category_id)
                    ->where('registered_count', '>', 0)
                    ->lockForUpdate()
                    ->decrement('registered_count');
            }

            return $reg;
        });
    }
}
