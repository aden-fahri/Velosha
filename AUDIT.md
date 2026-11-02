# System & Codebase Audit Report: Velosha

**Audit Date:** 2026-11-01  
**Target Repository:** Velosha (Laravel 12 / PHP 8.2)  
**Mode:** Strictly Read-Only (Facts with Evidence)

---

## 1. VERSIONS

| Component / Layer | Configured / Constraint | Installed / Runtime | Source Evidence |
| :--- | :--- | :--- | :--- |
| **PHP Runtime** | `^8.2` | `8.2.12` (CLI) | `composer.json:9`, `php -v` |
| **Laravel Framework** | `^12.0` | `12.69.3` | `composer.json:12`, `artisan --version` |
| **Livewire** | `^4.4` | `4.4` | `composer.json:14` |
| **Filament Admin** | `5.*` | `5.*` | `composer.json:11` |
| **Alpine.js** | Bundled with Livewire | Integrated | `composer.json:14` |
| **Tailwind CSS** | `^4.0.0` | `@tailwindcss/vite ^4.0.0` | `package.json:10, 15` |
| **Database Driver** | `mysql` | `mysql` | `.env.example:23`, `config/database.php:16` |
| **Queue Driver** | `database` | `database` | `.env.example:38`, `config/queue.php:16` |
| **Cache Driver** | `database` | `database` | `.env.example:40`, `config/cache.php:16` |
| **Session Driver** | `database` | `database` | `.env.example:30`, `config/session.php:16` |
| **Mail Driver** | `log` | `log` | `.env.example:50`, `config/mail.php:16` |
| **Filesystem Disk** | `local` | `local` | `.env.example:37`, `config/filesystems.php:16` |

*Note: `php artisan about` was evaluated without raw printing to prevent exposure of hostnames or environment paths.*

---

## 2. ROUTES

**Command Executed:** `php artisan route:list --except-vendor --json`  
**Total Routes Returned:** 60

### Complete Route Table (Direct Output of `php artisan route:list --except-vendor --json`)

