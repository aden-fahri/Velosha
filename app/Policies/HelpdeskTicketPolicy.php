<?php

namespace App\Policies;

use App\Models\HelpdeskTicket;
use App\Models\User;

class HelpdeskTicketPolicy
{
    /**
     * Tentukan apakah user boleh melihat daftar antrian / tiket saya.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isOperator() || $user->isPeserta();
    }

    /**
     * Tentukan apakah user boleh melihat detail tiket spesifik.
     * Aturan spec:
     * - Admin dan Operator dapat melihat semua tiket.
     * - Reporter (peserta) HANYA boleh melihat tiket miliknya.
     */
    public function view(User $user, HelpdeskTicket $ticket): bool
    {
        if ($user->isAdmin() || $user->isOperator()) {
            return true;
        }

        return $ticket->reporter_id === $user->id;
    }

    /**
     * Tentukan apakah user boleh membuat tiket baru (peserta).
     */
    public function create(User $user): bool
    {
        return $user->isPeserta() || $user->isAdmin();
    }

    /**
     * Tentukan apakah user boleh membalas tiket.
     * Aturan spec:
     * - Tiket yang sudah Closed tidak boleh dibalas oleh siapa pun (pelapor membuat tiket baru).
     * - Operator & Admin boleh membalas jika status belum Closed.
     * - Pelapor hanya boleh membalas tiket miliknya jika status belum Closed.
     */
    public function reply(User $user, HelpdeskTicket $ticket): bool
    {
        if ($ticket->isClosed()) {
            return false;
        }

        if ($user->isAdmin() || $user->isOperator()) {
            return true;
        }

        return $ticket->reporter_id === $user->id;
    }

    /**
     * Tentukan apakah user boleh mengubah status / prioritas / operator assignment.
     * Aturan spec: Operator dan Admin saja.
     */
    public function update(User $user, HelpdeskTicket $ticket): bool
    {
        return $user->isAdmin() || $user->isOperator();
    }

    /**
     * Tentukan apakah user boleh memberi rating kepuasan.
     * Aturan spec:
     * - HANYA reporter tiket (bukan operator atau admin).
     * - HANYA jika status Closed.
     * - HANYA satu kali (belum pernah dirating).
     */
    public function rate(User $user, HelpdeskTicket $ticket): bool
    {
        if ($ticket->reporter_id !== $user->id) {
            return false;
        }

        if (! $ticket->isClosed()) {
            return false;
        }

        return ! $ticket->rating()->exists();
    }
}
