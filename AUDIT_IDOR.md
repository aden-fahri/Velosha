# Broken Object-Level Authorization (IDOR) & Access Control Audit

**Audit Date:** 2026-11-02  
**Target Repository:** Velosha (Laravel 12 / PHP 8.2)  
**Test Suite:** `tests/Feature/Security/IdorSecurityTest.php`  
**Target Test Database:** `velosha_test` (verified in `phpunit.xml:31`)  
**Execution Mode:** Fully Executed via PHPUnit (`php vendor/bin/phpunit`)

---

## 1. Test Execution Status & Environment Diagnostic

### Exact Execution Results
- **Command:** `php vendor/bin/phpunit tests/Feature/Security/IdorSecurityTest.php`
- **Exit Code:** `0`
- **Results:** `OK (12 tests, 20 assertions)`
- **Duration:** `00:22.814` (Memory: 68.00 MB)
- **Status:** **ALL 12 TESTS EXECUTED AND PASSED**

### 5-Line Diagnostic on Execution Environment & Sandbox Behavior
1. **Target Database & Host:** `phpunit.xml:30-31` defines `DB_CONNECTION=mysql` and `DB_DATABASE=velosha_test` on `127.0.0.1:3306` (database `velosha_test` verified present in MySQL).
2. **Sandbox Socket Restriction:** Inside the standard sandbox, Windows AppContainer security blocks TCP loopback socket creation with `WSAEACCES 10013` (`SQLSTATE[HY000] [2002] Access forbidden by permissions`).
3. **Sandbox File Restriction:** The sandbox process user evaluated `is_readable('vendor/autoload.php') === false`, causing PHPUnit to abort with `Cannot open bootstrap script`.
4. **Unsandboxed Execution:** Executing with `BypassSandbox: true` restored read access to `vendor/` and allowed socket connections to MySQL on `127.0.0.1:3306`.
5. **Interactive TTY Bypass:** Running direct `php vendor/bin/phpunit` bypasses Laravel Collision's interactive termwind cursor listener, completing all 12 tests deterministically in 22.8 seconds.

---

## 2. Real Targets: IDOR & Authorization Audit Table

Every target below is evaluated as a strict pair:
1. **Positive Control:** The authorized owner accesses their own resource and must receive `200 OK` / success.
2. **Cross-User Control:** Another participant or unauthorized user attempts access and must receive `403 Forbidden` or `404 Not Found`.