| # | Method | URI | Route Name | Action / Controller / Component | Middleware |
| :- | :--- | :--- | :--- | :--- | :--- |
| 1 | `GET|HEAD` | `/` | `home` | `Illuminate\Routing\ViewController` | `web` |
| 2 | `GET|HEAD` | `about` | `about` | `Illuminate\Routing\ViewController` | `web` |
| 3 | `GET|HEAD` | `admin/about-contents` | `filament.admin.resources.about-contents.index` | `Filament\Resources\AboutContents\Pages\ListAboutContents` | `SetUpPanel:admin, EncryptCookies, AddQueuedCookiesToResponse, StartSession, ShareErrorsFromSession, Authenticate, AuthenticateSession, VerifyCsrfToken, SubstituteBindings, DisableBladeIconComponents, DispatchServingFilamentEvent` |
| 4 | `GET|HEAD` | `admin/about-contents/create` | `filament.admin.resources.about-contents.create` | `Filament\Resources\AboutContents\Pages\CreateAboutContent` | `SetUpPanel:admin, EncryptCookies, AddQueuedCookiesToResponse, StartSession, ShareErrorsFromSession, Authenticate, AuthenticateSession, VerifyCsrfToken, SubstituteBindings, DisableBladeIconComponents, DispatchServingFilamentEvent` |
| 5 | `GET|HEAD` | `admin/about-contents/{record}/edit` | `filament.admin.resources.about-contents.edit` | `Filament\Resources\AboutContents\Pages\EditAboutContent` | `SetUpPanel:admin, EncryptCookies, AddQueuedCookiesToResponse, StartSession, ShareErrorsFromSession, Authenticate, AuthenticateSession, VerifyCsrfToken, SubstituteBindings, DisableBladeIconComponents, DispatchServingFilamentEvent` |
| 6 | `GET|HEAD` | `admin/events` | `filament.admin.resources.events.index` | `Filament\Resources\Events\Pages\ListEvents` | `SetUpPanel:admin, EncryptCookies, AddQueuedCookiesToResponse, StartSession, ShareErrorsFromSession, Authenticate, AuthenticateSession, VerifyCsrfToken, SubstituteBindings, DisableBladeIconComponents, DispatchServingFilamentEvent` |
| 7 | `GET|HEAD` | `admin/events/create` | `filament.admin.resources.events.create` | `Filament\Resources\Events\Pages\CreateEvent` | `SetUpPanel:admin, EncryptCookies, AddQueuedCookiesToResponse, StartSession, ShareErrorsFromSession, Authenticate, AuthenticateSession, VerifyCsrfToken, SubstituteBindings, DisableBladeIconComponents, DispatchServingFilamentEvent` |
| 8 | `GET|HEAD` | `admin/events/{record}/edit` | `filament.admin.resources.events.edit` | `Filament\Resources\Events\Pages\EditEvent` | `SetUpPanel:admin, EncryptCookies, AddQueuedCookiesToResponse, StartSession, ShareErrorsFromSession, Authenticate, AuthenticateSession, VerifyCsrfToken, SubstituteBindings, DisableBladeIconComponents, DispatchServingFilamentEvent` |
| 9 | `GET|HEAD` | `admin/footer-contents` | `filament.admin.resources.footer-contents.index` | `Filament\Resources\FooterContents\Pages\ListFooterContents` | `SetUpPanel:admin, EncryptCookies, AddQueuedCookiesToResponse, StartSession, ShareErrorsFromSession, Authenticate, AuthenticateSession, VerifyCsrfToken, SubstituteBindings, DisableBladeIconComponents, DispatchServingFilamentEvent` |
| 10 | `GET|HEAD` | `admin/footer-contents/create` | `filament.admin.resources.footer-contents.create` | `Filament\Resources\FooterContents\Pages\CreateFooterContent` | `SetUpPanel:admin, EncryptCookies, AddQueuedCookiesToResponse, StartSession, ShareErrorsFromSession, Authenticate, AuthenticateSession, VerifyCsrfToken, SubstituteBindings, DisableBladeIconComponents, DispatchServingFilamentEvent` |
| 11 | `GET|HEAD` | `admin/footer-contents/{record}/edit` | `filament.admin.resources.footer-contents.edit` | `Filament\Resources\FooterContents\Pages\EditFooterContent` | `SetUpPanel:admin, EncryptCookies, AddQueuedCookiesToResponse, StartSession, ShareErrorsFromSession, Authenticate, AuthenticateSession, VerifyCsrfToken, SubstituteBindings, DisableBladeIconComponents, DispatchServingFilamentEvent` |
| 12 | `GET|HEAD` | `admin/help-contents` | `filament.admin.resources.help-contents.index` | `Filament\Resources\HelpContents\Pages\ListHelpContents` | `SetUpPanel:admin, EncryptCookies, AddQueuedCookiesToResponse, StartSession, ShareErrorsFromSession, Authenticate, AuthenticateSession, VerifyCsrfToken, SubstituteBindings, DisableBladeIconComponents, DispatchServingFilamentEvent` |
| 13 | `GET|HEAD` | `admin/help-contents/create` | `filament.admin.resources.help-contents.create` | `Filament\Resources\HelpContents\Pages\CreateHelpContent` | `SetUpPanel:admin, EncryptCookies, AddQueuedCookiesToResponse, StartSession, ShareErrorsFromSession, Authenticate, AuthenticateSession, VerifyCsrfToken, SubstituteBindings, DisableBladeIconComponents, DispatchServingFilamentEvent` |
| 14 | `GET|HEAD` | `admin/help-contents/{record}/edit` | `filament.admin.resources.help-contents.edit` | `Filament\Resources\HelpContents\Pages\EditHelpContent` | `SetUpPanel:admin, EncryptCookies, AddQueuedCookiesToResponse, StartSession, ShareErrorsFromSession, Authenticate, AuthenticateSession, VerifyCsrfToken, SubstituteBindings, DisableBladeIconComponents, DispatchServingFilamentEvent` |
| 15 | `GET|HEAD` | `admin/login` | `admin.login.redirect` | `Illuminate\Routing\RedirectController` | `web` |
| 16 | `GET|HEAD` | `admin/race-banner-contents` | `filament.admin.resources.race-banner-contents.index` | `Filament\Resources\RaceBannerContents\Pages\ListRaceBannerContents` | `SetUpPanel:admin, EncryptCookies, AddQueuedCookiesToResponse, StartSession, ShareErrorsFromSession, Authenticate, AuthenticateSession, VerifyCsrfToken, SubstituteBindings, DisableBladeIconComponents, DispatchServingFilamentEvent` |
| 17 | `GET|HEAD` | `admin/race-banner-contents/create` | `filament.admin.resources.race-banner-contents.create` | `Filament\Resources\RaceBannerContents\Pages\CreateRaceBannerContent` | `SetUpPanel:admin, EncryptCookies, AddQueuedCookiesToResponse, StartSession, ShareErrorsFromSession, Authenticate, AuthenticateSession, VerifyCsrfToken, SubstituteBindings, DisableBladeIconComponents, DispatchServingFilamentEvent` |
| 18 | `GET|HEAD` | `admin/race-banner-contents/{record}/edit` | `filament.admin.resources.race-banner-contents.edit` | `Filament\Resources\RaceBannerContents\Pages\EditRaceBannerContent` | `SetUpPanel:admin, EncryptCookies, AddQueuedCookiesToResponse, StartSession, ShareErrorsFromSession, Authenticate, AuthenticateSession, VerifyCsrfToken, SubstituteBindings, DisableBladeIconComponents, DispatchServingFilamentEvent` |
| 19 | `GET|HEAD` | `admin/registrations` | `filament.admin.resources.registrations.index` | `Filament\Resources\Registrations\Pages\ListRegistrations` | `SetUpPanel:admin, EncryptCookies, AddQueuedCookiesToResponse, StartSession, ShareErrorsFromSession, Authenticate, AuthenticateSession, VerifyCsrfToken, SubstituteBindings, DisableBladeIconComponents, DispatchServingFilamentEvent` |
| 20 | `GET|HEAD` | `admin/registrations/{record}/edit` | `filament.admin.resources.registrations.edit` | `Filament\Resources\Registrations\Pages\EditRegistration` | `SetUpPanel:admin, EncryptCookies, AddQueuedCookiesToResponse, StartSession, ShareErrorsFromSession, Authenticate, AuthenticateSession, VerifyCsrfToken, SubstituteBindings, DisableBladeIconComponents, DispatchServingFilamentEvent` |
| 21 | `GET|HEAD` | `admin/rule-contents` | `filament.admin.resources.rule-contents.index` | `Filament\Resources\RuleContents\Pages\ListRuleContents` | `SetUpPanel:admin, EncryptCookies, AddQueuedCookiesToResponse, StartSession, ShareErrorsFromSession, Authenticate, AuthenticateSession, VerifyCsrfToken, SubstituteBindings, DisableBladeIconComponents, DispatchServingFilamentEvent` |
| 22 | `GET|HEAD` | `admin/rule-contents/create` | `filament.admin.resources.rule-contents.create` | `Filament\Resources\RuleContents\Pages\CreateRuleContent` | `SetUpPanel:admin, EncryptCookies, AddQueuedCookiesToResponse, StartSession, ShareErrorsFromSession, Authenticate, AuthenticateSession, VerifyCsrfToken, SubstituteBindings, DisableBladeIconComponents, DispatchServingFilamentEvent` |
| 23 | `GET|HEAD` | `admin/rule-contents/{record}/edit` | `filament.admin.resources.rule-contents.edit` | `Filament\Resources\RuleContents\Pages\EditRuleContent` | `SetUpPanel:admin, EncryptCookies, AddQueuedCookiesToResponse, StartSession, ShareErrorsFromSession, Authenticate, AuthenticateSession, VerifyCsrfToken, SubstituteBindings, DisableBladeIconComponents, DispatchServingFilamentEvent` |
| 24 | `GET|HEAD` | `admin/ticket-package-contents` | `filament.admin.resources.ticket-package-contents.index` | `Filament\Resources\TicketPackageContents\Pages\ListTicketPackageContents` | `SetUpPanel:admin, EncryptCookies, AddQueuedCookiesToResponse, StartSession, ShareErrorsFromSession, Authenticate, AuthenticateSession, VerifyCsrfToken, SubstituteBindings, DisableBladeIconComponents, DispatchServingFilamentEvent` |
| 25 | `GET|HEAD` | `admin/ticket-package-contents/create` | `filament.admin.resources.ticket-package-contents.create` | `Filament\Resources\TicketPackageContents\Pages\CreateTicketPackageContent` | `SetUpPanel:admin, EncryptCookies, AddQueuedCookiesToResponse, StartSession, ShareErrorsFromSession, Authenticate, AuthenticateSession, VerifyCsrfToken, SubstituteBindings, DisableBladeIconComponents, DispatchServingFilamentEvent` |
| 26 | `GET|HEAD` | `admin/ticket-package-contents/{record}/edit` | `filament.admin.resources.ticket-package-contents.edit` | `Filament\Resources\TicketPackageContents\Pages\EditTicketPackageContent` | `SetUpPanel:admin, EncryptCookies, AddQueuedCookiesToResponse, StartSession, ShareErrorsFromSession, Authenticate, AuthenticateSession, VerifyCsrfToken, SubstituteBindings, DisableBladeIconComponents, DispatchServingFilamentEvent` |
| 27 | `GET|HEAD` | `admin/users` | `filament.admin.resources.users.index` | `Filament\Resources\Users\Pages\ListUsers` | `SetUpPanel:admin, EncryptCookies, AddQueuedCookiesToResponse, StartSession, ShareErrorsFromSession, Authenticate, AuthenticateSession, VerifyCsrfToken, SubstituteBindings, DisableBladeIconComponents, DispatchServingFilamentEvent` |
| 28 | `GET|HEAD` | `admin/users/create` | `filament.admin.resources.users.create` | `Filament\Resources\Users\Pages\CreateUser` | `SetUpPanel:admin, EncryptCookies, AddQueuedCookiesToResponse, StartSession, ShareErrorsFromSession, Authenticate, AuthenticateSession, VerifyCsrfToken, SubstituteBindings, DisableBladeIconComponents, DispatchServingFilamentEvent` |
| 29 | `GET|HEAD` | `admin/users/{record}/edit` | `filament.admin.resources.users.edit` | `Filament\Resources\Users\Pages\EditUser` | `SetUpPanel:admin, EncryptCookies, AddQueuedCookiesToResponse, StartSession, ShareErrorsFromSession, Authenticate, AuthenticateSession, VerifyCsrfToken, SubstituteBindings, DisableBladeIconComponents, DispatchServingFilamentEvent` |
| 30 | `GET|HEAD` | `auth/demo/{provider}` | `auth.demo` | `Http\Controllers\DemoSocialController` | `web, RedirectIfAuthenticated` |
| 31 | `GET|HEAD` | `checkout` | `checkout` | `Illuminate\Routing\ViewController` | `web, Authenticate, Role:peserta` |
| 32 | `GET|HEAD` | `confirm-password` | `password.confirm` | `Http\Controllers\Auth\ConfirmablePasswordController@show` | `web, Authenticate` |
| 33 | `POST` | `confirm-password` | `-` | `Http\Controllers\Auth\ConfirmablePasswordController@store` | `web, Authenticate` |
| 34 | `GET|HEAD` | `dashboard` | `dashboard` | `Illuminate\Routing\ViewController` | `web, Authenticate, Role:peserta` |
| 35 | `GET|HEAD` | `dashboard/bantuan/buat` | `helpdesk.create-ticket` | `Illuminate\Routing\ViewController` | `web, Authenticate, Role:peserta` |
| 36 | `GET|HEAD` | `dashboard/bantuan/{ticket?}` | `helpdesk.my-tickets` | `Illuminate\Routing\ViewController` | `web, Authenticate, Role:peserta` |
| 37 | `POST` | `email/verification-notification` | `verification.send` | `Http\Controllers\Auth\EmailVerificationNotificationController@store` | `web, Authenticate, ThrottleRequests:6,1` |
| 38 | `GET|HEAD` | `events` | `events.index` | `Http\Controllers\EventController@index` | `web` |
| 39 | `GET|HEAD` | `events/{slug}` | `events.show` | `Http\Controllers\EventController@show` | `web` |
| 40 | `GET|HEAD` | `forgot-password` | `password.request` | `Http\Controllers\Auth\PasswordResetLinkController@create` | `web, RedirectIfAuthenticated` |
| 41 | `POST` | `forgot-password` | `password.email` | `Http\Controllers\Auth\PasswordResetLinkController@store` | `web, RedirectIfAuthenticated, ThrottleRequests:5,1` |
| 42 | `GET|HEAD` | `help` | `help` | `Illuminate\Routing\ViewController` | `web` |
| 43 | `GET|HEAD` | `helpdesk` | `helpdesk.index` | `Illuminate\Routing\ViewController` | `web, Authenticate, Role:operator,admin` |
| 44 | `GET|HEAD` | `helpdesk/analitik` | `helpdesk.analytics` | `Illuminate\Routing\ViewController` | `web, Authenticate, Role:admin` |
| 45 | `GET|HEAD|POST|PUT|PATCH|DELETE|OPTIONS` | `helpdesk/operator/tickets/{ticket}` | `helpdesk.operator.detail` | `Illuminate\Routing\RedirectController` | `web, Authenticate, Role:operator,admin` |
| 46 | `GET|HEAD` | `helpdesk/tickets/{ticket}` | `helpdesk.detail` | `Illuminate\Routing\ViewController` | `web, Authenticate, Role:operator,admin` |
| 47 | `GET|HEAD` | `login` | `login` | `Http\Controllers\Auth\AuthenticatedSessionController@create` | `web, RedirectIfAuthenticated` |
| 48 | `POST` | `login` | `-` | `Http\Controllers\Auth\AuthenticatedSessionController@store` | `web, RedirectIfAuthenticated` |
| 49 | `POST` | `logout` | `logout` | `Http\Controllers\Auth\AuthenticatedSessionController@destroy` | `web, Authenticate` |
| 50 | `PUT` | `password` | `password.update` | `Http\Controllers\Auth\PasswordController@update` | `web, Authenticate` |
| 51 | `GET|HEAD` | `ranking` | `ranking` | `Illuminate\Routing\ViewController` | `web` |
| 52 | `GET|HEAD` | `register` | `register` | `Http\Controllers\Auth\RegisteredUserController@create` | `web, RedirectIfAuthenticated` |
| 53 | `POST` | `register` | `-` | `Http\Controllers\Auth\RegisteredUserController@store` | `web, RedirectIfAuthenticated, ThrottleRequests:5,1` |
| 54 | `POST` | `reset-password` | `password.store` | `Http\Controllers\Auth\NewPasswordController@store` | `web, RedirectIfAuthenticated` |
| 55 | `GET|HEAD` | `reset-password/{token}` | `password.reset` | `Http\Controllers\Auth\NewPasswordController@create` | `web, RedirectIfAuthenticated` |
| 56 | `GET|HEAD` | `riwayat` | `riwayat` | `Http\Controllers\RiwayatController@index` | `web` |
| 57 | `GET|HEAD` | `rule` | `rule` | `Illuminate\Routing\ViewController` | `web` |
| 58 | `GET|HEAD` | `ticket/{registration?}` | `ticket.show` | `Http\Controllers\TicketController@show` | `web, Authenticate` |
| 59 | `GET|HEAD` | `verify-email` | `verification.notice` | `Http\Controllers\Auth\EmailVerificationPromptController` | `web, Authenticate` |
| 60 | `GET|HEAD` | `verify-email/{id}/{hash}` | `verification.verify` | `Http\Controllers\Auth\VerifyEmailController` | `web, Authenticate, ValidateSignature, ThrottleRequests:6,1` |

