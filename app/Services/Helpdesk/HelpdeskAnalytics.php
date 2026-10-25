<?php

namespace App\Services\Helpdesk;

use App\Enums\TicketStatus;
use App\Models\HelpdeskTicket;
use App\Models\SatisfactionRating;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * @file HelpdeskAnalytics.php
 * @brief Layanan kalkulasi analitik dan metrik performa modul Velosha Helpdesk.
 *
 * @details
 * Kelas ini menyediakan agregasi metrik tingkat tinggi untuk kebutuhan dasbor administrator:
 * - Metrik KPI kuantitas tiket (Total, Open, In-Progress, Closed).
 * - Rata-rata waktu respons awal per operator via SQL TIMESTAMPDIFF(MINUTE, created_at, first_response_at).
 * - Distribusi skor kepuasan 1-5 bintang dan rata-rata rating (keseluruhan & per operator).
 * - Pemeringkatan operator terbaik (Top Operators) berdasarkan tiket selesai, respons, dan rating.
 *
 * Catatan Arsitektur & Aturan Domain:
 * 1. "Per operator" dihitung berdasarkan kolom `helpdesk_tickets.operator_id`.
 * 2. Waktu respons dihitung dari `created_at` hingga `first_response_at` (hanya respons manusia;
 *    balasan otomatis bot tidak mengubah first_response_at).
 * 3. Rating kepuasan diatribusikan ke operator melalui relasi tiket (`helpdesk_tickets.operator_id`).
 * 4. Pengguna dengan status akun sistem (`users.is_system = true`) mutlak dieksklusikan dari seluruh agregasi.
 * 5. Seluruh kalkulasi menggunakan pure SQL aggregates tanpa memuat seluruh baris model ke memori PHP.
 */
class HelpdeskAnalytics
{
    /**
     * @brief Mengambil metrik KPI ringkasan volume tiket.
     *
     * @return array{total: int, open: int, in_progress: int, closed: int}
     */
    public function getKpiMetrics(): array
    {
        $row = DB::table(HelpdeskTicket::TABLE_NAME)
            ->selectRaw("
                COUNT(*) as total,
                COUNT(CASE WHEN status = ? THEN 1 END) as open_count,
                COUNT(CASE WHEN status = ? THEN 1 END) as in_progress_count,
                COUNT(CASE WHEN status = ? THEN 1 END) as closed_count
            ", [
                TicketStatus::OPEN->value,
                TicketStatus::IN_PROGRESS->value,
                TicketStatus::CLOSED->value,
            ])
            ->first();

