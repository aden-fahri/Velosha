<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Registration;
use App\Models\Ticket;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Checkout extends Component
{
    /** Biaya layanan tetap (simulasi Midtrans Snap). */
    public const SERVICE_FEE = 4500;

    public string $eventSlug = '';

    public ?int $categoryId = null;

    public ?int $registrationId = null;

    public int $step = 1;

    public bool $showPaymentModal = false;

    /** Metode pembayaran yang dipilih di modal simulasi Midtrans. */
    public string $paymentMethod = 'qris';

    public string $error = '';

    /** True ketika sesi ini melanjutkan pendaftaran pending lama (banner + Mulai dari Awal). */
    public bool $resumed = false;

    /** category_id => registration_id lunas/check-in pada event ini (mencegah pendaftaran ganda). */
    public array $paidTicketMap = [];

    // Langkah 2: data diri & kontak darurat
    public string $participant_name = '';

    public string $identity_number = '';

    public string $birth_date = '';

    public string $gender = 'male';

    public string $blood_type = 'O';

    public string $email = '';

    public string $phone = '';

    public string $club = '';

    public string $emergency_contact_name = '';

    public string $emergency_contact_relation = 'Istri / Suami';

    public string $emergency_contact_phone = '';

    public string $medical_notes = '';

    public function mount(string $eventSlug = '', ?int $categoryId = null): void
    {
        $event = Event::query()->published()->where('slug', $eventSlug)->first();

        if (! $event instanceof Event) {
            $this->redirectRoute('events.index');

            return;
        }

        $this->eventSlug = $event->slug;
        $this->categoryId = $categoryId;

        $user = auth()->user();

        $this->participant_name = (string) $user->name;
        $this->email = (string) $user->email;
        $this->phone = (string) ($user->phone ?? '');
        $this->birth_date = $user->birth_date?->format('Y-m-d') ?? '';
        $this->gender = $user->gender ?? 'male';
        $this->blood_type = $user->blood_type ?? 'O';

        $existing = $user->registrations()
            ->whereHas('category', fn ($query) => $query->where('event_id', $event->id))
            ->latest()
            ->first();

        $this->paidTicketMap = $user->registrations()
            ->whereHas('category', fn ($query) => $query->where('event_id', $event->id))
            ->whereIn('status', [Registration::STATUS_PAID, Registration::STATUS_CHECKED_IN])
            ->get()
            ->unique('event_category_id')
            ->mapWithKeys(fn (Registration $reg) => [(int) $reg->event_category_id => (int) $reg->id])
            ->all();

        if ($existing instanceof Registration && $existing->status === Registration::STATUS_PENDING) {
            $this->categoryId = $existing->event_category_id;
            $this->registrationId = $existing->id;
            $this->step = 3;
            $this->resumed = true;
        }

        if ($this->categoryId === null) {
            $this->categoryId = $event->categories()->orderBy('price')->first()?->id;
        }
    }

    public function selectCategory(int $categoryId): void
    {
        $event = $this->event();

        if (! $event instanceof Event || ! $event->categories->contains('id', $categoryId)) {
            return;
        }

        $this->categoryId = $categoryId;
        $this->registrationId = null;
        $this->error = '';
        $this->resetValidation();
    }

    public function toStep1(): void
    {
        $this->step = 1;
        $this->error = '';
        $this->resetValidation();
    }

    /** Mulai ulang dari langkah 1 — membatalkan resume pendaftaran pending lama. */
    public function resetToStart(): void
    {
        $this->step = 1;
        $this->registrationId = null;
        $this->resumed = false;
        $this->error = '';
        $this->resetValidation();
    }

    /** Apakah kategori ini sudah pernah dibayar user pada event ini? */
    public function isCategoryPaid(int $categoryId): bool
    {
        return isset($this->paidTicketMap[$categoryId]);
    }

    public function toStep2(): void
    {
        $category = $this->category();

        if (! $category instanceof EventCategory) {
            $this->error = 'Pilih kategori jarak terlebih dahulu.';

            return;
        }

        if ($category->remaining <= 0) {
            $this->error = 'Slot kategori ini sudah penuh. Silakan pilih kategori lain.';

            return;
        }

        $paid = Registration::query()
            ->where('user_id', auth()->id())
            ->where('event_category_id', $category->id)
            ->whereIn('status', [Registration::STATUS_PAID, Registration::STATUS_CHECKED_IN])
            ->first();

        if ($paid instanceof Registration) {
            $this->error = 'Anda sudah terdaftar di kategori ini. Lihat E-Ticket Anda untuk detail lomba.';

            return;
        }

        $this->error = '';
        $this->step = 2;
    }

    public function backToStep2(): void
    {
        $this->step = 2;
        $this->showPaymentModal = false;
        $this->error = '';
        $this->resetValidation();
    }

    public function toStep3(): void
    {
        $category = $this->category();

        if (! $category instanceof EventCategory) {
            $this->error = 'Pilih kategori jarak terlebih dahulu.';

            return;
        }

        if ($category->remaining <= 0) {
            $this->error = 'Slot kategori sudah penuh saat Anda mengisi formulir. Pilih kategori lain.';

            return;
        }

        $validated = $this->validate($this->stepRules());

        $event = $this->event();
        $age = Carbon::parse($validated['birth_date'])->diffInYears(Carbon::parse($event->event_date));

        if ($age < 17) {
            $this->addError('birth_date', 'Umur minimum 17 tahun pada hari race (syarat PRD).');

            return;
        }

        $user = auth()->user();
        $user->fill([
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ]);
        $user->save();

        $attributes = Arr::except($validated, ['email', 'phone']);
        $attributes['club'] = $attributes['club'] !== '' ? $attributes['club'] : null;
        $attributes['medical_notes'] = $attributes['medical_notes'] !== '' ? $attributes['medical_notes'] : null;

        $registration = Registration::query()
            ->where('user_id', $user->id)
            ->where('event_category_id', $category->id)
            ->first();

        if ($registration instanceof Registration && $registration->isPaid()) {
            $this->error = 'Anda sudah terdaftar di kategori ini. Lihat E-Ticket Anda untuk detail lomba.';

            return;
        }

        if ($registration instanceof Registration) {
            $registration->fill($attributes);
            $registration->status = Registration::STATUS_PENDING;
            $registration->save();
        } else {
            try {
                $registration = Registration::create($attributes + [
                    'user_id' => $user->id,
                    'event_category_id' => $category->id,
                    'status' => Registration::STATUS_PENDING,
                ]);
            } catch (QueryException) {
                $this->error = 'Pendaftaran gagal disimpan. Muat ulang halaman lalu coba lagi.';

                return;
            }
        }

        $this->registrationId = $registration->id;
        $this->error = '';
        $this->step = 3;
    }

    public function openPayment(): void
    {
        if ($this->registrationId === null) {
            $this->error = 'Lengkapi langkah data diri sebelum melakukan pembayaran.';

            return;
        }

        $this->error = '';
        $this->showPaymentModal = true;
    }

    public function closePayment(): void
    {
        $this->showPaymentModal = false;
    }

    public function selectPaymentMethod(string $method): void
    {
        if (in_array($method, ['qris', 'bca_va', 'mandiri', 'bri_va', 'bni_va'], true)) {
            $this->paymentMethod = $method;
        }
    }

    /**
     * Simulasi pembayaran (Midtrans asli menyusul pada Fase 4).
     * Kunci baris kategori, cek kuota, terbitkan nomor BIB + tiket QR.
     */
    public function simulatePaid(): void
    {
        $this->showPaymentModal = false;
        $this->error = '';

        $registration = $this->registration();

        if (! $registration instanceof Registration) {
            $registration = Registration::query()
                ->where('user_id', auth()->id())
                ->where('event_category_id', $this->categoryId)
                ->first();
        }

        if (! $registration instanceof Registration) {
            $this->error = 'Data pendaftaran tidak ditemukan. Ulangi langkah data diri.';

            return;
        }

        try {
            $action = app(\App\Actions\CompleteRegistrationAction::class);
            $registration = $action->execute($registration);
        } catch (\RuntimeException $exception) {
            $this->error = $exception->getMessage();

            return;
        }

        $this->redirect('/ticket/'.$registration->id);
    }

    public function event(): ?Event
    {
        return Event::query()
            ->published()
            ->with('categories')
            ->where('slug', $this->eventSlug)
            ->first();
    }

    public function category(): ?EventCategory
    {
        if ($this->categoryId === null) {
            return null;
        }

        return $this->event()?->categories->firstWhere('id', $this->categoryId);
    }

    public function registration(): ?Registration
    {
        if ($this->registrationId === null) {
            return null;
        }

        return Registration::query()->with('category')->find($this->registrationId);
    }

    public function total(): int
    {
        return (int) ($this->category()?->price ?? 0) + self::SERVICE_FEE;
    }

    protected function stepRules(): array
    {
        return [
            'participant_name' => ['required', 'string', 'max:150'],
            'identity_number' => ['required', 'string', 'max:50'],
            'birth_date' => ['required', 'date'],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'blood_type' => ['required', Rule::in(['A', 'B', 'AB', 'O'])],
            'email' => ['required', 'email', 'max:191', Rule::unique('users', 'email')->ignore(auth()->id())],
            'phone' => ['required', 'string', 'max:30'],
            'club' => ['nullable', 'string', 'max:150'],
            'emergency_contact_name' => ['required', 'string', 'max:150'],
            'emergency_contact_relation' => ['required', 'string', 'max:50'],
            'emergency_contact_phone' => ['required', 'string', 'max:30'],
            'medical_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function render()
    {
        return view('livewire.checkout', [
            'event' => $this->event(),
            'category' => $this->category(),
            'registration' => $this->registration(),
        ]);
    }
}