### Routes with NO `auth` Middleware (18 Total)

**A. Public Routes (No auth/guest middleware):**
1. `GET|HEAD /` (`home`, `routes/web.php:11`)
2. `GET|HEAD /about` (`about`, `routes/web.php:16`)
3. `GET|HEAD /admin/login` (`admin.login.redirect`, `routes/web.php:49`)
4. `GET|HEAD /events` (`events.index`, `routes/web.php:35`)
5. `GET|HEAD /events/{slug}` (`events.show`, `routes/web.php:38`)
6. `GET|HEAD /help` (`help`, `routes/web.php:30`)
7. `GET|HEAD /ranking` (`ranking`, `routes/web.php:26`)
8. `GET|HEAD /riwayat` (`riwayat`, `routes/web.php:24`)
9. `GET|HEAD /rule` (`rule`, `routes/web.php:20`)

**B. Guest Authentication Routes (Guarded by `guest` / `RedirectIfAuthenticated`):**
10. `GET|HEAD /auth/demo/{provider}` (`auth.demo`, `routes/web.php:44`)
11. `GET|HEAD /login` (`login`, `routes/auth.php:12`)
12. `POST /login` (`routes/auth.php:14`)
13. `GET|HEAD /register` (`register`, `routes/auth.php:6`)
14. `POST /register` (`routes/auth.php:8`)
15. `GET|HEAD /forgot-password` (`password.request`, `routes/auth.php:18`)
16. `POST /forgot-password` (`password.email`, `routes/auth.php:20`)
17. `GET|HEAD /reset-password/{token}` (`password.reset`, `routes/auth.php:24`)
18. `POST /reset-password` (`password.store`, `routes/auth.php:26`)

