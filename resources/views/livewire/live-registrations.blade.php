<div wire:poll.15s>
    <div class="velo-rr-grid velo-rr-head">
        <span>BIB</span>
        <span>Rider &amp; Event</span>
        <span>Waktu</span>
        <span>Status</span>
        <span class="velo-rr-aksi">Aksi</span>
    </div>

    @forelse ($registrations as $registration)
    <div class="velo-rr-grid velo-rr-row">
        <span>
            @if ($registration->bib_number)
                <span class="velo-rr-bib">
                    {{ $registration->bib_number }}
                </span>
            @else
                <span class="velo-rr-bib velo-rr-bib--empty">—</span>
            @endif
        </span>

        <div class="velo-rr-main">
            <div class="velo-rr-name">
                {{ $registration->participant_name }}
            </div>
            <div class="velo-rr-sub">
                {{ $registration->category?->event?->title ?? '—' }}
                &middot;
                {{ $registration->category?->name ?? '—' }}
            </div>
        </div>

        <span class="velo-rr-time">
            {{ $registration->created_at?->format('d M · H:i') ?? '—' }}
        </span>

        <span>
            <x-filament::badge
                :color="match ($registration->status) {
                    \App\Models\Registration::STATUS_PAID => 'success',
                    \App\Models\Registration::STATUS_CHECKED_IN => 'info',
                    \App\Models\Registration::STATUS_PENDING => 'warning',
                    default => 'danger',
                }"
            >
                {{ match ($registration->status) {
                    \App\Models\Registration::STATUS_PAID => 'LUNAS',
                    \App\Models\Registration::STATUS_CHECKED_IN => 'CHECK-IN RPC',
                    \App\Models\Registration::STATUS_PENDING => 'MENUNGGU BAYAR',
                    default => 'DIBATALKAN',
                } }}
            </x-filament::badge>
        </span>

        <div class="velo-rr-aksi">
            @if ($registration->isPaid())
                <x-filament::button
                    size="xs"
                    tag="a"
                    href="{{ route('ticket.show', $registration) }}"
                    color="gray"
                >
                    E-Ticket
                </x-filament::button>
            @endif
        </div>
    </div>
    @empty
        <p class="velo-rr-empty">
            Belum ada pendaftaran masuk.
        </p>
    @endforelse
</div>
