<?php

namespace App\Actions;

use App\Enums\RegistrationStatus;
use App\Models\EventCategory;
use App\Models\Registration;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CompleteRegistrationAction
{
    /**
     * Selesaikan pendaftaran balap secara atomik:
     * - Kunci baris kategori & pendaftaran (cegah race condition)
     * - Verifikasi ketersediaan kuota
     * - Terbitkan nomor BIB resmi jika belum ada
     * - Ubah status ke PAID
     * - Tambah registered_count kategori
     * - Terbitkan tiket QR resmi
     *
     * @throws RuntimeException
     */
    public function execute(Registration|int $registration): Registration
    {
        $registrationId = $registration instanceof Registration ? $registration->id : $registration;
        $quotaFull = false;

        $reg = DB::transaction(function () use ($registrationId, &$quotaFull) {
            /** @var Registration $reg */
            $reg = Registration::query()
                ->whereKey($registrationId)
                ->lockForUpdate()
                ->first();

            if (! $reg instanceof Registration) {
                throw new RuntimeException('Data pendaftaran tidak ditemukan. Ulangi proses checkout.');
            }

            if ($reg->isPaid()) {
                return $reg;
            }

            /** @var EventCategory $category */
            $category = EventCategory::query()
                ->whereKey($reg->event_category_id)
                ->lockForUpdate()
                ->first();

            if (! $category instanceof EventCategory) {
                throw new RuntimeException('Kategori event tidak ditemukan.');
            }

            if ($category->remaining <= 0) {
                $reg->status = RegistrationStatus::CANCELLED->value;
                $reg->save();
                $quotaFull = true;

                return $reg;
            }

            if (blank($reg->bib_number)) {
                $reg->bib_number = Registration::generateBibNumber($category);
            }

            $reg->status = RegistrationStatus::PAID->value;
            $reg->save();

            $category->increment('registered_count');

            Ticket::issueFor($reg);

            return $reg;
        });

        if ($quotaFull) {
            throw new RuntimeException('Slot kategori sudah penuh. Pendaftaran Anda dibatalkan.');
        }

        return $reg;
    }
}