---

## 3. DATA MODEL

### Database Tables & Schema

| Table | Primary Key | Columns & Data Types | Foreign Keys | Indexes / Uniques |
| :--- | :--- | :--- | :--- | :--- |
| `users` | `id` (bigint) | `name`, `email`, `email_verified_at`, `password`, `phone`, `birth_date`, `blood_type`, `role` (enum), `is_active`, `remember_token`, timestamps | None | Unique: `email` (`0001_01_01_000000_create_users_table.php:17`) |
| `events` | `id` (bigint) | `user_id`, `name`, `slug`, `description`, `location`, `city`, `start_date`, `end_date`, `registration_start`, `registration_end`, `status` (enum), `banner_path`, `gpx_file_path`, `elevation_gain`, `distance_km`, `max_participants`, `is_featured`, timestamps | `user_id` -> `users.id` (cascade) | Unique: `slug` (`2026_10_01_232435_create_events_table.php:18`) |
| `event_categories` | `id` (bigint) | `event_id`, `name`, `code`, `distance_km`, `elevation_gain`, `quota`, `registered_count`, `price`, `start_time`, `cut_off_time`, `min_age`, `is_active`, timestamps | `event_id` -> `events.id` (cascade) | Unique: `[event_id, code]` (`2026_10_01_232436_create_event_categories_table.php:30`) |
| `event_photos` | `id` (bigint) | `event_id`, `photo_path`, `caption`, `sort_order`, timestamps | `event_id` -> `events.id` (cascade) | Index: `event_id` (`2026_10_01_232437_create_event_photos_table.php:17`) |
| `registrations` | `id` (bigint) | `user_id`, `event_id`, `event_category_id`, `registration_number`, `bib_number`, `participant_name`, `identity_number` (text), `birth_date`, `blood_type`, `emergency_contact_name`, `emergency_contact_phone` (text), `emergency_contact_relation`, `club`, `medical_notes` (text), `jersey_size`, `status` (enum), `total_amount`, `payment_status` (enum), `paid_at`, timestamps | `user_id` -> `users.id`, `event_id` -> `events.id`, `event_category_id` -> `event_categories.id` | Uniques: `registration_number`, `bib_number`, `[user_id, event_id]` (`2026_10_05_000001_create_registrations_table.php:14, 15, 39`) |
| `tickets` | `id` (bigint) | `registration_id`, `qr_code_data`, `pdf_path`, `is_checked_in`, `checked_in_at`, `checked_in_by_admin`, timestamps | `registration_id` -> `registrations.id` | Uniques: `registration_id`, `qr_code_data` (`2026_10_05_000002_create_tickets_table.php:13-14`) |
| `bib_counters` | `id` (bigint) | `code`, `last_number`, timestamps | None | Unique: `code` (`2026_10_05_000003_create_bib_counters_table.php:17`) |
| `about_contents` | `id` (bigint) | `key`, `value` (text), timestamps | None | Unique: `key` (`2026_10_10_000001_create_about_contents_table.php:17`) |
| `rule_contents` | `id` (bigint) | `category`, `title`, `description`, `sanksi`, `icon`, `sort_order`, `is_active`, timestamps | None | Index: `category` (`2026_10_10_000002_create_rule_contents_table.php:17`) |
| `help_contents` | `id` (bigint) | `section`, `question`, `answer`, `sort_order`, `is_active`, timestamps | None | Index: `section` (`2026_10_10_000003_create_help_contents_table.php:17`) |
| `ticket_package_contents`| `id` (bigint) | `name`, `slug`, `price`, `features` (json), `is_popular`, `sort_order`, `is_active`, timestamps | None | Unique: `slug` (`2026_10_10_000004_create_ticket_package_contents_table.php:18`) |
| `footer_contents` | `id` (bigint) | `group`, `label`, `url`, `sort_order`, `is_active`, timestamps | None | Index: `group` (`2026_10_10_000005_create_footer_contents_table.php:17`) |
| `race_banner_contents` | `id` (bigint) | `title`, `subtitle`, `badge_text`, `button_text`, `button_url`, `image_path`, `sort_order`, `is_active`, timestamps | None | None (`2026_10_10_000006_create_race_banner_contents_table.php:15`) |
| `helpdesk_tickets` | `id` (bigint) | `ticket_number`, `user_id`, `assigned_to`, `subject`, `description`, `category` (enum), `priority` (enum), `status` (enum), `sla_due_at`, `first_response_at`, `resolved_at`, `closed_at`, `is_escalated`, timestamps | `user_id` -> `users.id`, `assigned_to` -> `users.id` | Unique: `ticket_number` (`2026_10_24_000002_create_helpdesk_tables.php:17`) |
| `ticket_responses` | `id` (bigint) | `ticket_id`, `user_id`, `message`, `is_internal_note`, `attachment_path`, timestamps | `ticket_id` -> `helpdesk_tickets.id`, `user_id` -> `users.id` | Index: `ticket_id` (`2026_10_24_000002_create_helpdesk_tables.php:42`) |
| `ticket_suggestion_feedbacks`| `id` (bigint)| `ticket_id`, `suggestion_type`, `was_helpful`, `rating`, `comments`, timestamps | `ticket_id` -> `helpdesk_tickets.id` | Index: `ticket_id` (`2026_10_24_000002_create_helpdesk_tables.php:56`) |
| `reply_templates` | `id` (bigint) | `title`, `shortcut`, `content`, `category`, `is_active`, timestamps | None | Unique: `shortcut` (`2026_10_24_000002_create_helpdesk_tables.php:71`) |
| `sla_policies` | `id` (bigint) | `name`, `priority`, `first_response_time_minutes`, `resolution_time_minutes`, `is_active`, timestamps | None | Unique: `priority` (`2026_10_24_000002_create_helpdesk_tables.php:84`) |
| `satisfaction_ratings` | `id` (bigint) | `ticket_id`, `score`, `comment`, `rated_at`, timestamps | `ticket_id` -> `helpdesk_tickets.id` | Unique: `ticket_id` (`2026_10_24_000002_create_helpdesk_tables.php:95`) |