| Target | Test Method (Pair) | Positive Control Result | Cross-User Result | Status | Authorization Check Location |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Target A:** `GET /ticket/{registration}` | `test_target_a_positive_control_owner_can_view_own_ticket`<br>`test_target_a_cross_user_other_participant_cannot_view_foreign_ticket` | **200 OK** (BIB & name visible) | **403 Forbidden** (Guest: 302 to `/login`) | **PASS** | `app/Http/Controllers/TicketController.php:20-25` (`abort_unless($registration->user_id === $user->id ...)`) |
| **Target B:** Livewire `Checkout` Property Tampering (`registrationId`) | `test_target_b_positive_control_owner_mounts_checkout`<br>`test_target_b_cross_user_cannot_tamper_locked_registration_id` | **Successful Mount** (Step reached) | **Exception Thrown** (Tampering rejected) + **null** returned | **PASS** | `#[Locked]` on `app/Livewire/Checkout.php:28-29` and scoped query in `app/Livewire/Checkout.php:378-380` |
| **Target C:** Payment Completion (`Checkout::simulatePaid`) | `test_target_c_positive_control_owner_can_complete_payment`<br>`test_target_c_cross_user_cannot_trigger_payment_for_other_participant` | **PAID Status** (BIB generated, 302 to ticket) | **Error Banner** (B's record remains `PENDING`) | **PASS** | `app/Livewire/Checkout.php:326-339` (scoped to `auth()->id()`) & `app/Actions/CompleteRegistrationAction.php:30-75` |
| **Target D:** Filament Registration Resource (`admin/registrations/{record}/edit`) | `test_target_d_positive_control_penyelenggara_can_view_own_event_registration`<br>`test_target_d_cross_user_penyelenggara_cannot_view_foreign_event_registration` | **200 OK** (Edit form rendered) | **404 Not Found** (ModelNotFoundException) | **PASS** | `app/Filament/Resources/Registrations/RegistrationResource.php:51-55` (`whereHas('category.event', user_id)`) |
| **Extra:** Participant Helpdesk (`dashboard/bantuan/{ticket}`) | `test_helpdesk_positive_control_reporter_can_view_own_ticket`<br>`test_helpdesk_cross_user_other_participant_cannot_view_foreign_ticket` | **200 OK** (Ticket detail rendered) | **404 Not Found** (Operator route: 302 to dashboard) | **PASS** | `app/Livewire/Helpdesk/MyTickets.php:76-78` (`abort(404)` if `reporter_id !== Auth::id()`) |

---

## 3. Public Property Tampering Analysis in Livewire Components

| Component | Public Property | Protection Mechanism | Re-Authorization inside Action | File:Line |
| :--- | :--- | :--- | :--- | :--- |
| `App\Livewire\Checkout` | `public ?int $registrationId` | **`#[Locked]`** | Re-authorized in `registration()` via `where('user_id', auth()->id())->find($this->registrationId)` | `app/Livewire/Checkout.php:28-29`, `app/Livewire/Checkout.php:378-380` |
| `App\Livewire\Checkout` | `public array $paidTicketMap` | **`#[Locked]`** | Derived from `$user->registrations()` during `mount()`, immutable from client | `app/Livewire/Checkout.php:44-45`, `app/Livewire/Checkout.php:99-104` |
| `App\Livewire\Checkout` | `public string $eventSlug` | **`#[Locked]`** | Validated via `event()` published query `where('slug', $this->eventSlug)` | `app/Livewire/Checkout.php:23-24`, `app/Livewire/Checkout.php:355-360` |
| `App\Livewire\Helpdesk\MyTickets` | `public ?int $selectedTicketId` | **`#[Locked]`** | Re-authorized in `selectTicket()` and `sendReply()`: `abort_if($ticket->reporter_id !== Auth::id() && !Auth::user()->isAdmin(), 404)` | `app/Livewire/Helpdesk/MyTickets.php:27-28`, `app/Livewire/Helpdesk/MyTickets.php:76-78` |

---

## 4. Summary of PaymentGuardTest & Production Guard (10 Lines)

1. `tests/Feature/Security/PaymentGuardTest.php` asserts that `simulatePaid()` is strictly rejected when `payments.simulation` is `false`.
2. Specifically, line 60 sets `config(['payments.simulation' => false])` and line 74 invokes `Livewire::test(Checkout::class)->call('simulatePaid')`.
3. The test verifies the registration status remains `Registration::STATUS_PENDING` (line 76) and is not transitioned to `PAID`.
4. It also asserts that the simulation modal UI (`MIDTRANS SNAP`) is completely hidden from view when the flag is disabled (line 109).
5. The production environment guard is implemented directly in `config/payments.php:4-5`.
6. It evaluates: `filter_var(env('PAYMENT_SIMULATION', false), FILTER_VALIDATE_BOOLEAN) && in_array(env('APP_ENV', 'production'), ['local', 'testing'], true)`.
7. This condition guarantees that `payments.simulation` evaluates to `false` in production, even if `PAYMENT_SIMULATION=true` is set.
8. At runtime, `app/Livewire/Checkout.php:313` executes `abort_unless(config('payments.simulation'), 403)`.
9. Any attempt to invoke the payment simulation endpoint in a production environment terminates immediately with HTTP `403 Forbidden`.
10. `php vendor/bin/phpunit tests/Feature/Security/PaymentGuardTest.php` executed cleanly with `OK (2 tests, 3 assertions)` in 40.8s.