        return [
            'total' => (int) ($row->total ?? 0),
            'open' => (int) ($row->open_count ?? 0),
            'in_progress' => (int) ($row->in_progress_count ?? 0),
            'closed' => (int) ($row->closed_count ?? 0),
        ];
    }

    /**
     * @brief Menghitung rata-rata waktu respons awal per operator.
     *
     * @details
     * Atribut "per operator" diagregasikan berdasarkan `helpdesk_tickets.operator_id`.
     * Waktu respons awal diukur dari `created_at` tiket menuju `first_response_at` (hanya
     * balasan staf/operator manusia).
     * Cakupan operator mencakup seluruh user dengan `role = 'operator'` (selalu disertakan),
     * ditambah user dengan `role = 'admin'` yang pernah menangani tiket (`operator_id` minimal 1 tiket).
     * Akun sistem (`users.is_system = true`) mutlak dieksklusikan.
     * Jika operator belum memiliki data respons, nilai numerik bernilai null dan
     * `formatted_time` menghasilkan "-".
     *
     * @return \Illuminate\Support\Collection<int, object>
     */
    public function getAverageResponseTimesPerOperator(): Collection
    {
        $query = DB::table('users')
            ->leftJoin(HelpdeskTicket::TABLE_NAME, function ($join) {
                $join->on(HelpdeskTicket::TABLE_NAME . '.operator_id', '=', 'users.id');
            });

        $this->applyOperatorScope($query);

        $results = $query
            ->selectRaw("
                users.id as operator_id,
                users.name as operator_name,
                COUNT(" . HelpdeskTicket::TABLE_NAME . ".id) as total_tickets,
                COUNT(" . HelpdeskTicket::TABLE_NAME . ".first_response_at) as response_count,
                AVG(CASE 
                    WHEN " . HelpdeskTicket::TABLE_NAME . ".first_response_at IS NOT NULL 
                    THEN TIMESTAMPDIFF(MINUTE, " . HelpdeskTicket::TABLE_NAME . ".created_at, " . HelpdeskTicket::TABLE_NAME . ".first_response_at)
                END) as avg_response_minutes
            ")
            ->groupBy('users.id', 'users.name')
            ->orderBy('users.name', 'asc')
            ->get();

        return $results->map(function ($row) {
            $rawMinutes = $row->avg_response_minutes !== null ? (float) $row->avg_response_minutes : null;
            $row->avg_response_minutes = $rawMinutes;
            $row->formatted_response_time = $this->formatResponseTime($rawMinutes);
            return $row;
        });
    }

    /**
     * @brief Menghitung skor kepuasan rata-rata dan distribusi bintang 1 sampai 5.
     *
     * @details
     * Menghasilkan dua set data:
     * 1. `overall`: Ringkasan rating kepuasan di seluruh sistem.
     * 2. `by_operator`: Ringkasan kepuasan dikelompokkan berdasarkan `helpdesk_tickets.operator_id`.
     * Atribusi kepuasan mengaitkan `satisfaction_ratings.ticket_id` dengan `helpdesk_tickets.operator_id`.
     * Akun sistem (`users.is_system = true`) mutlak dieksklusikan.
     *
     * @return array{
     *     overall: array{total: int, average: float|null, formatted_average: string, distribution: array<int, int>},
     *     by_operator: \Illuminate\Support\Collection<int, object>
     * }
     */
    public function getSatisfactionRatings(): array
    {
        // 1. Agregasi Keseluruhan (Overall)
        $overallRow = DB::table('satisfaction_ratings')
            ->join(HelpdeskTicket::TABLE_NAME, 'satisfaction_ratings.ticket_id', '=', HelpdeskTicket::TABLE_NAME . '.id')
            ->leftJoin('users', HelpdeskTicket::TABLE_NAME . '.operator_id', '=', 'users.id')
            ->where(function ($q) {
                $q->whereNull('users.is_system')->orWhere('users.is_system', false);
            })
            ->selectRaw("
                COUNT(satisfaction_ratings.id) as total_ratings,
                AVG(satisfaction_ratings.score) as avg_score,
                COUNT(CASE WHEN satisfaction_ratings.score = 1 THEN 1 END) as star_1,
                COUNT(CASE WHEN satisfaction_ratings.score = 2 THEN 1 END) as star_2,
                COUNT(CASE WHEN satisfaction_ratings.score = 3 THEN 1 END) as star_3,
                COUNT(CASE WHEN satisfaction_ratings.score = 4 THEN 1 END) as star_4,
                COUNT(CASE WHEN satisfaction_ratings.score = 5 THEN 1 END) as star_5
            ")
            ->first();

        $overallTotal = (int) ($overallRow->total_ratings ?? 0);
        $overallAvg = $overallRow->avg_score !== null ? round((float) $overallRow->avg_score, 1) : null;

        $overallData = [
            'total' => $overallTotal,
            'average' => $overallAvg,
            'formatted_average' => $overallAvg !== null ? number_format($overallAvg, 1) : '-',
            'distribution' => [
                1 => (int) ($overallRow->star_1 ?? 0),
                2 => (int) ($overallRow->star_2 ?? 0),
                3 => (int) ($overallRow->star_3 ?? 0),
                4 => (int) ($overallRow->star_4 ?? 0),
                5 => (int) ($overallRow->star_5 ?? 0),
            ],
        ];

        // 2. Agregasi Per Operator (grouped by helpdesk_tickets.operator_id)
        $byOperatorQuery = DB::table('users')
            ->leftJoin(HelpdeskTicket::TABLE_NAME, 'helpdesk_tickets.operator_id', '=', 'users.id')
            ->leftJoin('satisfaction_ratings', 'satisfaction_ratings.ticket_id', '=', 'helpdesk_tickets.id');

        $this->applyOperatorScope($byOperatorQuery);

        $byOperator = $byOperatorQuery
            ->selectRaw("
                users.id as operator_id,
                users.name as operator_name,
                COUNT(satisfaction_ratings.id) as total_ratings,
                AVG(satisfaction_ratings.score) as avg_score,
                COUNT(CASE WHEN satisfaction_ratings.score = 1 THEN 1 END) as star_1,
                COUNT(CASE WHEN satisfaction_ratings.score = 2 THEN 1 END) as star_2,
                COUNT(CASE WHEN satisfaction_ratings.score = 3 THEN 1 END) as star_3,
                COUNT(CASE WHEN satisfaction_ratings.score = 4 THEN 1 END) as star_4,
                COUNT(CASE WHEN satisfaction_ratings.score = 5 THEN 1 END) as star_5
            ")
            ->groupBy('users.id', 'users.name')
            ->orderBy('users.name', 'asc')
            ->get()
            ->map(function ($row) {
                $avg = $row->avg_score !== null ? round((float) $row->avg_score, 1) : null;
                $row->avg_score = $avg;
                $row->formatted_average = $avg !== null ? number_format($avg, 1) : '-';
                $row->distribution = [
                    1 => (int) $row->star_1,
                    2 => (int) $row->star_2,
                    3 => (int) $row->star_3,
                    4 => (int) $row->star_4,
                    5 => (int) $row->star_5,
                ];
                return $row;
            });

        return [
            'overall' => $overallData,
            'by_operator' => $byOperator,
        ];
    }

    /**
     * @brief Mengambil daftar operator terbaik (Top Operators) berdasarkan tiket selesai,
     * rata-rata respons, dan skor kepuasan.
     *
     * @details
     * Mengelompokkan berdasarkan `helpdesk_tickets.operator_id` dan mengaitkan rating.
     * Cakupan operator mencakup pengguna `role = 'operator'` (selalu) ditambah pengguna
     * `role = 'admin'` yang menangani minimal 1 tiket. Akun sistem (`is_system = true`) dieksklusikan.
     *
     * @return \Illuminate\Support\Collection<int, object>
     */
    public function getTopOperators(): Collection
    {
        $query = DB::table('users')
            ->leftJoin(HelpdeskTicket::TABLE_NAME, 'helpdesk_tickets.operator_id', '=', 'users.id')
            ->leftJoin('satisfaction_ratings', 'satisfaction_ratings.ticket_id', '=', 'helpdesk_tickets.id');

        $this->applyOperatorScope($query);

        $results = $query
            ->selectRaw("
                users.id as operator_id,
                users.name as operator_name,
                COUNT(DISTINCT CASE WHEN " . HelpdeskTicket::TABLE_NAME . ".status = ? THEN " . HelpdeskTicket::TABLE_NAME . ".id END) as closed_count,
                AVG(CASE 
                    WHEN " . HelpdeskTicket::TABLE_NAME . ".first_response_at IS NOT NULL 
                    THEN TIMESTAMPDIFF(MINUTE, " . HelpdeskTicket::TABLE_NAME . ".created_at, " . HelpdeskTicket::TABLE_NAME . ".first_response_at)
                END) as avg_response_minutes,
                AVG(satisfaction_ratings.score) as avg_rating
            ", [TicketStatus::CLOSED->value])
            ->groupBy('users.id', 'users.name')
            ->orderByRaw("closed_count DESC, avg_response_minutes ASC, users.name ASC")
            ->get();

        return $results->map(function ($row) {
            $rawMinutes = $row->avg_response_minutes !== null ? (float) $row->avg_response_minutes : null;
            $rawRating = $row->avg_rating !== null ? round((float) $row->avg_rating, 1) : null;

            $row->closed_count = (int) $row->closed_count;
            $row->avg_response_minutes = $rawMinutes;
            $row->avg_rating = $rawRating;
            $row->formatted_response_time = $this->formatResponseTime($rawMinutes);
            $row->formatted_rating = $rawRating !== null ? number_format($rawRating, 1) : '-';

            return $row;
        });
    }

    /**
     * @brief Memformat durasi waktu respons awal sesuai spesifikasi domain.
     *
     * @details
     * Aturan pemformatan:
     * - Jika tidak ada data (`null`), menghasilkan "-".
     * - Jika durasi < 120 menit, menghasilkan nilai menit bulat e.g. "15m".
     * - Jika durasi >= 120 menit, menghasilkan nilai jam dengan 1 desimal e.g. "2.5h".
     *
     * @param float|int|null $minutes Durasi respons dalam satuan menit.
     * @return string String durasi terformat atau "-" jika tanpa data.
     */
    public function formatResponseTime(float|int|null $minutes): string
    {
        if ($minutes === null) {
            return '-';
        }

        $min = (float) $minutes;

        if ($min < 120.0) {
            return (string) round($min) . 'm';
        }

        $hours = $min / 60.0;
        return number_format($hours, 1, '.', '') . 'h';
    }

    /**
     * @brief Menerapkan cakupan staf/operator yang menangani tiket.
     *
     * @details
     * "Per operator" mencakup seluruh pengguna dengan peran operator (`users.role = 'operator'`),
     * ditambah pengguna dengan peran admin (`users.role = 'admin'`) yang memiliki minimal 1 tiket
     * yang ditangani (`operator_id = users.id`). Akun sistem (`users.is_system = true`) mutlak dieksklusikan.
     *
     * @param \Illuminate\Database\Query\Builder $query
     */
    protected function applyOperatorScope($query): void
    {
        $query->where('users.is_system', false)
            ->where(function ($q) {
                $q->where('users.role', User::ROLE_OPERATOR)
                    ->orWhere(function ($adminQuery) {
                        $adminQuery->where('users.role', User::ROLE_ADMIN)
                            ->whereExists(function ($sub) {
                                $sub->select(DB::raw(1))
                                    ->from(HelpdeskTicket::TABLE_NAME)
                                    ->whereColumn(HelpdeskTicket::TABLE_NAME . '.operator_id', 'users.id');
                            });
                    });
            });
    }
}