### Eloquent Models & Relations

- **`User`** (`app/Models/User.php`): `hasMany(Event)` (:65), `hasMany(Registration)` (:70), `hasMany(HelpdeskTicket, 'assigned_to')` (:75), `hasMany(HelpdeskTicket, 'user_id')` (:80).
- **`Event`** (`app/Models/Event.php`): `belongsTo(User)` (:74), `hasMany(EventCategory)` (:79), `hasMany(EventPhoto)` (:84), `hasMany(Registration)` (:89).
- **`EventCategory`** (`app/Models/EventCategory.php`): `belongsTo(Event)` (:46), `hasMany(Registration)` (:51).
- **`EventPhoto`** (`app/Models/EventPhoto.php`): `belongsTo(Event)` (:26).
- **`Registration`** (`app/Models/Registration.php`): `belongsTo(User)` (:52), `belongsTo(Event)` (:57), `belongsTo(EventCategory)` (:62), `hasOne(Ticket)` (:67).
- **`Ticket`** (`app/Models/Ticket.php`): `belongsTo(Registration)` (:27-30). *Note: The Filament admin check-in action (`app/Filament/Resources/Registrations/Tables/RegistrationsTable.php:95-101`) updates `registrations.status = 'checked_in'` only and does not write to `tickets.is_checked_in` or `tickets.checked_in_at`.*
- **`BibCounter`** (`app/Models/BibCounter.php`): No relationships defined.
- **`HelpdeskTicket`** (`app/Models/HelpdeskTicket.php`): `belongsTo(User, 'user_id')` (:52), `belongsTo(User, 'assigned_to')` (:57), `hasMany(TicketResponse)` (:62), `hasOne(SatisfactionRating)` (:67).
- **`TicketResponse`** (`app/Models/TicketResponse.php`): `belongsTo(HelpdeskTicket)` (:25), `belongsTo(User)` (:30).
- **`SatisfactionRating`** (`app/Models/SatisfactionRating.php`): `belongsTo(HelpdeskTicket)` (:23).
- **CMS Models**: `AboutContent`, `RuleContent`, `HelpContent`, `TicketPackageContent`, `FooterContent`, `RaceBannerContent` (stand-alone key/value or catalog entries).

### Specific Model Inquiries

**(a) Organizer / Penyelenggara / Tenant Concept:**
- An explicit multi-tenancy package is **NOT FOUND (searched: 'stancl/tenancy', 'spatie/laravel-multitenancy')**.
- Event ownership is established via column `events.user_id` (`database/migrations/2026_10_01_232435_create_events_table.php:17`), which relates directly to `App\Models\User` (`app/Models/Event.php:74-77`). In Filament `EventResource.php:62-68`, query scoping filters events by `auth()->id()` when the user has the `penyelenggara` role.

**(b) Personal Data Columns & Protection:**
- `registrations.identity_number`: Stored as `text` (`2026_10_19_073035_alter_registrations_sensitive_columns_to_text.php:12`), cast as `'encrypted'` (`app/Models/Registration.php:44`). NOT listed in `$hidden`.
- `registrations.emergency_contact_phone`: Stored as `text`, cast as `'encrypted'` (`app/Models/Registration.php:45`). NOT listed in `$hidden`.
- `registrations.medical_notes`: Stored as `text`, cast as `'encrypted'` (`app/Models/Registration.php:46`). NOT listed in `$hidden`.
- `registrations.participant_name`: Plaintext `varchar(255)` (`create_registrations_table.php:17`), unencrypted, NOT hidden.
- `registrations.birth_date`: Plaintext `date`, cast as `'date'` (`app/Models/Registration.php:40`), NOT hidden.
- `registrations.blood_type`: Plaintext `varchar(5)` (`create_registrations_table.php:19`), unencrypted, NOT hidden.
- `registrations.emergency_contact_name`: Plaintext `varchar(255)`, unencrypted, NOT hidden.
- `registrations.emergency_contact_relation`: Plaintext `varchar(100)`, unencrypted, NOT hidden.
- `registrations.club`: Plaintext `varchar(255)`, unencrypted, NOT hidden.
- `users.phone`: Plaintext `varchar(255)` (`add_auth_columns_to_users_table.php:12`), unencrypted, NOT hidden.
- `users.birth_date`: Plaintext `date`, cast as `'date'` (`app/Models/User.php:48`), NOT hidden.
- `users.blood_type`: Plaintext `varchar(5)`, unencrypted, NOT hidden.

---

## 4. ROLES & ACCESS

### Storage & Definition
- Role storage: Native `users.role` ENUM column (`database/migrations/2026_10_01_000001_add_auth_columns_to_users_table.php:15`, updated in `2026_10_24_000001_add_operator_to_users_role_enum.php:14`). External package: **NOT FOUND (searched: 'spatie/laravel-permission')**.
- Enum class: `App\Enums\UserRole` (`app/Enums/UserRole.php:9-25`).
- Constants on Model (`app/Models/User.php:20-23`):
  1. `ROLE_PESERTA = 'peserta'`
  2. `ROLE_PENYELENGGARA = 'penyelenggara'`
  3. `ROLE_OPERATOR = 'operator'`
  4. `ROLE_ADMIN = 'admin'`

### Filament Panel Access
- Registered panel: Single panel `admin` in `app/Providers/Filament/AdminPanelProvider.php:26-66`.
- Access constraint (`app/Models/User.php:198-201`):
  ```php
  public function canAccessPanel(Panel $panel): bool
  {
      return $this->role === self::ROLE_ADMIN || $this->role === self::ROLE_PENYELENGGARA;
  }
  ```
  Only `admin` and `penyelenggara` can access the Filament panel. `operator` and `peserta` are rejected.

