<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Reservation;
use Livewire\Attributes\Locked;
use Livewire\Component;

class WaitingRoom extends Component
{
    #[Locked]
    public string $eventSlug = '';

    #[Locked]
    public ?int $categoryId = null;

    public int $queuePosition = 0;

    public int $estimatedWaitMinutes = 1;

    public bool $isSoldOut = false;

    public bool $slotSecured = false;

    public function mount(string $eventSlug = '', ?int $categoryId = null): void
    {
        $event = Event::query()->published()->where('slug', $eventSlug)->first();

        if (! $event instanceof Event) {
            $this->redirectRoute('events.index');

            return;
        }

        $this->eventSlug = $event->slug;

        $category = null;
        if ($categoryId) {
            $category = $event->categories()->find($categoryId);
        }
        if (! $category) {
            $category = $event->categories()->orderBy('price')->first();
        }

        if (! $category) {
            $this->redirectRoute('events.show', ['slug' => $event->slug]);

            return;
        }

        $this->categoryId = $category->id;

        $this->processQueue();
    }

    /**
     * Dipanggil saat mount dan secara otomatis setiap polling (wire:poll.3s).
     */
    public function processQueue(): void
    {
        $category = $this->category();

        if (! $category instanceof EventCategory) {
            return;
        }

        // 1. Jika kuota resmi sudah habis terbeli
        if ($category->remaining <= 0) {
            $this->isSoldOut = true;

            return;
        }

        // 2. Lepas reservasi held yang sudah kedaluwarsa
        Reservation::where('status', Reservation::STATUS_HELD)
            ->where('expires_at', '<=', now())
            ->update(['status' => Reservation::STATUS_RELEASED]);

        $userId = auth()->id();

        // 3. Cek apakah user saat ini sudah memegang slot held aktif
        $myHeld = Reservation::query()
            ->where('user_id', $userId)
            ->where('event_category_id', $this->categoryId)
            ->activeHeld()
            ->first();

        if ($myHeld) {
            $this->slotSecured = true;
            $this->redirect(route('checkout', ['event' => $this->eventSlug, 'category' => $this->categoryId]));

            return;
        }

        // 4. Hitung sisa slot yang benar-benar bebas
        $heldCount = Reservation::query()
            ->where('event_category_id', $this->categoryId)
            ->where('user_id', '!=', $userId)
            ->activeHeld()
            ->count();

        $freeSlots = max(0, $category->remaining - $heldCount);

        // 5. Cek tiket antrean milik user ini
        $myWaiting = Reservation::query()
            ->where('user_id', $userId)
            ->where('event_category_id', $this->categoryId)
            ->where('status', Reservation::STATUS_WAITING)
            ->first();

        if ($freeSlots > 0) {
            if ($myWaiting) {
                // Berapa banyak antrean di depan saya?
                $ahead = Reservation::query()
                    ->where('event_category_id', $this->categoryId)
                    ->where('status', Reservation::STATUS_WAITING)
                    ->where('id', '<', $myWaiting->id)
                    ->count();

                if ($ahead < $freeSlots) {
                    // Giliran tiba! Promosikan tiket antrean menjadi slot held 15 menit
                    $myWaiting->update([
                        'status' => Reservation::STATUS_HELD,
                        'expires_at' => now()->addMinutes(15),
                    ]);

                    $this->slotSecured = true;
                    $this->redirect(route('checkout', ['event' => $this->eventSlug, 'category' => $this->categoryId]));

                    return;
                }

                $this->queuePosition = $ahead + 1;
            } else {
                $waitingCount = Reservation::query()
                    ->where('event_category_id', $this->categoryId)
                    ->where('status', Reservation::STATUS_WAITING)
                    ->count();

                if ($waitingCount === 0) {
                    // Tidak ada antrean, langsung ambil slot held
                    Reservation::create([
                        'user_id' => $userId,
                        'event_category_id' => $this->categoryId,
                        'status' => Reservation::STATUS_HELD,
                        'expires_at' => now()->addMinutes(15),
                    ]);

                    $this->slotSecured = true;
                    $this->redirect(route('checkout', ['event' => $this->eventSlug, 'category' => $this->categoryId]));

                    return;
                }

                // Masuk antrean baru (firstOrCreate mencegah duplikasi saat klik ganda)
                $myWaiting = Reservation::firstOrCreate([
                    'user_id' => $userId,
                    'event_category_id' => $this->categoryId,
                    'status' => Reservation::STATUS_WAITING,
                ]);

                $this->queuePosition = Reservation::query()
                    ->where('event_category_id', $this->categoryId)
                    ->where('status', Reservation::STATUS_WAITING)
                    ->where('id', '<=', $myWaiting->id)
                    ->count();
            }
        } else {
            // Semua slot sedang di-hold
            if (! $myWaiting) {
                $myWaiting = Reservation::firstOrCreate([
                    'user_id' => $userId,
                    'event_category_id' => $this->categoryId,
                    'status' => Reservation::STATUS_WAITING,
                ]);
            }

            $this->queuePosition = Reservation::query()
                ->where('event_category_id', $this->categoryId)
                ->where('status', Reservation::STATUS_WAITING)
                ->where('id', '<=', $myWaiting->id)
                ->count();
        }

        $this->estimatedWaitMinutes = max(1, (int) ceil($this->queuePosition * 2));
    }

    public function leaveQueue(): void
    {
        Reservation::query()
            ->where('user_id', auth()->id())
            ->where('event_category_id', $this->categoryId)
            ->where('status', Reservation::STATUS_WAITING)
            ->delete();

        $this->redirectRoute('events.show', ['slug' => $this->eventSlug]);
    }

    public function event(): ?Event
    {
        return Event::query()
            ->published()
            ->where('slug', $this->eventSlug)
            ->first();
    }

    public function category(): ?EventCategory
    {
        if ($this->categoryId === null) {
            return null;
        }

        return EventCategory::find($this->categoryId);
    }

    public function render()
    {
        return view('livewire.waiting-room', [
            'event' => $this->event(),
            'category' => $this->category(),
        ]);
    }
}

