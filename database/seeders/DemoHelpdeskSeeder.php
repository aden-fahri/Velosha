<?php

namespace Database\Seeders;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\HelpdeskTicket;
use App\Models\TicketResponse;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoHelpdeskSeeder extends Seeder
{
    /**
     * Seeder lokal untuk tiket demo helpdesk dengan status dan tenggat SLA deterministik.
     * Hanya dijalankan di lingkungan non-produksi.
     */
    public function run(): void
    {
        $operator = User::where('email', 'operator@velosha.id')->first();
        $peserta = User::where('email', 'peserta@velosha.id')->first();

        if (! $peserta || ! $operator) {
            return;
        }

        // 1. Tiket OPEN (Belum diambil, Mid priority)
        HelpdeskTicket::firstOrCreate(
            ['subject' => 'Tidak bisa unduh e-ticket PDF'],
            [
                'reporter_id' => $peserta->id,
                'description' => 'Saya sudah menekan tombol unduh e-ticket di dashboard tetapi file PDF gagal terunduh.',
                'priority' => TicketPriority::MID,
                'status' => TicketStatus::OPEN,
                'operator_id' => null,
                'response_due_at' => Carbon::now()->addMinutes(45),
                'resolution_due_at' => Carbon::now()->addHours(20),
            ]
        );

        // 2. Tiket IN-PROGRESS (Sedang ditangani operator)
        $inProgressTicket = HelpdeskTicket::firstOrCreate(
            ['subject' => 'E-ticket belum muncul setelah pembayaran'],
            [
                'reporter_id' => $peserta->id,
                'description' => 'Sudah bayar lunas via QRIS tadi pagi tapi e-ticket tidak muncul di Portal.',
                'priority' => TicketPriority::HIGH,
                'status' => TicketStatus::IN_PROGRESS,
                'operator_id' => $operator->id,
                'created_at' => Carbon::now()->subHours(2),
                'first_response_at' => Carbon::now()->subMinutes(15),
                'response_due_at' => Carbon::now()->addMinutes(10),
                'resolution_due_at' => Carbon::now()->addHours(3),
            ]
        );

        TicketResponse::firstOrCreate(
            [
                'ticket_id' => $inProgressTicket->id,
                'message' => 'Halo Budi, sistem verifikasi kami sedang mengecek status transaksi bank Anda.',
            ],
            [
                'responder_id' => $operator->id,
                'is_auto_reply' => false,
            ]
        );

        // 3. Tiket OVERDUE (Melewati batas SLA)
        HelpdeskTicket::firstOrCreate(
            ['subject' => 'Refund karena cedera sebelum race'],
            [
                'reporter_id' => $peserta->id,
                'description' => 'Saya mengalami cedera saat latihan dan membutuhkan bantuan proses refund atau transfer tiket.',
                'priority' => TicketPriority::HIGH,
                'status' => TicketStatus::OPEN,
                'operator_id' => null,
                'created_at' => Carbon::now()->subHours(4),
                'response_due_at' => Carbon::now()->subMinutes(30),
                'resolution_due_at' => Carbon::now()->subHours(2),
            ]
        );

        // 4. Tiket CLOSED & RUJUKAN RESMI (Reference ticket dengan solution_summary)
        $closedTicket = HelpdeskTicket::firstOrCreate(
            ['subject' => 'Nama di BIB salah ketik saat registrasi'],
            [
                'reporter_id' => $peserta->id,
                'description' => 'Nama di BIB saya kurang satu huruf.',
                'priority' => TicketPriority::LOW,
                'status' => TicketStatus::CLOSED,
                'operator_id' => $operator->id,
                'is_reference' => true,
                'solution_summary' => 'Nama peserta telah disesuaikan di database. Silakan muat ulang tab BIB di portal untuk melihat nomor BIB terbaru.',
                'created_at' => Carbon::now()->subDays(3),
                'first_response_at' => Carbon::now()->subDays(2),
                'closed_at' => Carbon::now()->subDays(1),
                'resolution_time' => 120,
                'response_due_at' => Carbon::now()->subDays(2)->addHours(4),
                'resolution_due_at' => Carbon::now()->subDays(2)->addHours(24),
            ]
        );

        TicketResponse::firstOrCreate(
            [
                'ticket_id' => $closedTicket->id,
                'message' => 'Halo, nama BIB Anda sudah kami perbaiki menjadi benar. Selamat berlomba!',
            ],
            [
                'responder_id' => $operator->id,
                'is_auto_reply' => false,
            ]
        );
    }
}