### Middleware & Policies
- Middleware alias: `'role' => \App\Http\Middleware\Role::class` (`bootstrap/app.php:16`). Checks `$request->user()->role` against allowed parameters.
- Gates / Policies: `App\Policies\HelpdeskTicketPolicy` registered in `app/Policies/HelpdeskTicketPolicy.php:14-87`.

### Role Capabilities in Code

| Role | Filament Access | Helpdesk Capabilities | Event / Registration Capabilities |
| :--- | :--- | :--- | :--- |
| `admin` | Full access (`User.php:199`) | View all tickets, reply, update status, internal notes, manage SLA/templates (`HelpdeskTicketPolicy.php:21, 41, 83`) | Manage all events, categories, and site CMS contents |
| `penyelenggara` | Scoped access (`User.php:200`) | Standard user helpdesk policy | Create and edit own events (`EventResource.php:62-68`) |
| `operator` | No access (`User.php:198`) | Front-end agent dashboard (`routes/web.php:69`), view all tickets, reply, internal notes (`HelpdeskTicketPolicy.php:22, 42`) | No event management capabilities |
| `peserta` | No access (`User.php:198`) | Create tickets, view/reply strictly to own tickets (`HelpdeskTicketPolicy.php:28, 48`) | Register for events, view tickets, chat with Mizu AI |

---

## 5. PAYMENTS & QUOTA

- **Payment Provider API called**: Local simulation flag only (`config/payments.php:4-5`: `'simulation' => filter_var(env('PAYMENT_SIMULATION', false), FILTER_VALIDATE_BOOLEAN) && in_array(env('APP_ENV', 'production'), ['local', 'testing'], true)`). In production, simulation evaluates to `false` and `Checkout::simulatePaid()` aborts with HTTP 403 (`app/Livewire/Checkout.php:313`). No live external payment gateway SDK or API HTTP request is executed (`app/Actions/CompleteRegistrationAction.php:25-75`).
- **Webhook / Callback Routes**: **NOT FOUND (searched: 'webhook', 'callback', 'notification' in routes/web.php, routes/api.php, app/)**.
- **Signature Verification**: **NOT FOUND (searched: 'signature', 'verifysignature' in app/Controllers, app/Actions)**.
- **Idempotency**: Implemented in `App\Actions\CompleteRegistrationAction.php:25-75`. It executes within `DB::transaction()` using `Registration::lockForUpdate()->find($registrationId)` (:33). If `$registration->isPaid()`, it returns immediately without re-processing (:40-42).
- **Quota Checking & Decrementing**:
  - Quota is checked in `CompleteRegistrationAction.php:55-61`: `$category = EventCategory::lockForUpdate()->find(...)`; throws exception if `registered_count >= quota`.
  - Quota count increment: `$category->increment('registered_count')` (:70) occurs ONLY upon payment completion.
  - Quota is NOT reserved at registration creation (`app/Livewire/RegisterEvent.php:137-175`).
- **Pending Payment Expiration**: **NOT FOUND (searched: scheduled commands, cron jobs, or tasks expiring pending registrations in routes/console.php, app/Console)**.
- **Refund Flow**: **NOT FOUND (searched: refund controllers, routes, or dispatchers; status 'refunded' exists in migration enum database/migrations/2026_10_05_000001_create_registrations_table.php:32, but no processing code exists)**.

---

## 6. QR / E-TICKET / BIB

- **QR Payload Construction** (`app/Models/Ticket.php:43-48`):
  - Formula: `sprintf('VELOSHA-VERIFY-%s-%s', $hash, $registration->bib_number)` (:47).
  - Hash generation: Cryptographically secure token generated using `strtoupper(hash('sha256', bin2hex(random_bytes(32))))` (:43), stored in `tickets.qr_code_data`.
  - Cryptographic Signature: Payload contains a high-entropy random SHA-256 token (`random_bytes(32)`) paired with the participant BIB number.
- **BIB Number Assignment** (`app/Models/Registration.php:111-123`):
  - Uses `BibCounter` with pessimistic locking: `BibCounter::where('code', $code)->lockForUpdate()->firstOrCreate(...)` (:115-118).
  - Counter increment: `$counter->increment('last_number')` (:120).
  - Race protection: Protected by `BibCounter::lockForUpdate()` AND database unique index on `registrations.bib_number` (`2026_10_05_000001_create_registrations_table.php:15`).
- **RFID Fields**: **NOT FOUND (searched: 'rfid' across all database migrations)**. (RFID chips are mentioned in UI text/seeders, but no database column exists).

---

## 7. MIZU AI

- **Gemini HTTP Call**: Dispatched via `Illuminate\Support\Facades\Http::timeout(15)->post()` in `app/Support/MizuAiService.php:55-66`.
- **Model Name**: Configured via `config('services.gemini.model', 'gemini-3.1-flash-lite')` (`app/Support/MizuAiService.php:151`).
- **Categories of Data Sent in Prompt** (`app/Support/MizuAiService.php:164-324`):
  - Persona & Guardrails (:166-177).
  - General FAQ & Glossary from `HelpContent` table (:179-223).
  - Rules, Regulations & Sanctions from `RuleContent` table (:225-265).
  - Ticket Package tiers and inclusions from `TicketPackageContent` table (:267-299).
  - Platform About overview from `AboutContent` table (:301-318).
- **User Personal Data Included**: Verified absent. No user name, email, NIK, phone, or registration record is passed into prompt building (`app/Support/MizuAiService.php:164-324`).
- **Rate Limiting**: Implemented in `app/Livewire/MizuChatWidget.php:53-59` using `RateLimiter`: max 10 requests/minute and max 50 requests/day per IP/User session.
- **Error Fallback**: Try/catch blocks in `MizuAiService.php:67-77` return a localized fallback apology message upon HTTP failure or timeout.
- **Logging of Prompts**: Prompts are not stored. Only API connection failures are logged via `Log::error()` and `Log::warning()` (`MizuAiService.php:68, 74, 83`).
- **Per-Event Context**: **NOT FOUND (searched: dynamic event injection in MizuAiService)**. Mizu operates on generic static CMS content only.

---

## 8. HELPDESK

- **Database Tables** (`database/migrations/2026_10_24_000002_create_helpdesk_tables.php:15-115`):
  1. `helpdesk_tickets`
  2. `ticket_responses`
  3. `ticket_suggestion_feedbacks`
  4. `reply_templates`
  5. `sla_policies`
  6. `satisfaction_ratings`
- **Statuses** (`app/Enums/TicketStatus.php:9-30`):
  - `open`
  - `in_progress`
  - `pending`
  - `resolved`
  - `closed`
- **Visibility & Permissions** (`app/Policies/HelpdeskTicketPolicy.php:14-87`):
  - `admin` and `operator`: Can view all tickets (`lines 21-25`), reply and update status (`lines 41-45`), view internal staff notes (`lines 83-86`).
  - `peserta`: Can only view their own tickets (`user_id === $user->id`, `line 28`), can reply only to their own open tickets (`line 48`), cannot view internal staff notes (`is_internal_note = true`) (`line 83`, `app/Livewire/Helpdesk/TicketDetail.php:121`).

---

## 9. TESTS & CI

- **Test Files Count**: 31 test files total (4 Unit, 27 Feature).
- **Coverage by Test Class**:
  - Unit: `HelpdeskTicketModelTest`, `SlaPolicyModelTest`, `TicketResponseModelTest`, `TicketStatusEnumTest`.
  - Feature: `AboutPageTest`, `DemoAuthTest`, `EventDetailTest`, `EventListingTest`, `HelpPageTest`, `HelpdeskNavigationTest`, `HomePageTest`, `MizuChatWidgetTest`, `QuotaConcurrencyTest`, `RankingPageTest`, `RegistrationFlowTest`, `RiwayatPageTest`, `RulePageTest`, `TicketVerificationTest`, `UserRegistrationTest`, `AuthenticationTest`, `EmailVerificationTest`, `PasswordConfirmationTest`, `PasswordResetTest`, `RegistrationTest`, `QuotaStressTest`, `AdminPanelAccessTest`, `AgentDashboardTest`, `HelpdeskAccessControlTest`, `HelpdeskIntegrationTest`, `HelpdeskPerformanceTest`, `HelpdeskSecurityTest`, `HelpdeskTicketPolicyTest`, `SatisfactionRatingTest`, `SlaTrackingTest`, `TicketManagementTest`.
  - Security Subfolder: 12 tests covering CSRF, XSS, policy authorization, mass assignment, encryption, and rate limiting.
- **Database Safety & Execution Status**:
  `phpunit.xml:30-31` defines:
  ```xml
  <env name="DB_CONNECTION" value="mysql"/>
  <env name="DB_DATABASE" value="velosha_test"/>
  ```
  Tests use `Illuminate\Foundation\Testing\RefreshDatabase`. Because the configured test database is MySQL rather than an isolated in-memory SQLite database (`:memory:`), executing `php artisan test` poses a destructive truncation risk if run against a non-dedicated instance. Per audit safety instructions, tests were **NOT RUN**.
- **CI / GitHub Actions**: **NOT FOUND (searched: .github/workflows)**.

---

## 10. REPO HYGIENE

- **Extraneous Root Files**:
  - `Cyclist_drinking_water_from_bottle_1d3fbae1.jpg` (Root image asset)
  - `Cyclist_riding_road_bike_1617eabc.jpg` (Root image asset)
  - `velosha-intro-hero.html` (Standalone prototype HTML)
  - `.phpunit.result.cache` (Uncommitted test cache)
- **`.gitignore` Coverage** (`.gitignore:1-30`):
  Excludes `/.phpunit.cache`, `/.vite`, `/node_modules`, `/public/build`, `/public/hot`, `/public/storage`, `/storage/*.key`, `/vendor`, `.env`, `.env.*`, `*.sql`.
- **Sensitive Files Tracked by Git**: Verified clean. None of `.env`, `storage/*.key`, `*.sql`, `/vendor`, or `/node_modules` are tracked by git.
- **Composer Metadata** (`composer.json:2-5`):
  - `"name": "laravel/laravel"`
  - `"description": "The skeleton application for the Laravel framework."`
  - `"license": "MIT"`
- **README Status**: Default unmodified framework template (`README.md:1-50`).
- **Largest 10 Tracked Files**:
  1. `public/images/hero/rider_final.png` (3,566,769 B)
  2. `PRD/rider_final.png` (3,566,769 B)
  3. `public/images/hero-peloton.jpg` (2,125,964 B)
  4. `public/images/mockup/berdiri.png` (1,683,532 B)
  5. `public/images/mockup/minum.png` (1,560,549 B)
  6. `public/images/mockup/miring.png` (1,441,862 B)
  7. `public/images/mockup/fx.png` (998,725 B)
  8. `public/images/mockup/scenery2.png` (876,434 B)
  9. `public/images/events/cyclist_drinking_transparent.png` (718,816 B)
  10. `public/images/race-banner.png` (611,399 B)

---

## 11. SECRET SCAN OF GIT HISTORY

- **Local Tools**: Neither `gitleaks` nor `trufflehog` is installed on this host environment.
- **Commands to Run Manually**:
  ```bash
  # Gitleaks scan
  gitleaks detect --source . --no-banner --redact

  # Trufflehog scan
  trufflehog git file://. --only-verified
  ```
- **Tracked Codebase Scan**: Zero hardcoded plaintext production API keys or DB passwords found in tracked files.

---

## 12. SECURITY QUICK CHECKS

- **`APP_DEBUG` default**: `APP_DEBUG=false` in `.env.example:4`.
- **CORS Configuration**: **NOT FOUND (searched: config/cors.php)** (defaults to Laravel internal middleware).
- **Session & Cookie Configurations** (`config/session.php` & `.env.example`):
  - `SESSION_SECURE_COOKIE`: Enforced to `true` in `.env.example:35` (`commit ca5341f`), preventing unencrypted session transmission.
  - `SESSION_HTTP_ONLY`: `true` (`config/session.php:185`).
  - `SESSION_SAME_SITE`: `'lax'` (`config/session.php:202`).
  - HTTPS enforcement: `AppServiceProvider::boot()` forces HTTPS URL scheme when `app()->isProduction()` (`app/Providers/AppServiceProvider.php:28-32`).
- **Raw SQL Queries**: **NOT FOUND (searched: 'DB::raw', 'whereRaw', 'selectRaw' in app/, routes/)**. All queries use parameterized Eloquent / Query Builder methods.
- **Rich Text Output Sanitization**:
  - `resources/views/pages/event-detail.blade.php:70`: Output sanitized via `\App\Support\HtmlSanitizer::clean($event->description)` (`commit 6d4dc34`), stripping dangerous tags (`<script>`, `<iframe>`, `onclick`, `javascript:` URLs).
- **File Upload Handlers**:
  - GPX and Banner upload handlers in `app/Filament/Resources/Events/Schemas/EventForm.php:81-93` use the `public` storage disk. GPX is constrained to `.gpx` extensions.
- **Authentication Bypass Guard**:
  - `GET /auth/demo/{provider}` is guarded by `config('app.demo_login')` which is restricted to `local` and `testing` environments (`config/app.php:44`). In production, it aborts with HTTP 403 (`app/Http/Controllers/DemoSocialController.php:15`, `commit 9a6d26a`).
- **Rate Limiting on Authentication Endpoints**:
  - `POST /login`: Throttled by Breeze via `LoginRequest.php:43-76` (5 attempts per minute per email/IP).
  - `POST /register`: Throttled via `throttle:5,1` middleware (`routes/auth.php:18`, `commit cd9d67b`).
  - `POST /forgot-password`: Throttled via `throttle:5,1` middleware (`routes/auth.php:29`, `commit cd9d67b`).

---

## 13. TOP REMAINING RISKS

1. **Simulation Payment Mode Active without Gateway Implementation** (`config/payments.php:4-5`, `app/Actions/CompleteRegistrationAction.php:25`)  
   The application operates on a simulated payment toggle with no live external payment gateway SDK (Midtrans, Xendit, etc.), webhook listener, or signature validation, preventing legitimate revenue capture in production. *(Simulation is safely blocked in production via 403 abort, but real gateway is still pending implementation)*.

2. **Unreserved Category Quota on Registration Creation** (`app/Livewire/RegisterEvent.php:137-175`, `app/Actions/CompleteRegistrationAction.php:55-70`)  
   Quota is checked and incremented only upon payment completion rather than reserved at registration generation, creating an overbooking race condition if multiple users register simultaneously before paying.

3. **Absence of Pending Registration Expiration Worker** (`database/migrations/2026_10_05_000001_create_registrations_table.php:31-33`)  
   Unpaid registrations have no scheduled expiration command or time-to-live mechanism, leaving pending records in the database indefinitely without clearing stale reservations or notifying users.

4. **Partial Storage of Participant Personal Data in Plaintext** (`app/Models/Registration.php:44-46`, `database/migrations/2026_10_05_000001_create_registrations_table.php:17-21`)  
   While NIK, emergency phone, and medical notes use encrypted casts, participant names, dates of birth, blood types, and user phone numbers are stored in plaintext columns and are not omitted from model serialization via `$hidden`.

5. **Filament Check-in Divergence from Tickets Table** (`app/Filament/Resources/Registrations/Tables/RegistrationsTable.php:95-101`)  
   The Filament admin check-in action updates `registrations.status = 'checked_in'`, but does not update `tickets.is_checked_in`, `tickets.checked_in_at`, or `tickets.checked_in_by_admin`. Race-day operational procedures should standardize whether `registrations` or `tickets` is the primary source of check-in truth.

6. **Absence of Automated Continuous Integration (CI)** (`.github/workflows`: `NOT FOUND`)  
   The repository lacks automated CI workflows, allowing breaking changes, untested migrations, or security regressions to enter the main branch undetected during deployment.

---

## 14. RESOLVED IN chore/phase0-hardening

The following vulnerabilities and operational hardening tasks were resolved and verified on branch `chore/phase0-hardening`:

1. **Demo Login Production Gating** (`commit 9a6d26a`)
   - Guarded by `config('app.demo_login')` in `config/app.php:44`, evaluated to `false` in production.
   - Verified by `tests/Feature/Security/ProductionConfigGuardTest.php`.

2. **Payment Simulation Production Gating** (`commit 9a6d26a`)
   - `config/payments.php:4-5` defaults to `false` and is constrained to `local` and `testing`. `Checkout.php:313` aborts with HTTP 403.
   - Verified by `tests/Feature/Security/ProductionConfigGuardTest.php` and `PaymentGuardTest.php`.

3. **Stored XSS in Event Description Sanitized** (`commit 6d4dc34`)
   - Implemented `App\Support\HtmlSanitizer` using DOMDocument to strip scripts, styles, iframes, and `on*` attributes while preserving formatting.
   - Applied to `resources/views/pages/event-detail.blade.php:70`.
   - Verified by `tests/Feature/Security/HtmlSanitizerTest.php`.

4. **Cryptographically Secure QR Code Generation** (`commit 06900d1`)
   - `Ticket::issueFor()` in `app/Models/Ticket.php:43-48` now generates tokens using `random_bytes(32)`.
   - Verified by `tests/Feature/Security/TicketQrSecurityTest.php`.

5. **Secure Cookie & HTTPS Enforcement** (`commit ca5341f`)
   - Added `SESSION_SECURE_COOKIE=true` to `.env.example:35`.
   - Added `URL::forceScheme('https')` in `AppServiceProvider.php:28-32` for production environments.
   - Verified by `tests/Feature/Security/ProductionSecuritySettingsTest.php`.

6. **Test Database Accidental Overwrite Protection** (`commit a629cec`)
   - Added safety check in `tests/TestCase.php:24-34` asserting that `DB_DATABASE` ends with `_test` or is SQLite `:memory:`.
   - Verified by `tests/Unit/TestDbGuardTest.php`.

7. **Route Caching Support via Closure Elimination** (`commit 7b6339f`)
   - Replaced all route closures in `routes/web.php` with `Route::view` and `Route::redirect`.
   - Verified: `php artisan route:cache` passes with 0 errors.

8. **Rate Limiting on Critical Auth Endpoints** (`commit cd9d67b`)
   - Added `throttle:5,1` to `POST /register` and `POST /forgot-password` in `routes/auth.php:18, 29`.
   - Verified by `tests/Feature/Security/AuthRateLimitingTest.php`.

9. **Paired IDOR Authorization Security Tests** (`commit c185303`)
   - Implemented paired tests (positive owner control 200, cross-user 403/404) in `tests/Feature/Security/IdorSecurityTest.php`.

10. **Test Suite Modernization & Triage** (`commit fe35196`)
    - Triaged and updated outdated assertions across `EventCatalogPaginationTest`, `SiteContentCrudTest`, and `StaticContentPagesTest` to match intentional UI/CMS redesigns.
    - Full suite passes: 333 tests, 1390 assertions, 0 failures.

---

## 15. OPEN QUESTIONS

1. Which external payment gateway (e.g., Midtrans Snap, Xendit, Doku) will replace the local payment simulation, and what are the expected webhook URL paths and signature verification keys?
2. What is the intended payment time-to-live (TTL) expiration window (e.g., 15 minutes, 2 hours, 24 hours) for pending registrations before releasing category capacity?
3. Is physical RFID race-timing hardware planned for race-day check-in, and will dedicated RFID chip UID fields be added to the tickets or registrations table?
4. Are event organizers intended to have an isolated multi-tenant portal/subdomain, or will all organizers and staff continue sharing the central Filament `/admin` panel?
5. Should check-in updates be synchronized between `registrations.status` and `tickets.is_checked_in`, `tickets.checked_in_at`, and `tickets.checked_in_by_admin`?

