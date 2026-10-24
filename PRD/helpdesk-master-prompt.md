# MASTER PROMPT — Build the Helpdesk module (Velosha)

## Role
You are a senior Laravel/Livewire engineer building the **helpdesk module** for Velosha (Laravel 12, Livewire 4, Filament 5, Tailwind 4, Blade, **MySQL 100%**). This work is separate from the ongoing visual redesign phases; do not mix them. The owner has approved the design. Implement it faithfully, in small verified steps.

## Read first (before changing anything)
1. `PRD/helpdesk.md` — the full spec for this module. **Source of truth.**
2. `PRD/desain-revisi.md` — design tokens, components, accessibility rules (reuse; do not redefine).
3. The helpdesk mockup boards (screenshots or the zipped `.dc.html` + `velosha.css` + `helpdesk.css`): Pelapor (create ticket, my tickets), Operator (queue, detail), Admin (dashboard, SLA policy). If screenshots are missing, ask; do not guess the look.
4. Existing code you must understand: `app/Models/User.php` (role constants, `isAdmin()`, `home()`, `canAccessPanel()`), the `UserRole` enum, `app/Http/Middleware/Role.php`, the participant portal (`livewire/portal-dashboard`, route `dashboard`), `layouts/app.blade.php`, existing tests (`tests/Feature/Security/*`), `registrationId` `#[Locked]` pattern, existing rate-limit usage.

## Non-negotiables
- **MySQL only.** Tests run on MySQL.
- **Do not touch** payment flow, encryption casts, `bib_counters`, existing rate limits, the `demo_login` guard, production seeders. Do not delete or weaken any existing test.
- Roles: `peserta` = reporter, **new role `operator`**, `admin` = manager. `penyelenggara` has no helpdesk access. Add `operator` via a safe MySQL migration (enum alteration with a working `down()`), update `UserRole`, `User::ROLES`, `isOperator()`, `home()`. Do not grant operators Filament panel access.
- Reporter can only ever see their own tickets (other ids → 404). Livewire ticket ids are `#[Locked]`; queries are always scoped by owner/role.
- Rating: reporter only, only when `Closed`, once per ticket (unique constraint + validation).
- **Auto-reply must never send an empty message.** `AutoReplyService::build()` trims; if the result is blank it returns `null` and no `ticket_responses` row is created.
- `first_response_at` is set only by the first **human** reply from operator/admin (`is_auto_reply = false`).
- `resolution_time` (minutes, never negative) and `closed_at` are set in exactly one place: `TicketService::changeStatus()`. Beware Carbon 3: `diffInMinutes` is signed and returns float; argument order matters. Accept an optional `?CarbonInterface $now` so it is testable and debuggable.
- Allowed transitions: Open → In-Progress → Closed, and Open → Closed. Anything else throws `DomainException`.
- Duplicate-search suggestions must not leak other reporters' data: show cleaned subject + solution summary only, and only for tickets flagged `is_reference = true`.
- No new JS/animation libraries. CSS-only motion, wrapped in `prefers-reduced-motion: no-preference`. Text on orange uses `--ink` (white on orange fails contrast). Rating stars must be keyboard-accessible (radio inputs), not clickable divs.
- Production seeders create no tickets or demo users. Local demo data only behind an environment guard.
- Never force-push or rewrite history. Never commit secrets.

## Data model
Implement exactly the schema in `PRD/helpdesk.md` §4: the three required tables (`tickets`, `ticket_responses`, `satisfaction_ratings`) plus the additions (`first_response_at`, `closed_at`, `resolution_time`, `response_due_at`, `resolution_due_at`, `is_reference`, timestamps and indexes incl. FULLTEXT on subject+description; `sla_policies`, `reply_templates`, `ticket_suggestion_feedback`). PHP enums `TicketStatus`, `TicketPriority`. Factories for every model. All times UTC, shown in Asia/Jakarta.

## Services (keep logic out of Livewire components)
- `TicketService`: create, reply, changeStatus (single source of `closed_at` / `resolution_time`), assign, rate.
- `SlaCalculator`: due dates from `sla_policies`, recalculation on priority change, compliance checks.
- `AutoReplyService`: `build()` / `send()` with the non-empty guarantee; system user for auto replies.
- `DuplicateTicketFinder`: `MATCH ... AGAINST` search, learning re-rank from `ticket_suggestion_feedback`, honors `is_reference`.
- `HelpdeskAnalytics`: aggregation queries (avg response/resolution minutes, CSAT avg + distribution, SLA compliance, volume, per-operator stats, top problem clusters). SQL aggregates with indexes, short cache; never load all tickets into memory.

## Pages (Livewire, Velosha look, see boards)
Reporter (inside participant portal, new sidebar item "Tiket bantuan"): create ticket with live similar-ticket panel and "Ini menjawab / Tetap kirim"; my tickets with thread and rating. Operator/Admin: queue with SLA countdown pills, ticket detail with reply composer + suggested replies + status/priority controls. Admin only: performance dashboard, SLA policy and reply templates. Public Help page: add the "Buat tiket bantuan" call-to-action card (login redirect). Add `wire:poll.10s` on threads. Email notification to the reporter on operator reply and on close (queued; failures must not block the reply).

## Documentation and debugging deliverables (the owner is assessed on these)
- Full Doxygen-style docblocks (`/** */`, `@param`, `@return`, `@throws`) on `TicketService`, `AutoReplyService`, `SlaCalculator`, `DuplicateTicketFinder`, and the `Ticket` model. Add a `Doxyfile` at the repo root (`INPUT` = helpdesk code, `RECURSIVE = YES`, `FILE_PATTERNS = *.php`, `EXTRACT_ALL = YES`, `GENERATE_HTML = YES`, `GENERATE_LATEX = NO`, `OUTPUT_DIRECTORY = docs/doxygen`). Do not commit generated HTML; add it to `.gitignore`.
- Make `TicketService::changeStatus()` easy to demonstrate under an Xdebug breakpoint (one clear place where `closed_at` and `resolution_time` are computed). Write a short `docs/debugging-resolution-time.md` describing how to reproduce and observe it (breakpoint location, variables to watch). Do not plant an intentional bug in the committed code.

## Tests (PHPUnit, MySQL)
Required coverage: RBAC per role and route; reporter isolation (404); rating rules (after Closed only, once, 1–5); valid/invalid transitions; `resolution_time` (created 10:00, closed 12:30 → 150, never negative, injected `$now`); `first_response_at` human-only; SLA due dates per priority and recalculation; **auto-reply never empty** (data provider `''`, `'   '`, `"\n\t"`, and a template that becomes blank after placeholder replacement → no `ticket_responses` row) plus the positive case; duplicate finder (similar found, `is_reference` honored, no leakage of reporter data, feedback changes ranking); analytics aggregates against fixture data with exact expected numbers; rate limits; public Help page shows the CTA. Run mutation checks on every new meaningful test (disable the behavior, the test must fail) and report the table with real results.

## Workflow
- Phases (one commit/PR each): 1 operator role + migration + RBAC tests; 2 schema/models/enums/factories/policies; 3 reporter create + my tickets + thread; 4 operator queue + detail + `TicketService` + SLA; 5 auto-reply + suggested replies + templates; 6 duplicate finder + feedback learning; 7 rating; 8 admin dashboard + SLA policy page; 9 notifications, accessibility, responsive, Doxygen, final tests.
- Before each phase: a 5–10 line plan. After each: full suite (plus the separate ConfigGuard/concurrency runs), Playwright screenshots at 360/390/768/1024/1440 for touched pages with a horizontal-overflow check, and a `prefers-reduced-motion: reduce` check.
- Push policy: work on a branch and open a PR. Push directly to `main` only if the full suite is green (0 failures) and ConfigGuard/concurrency are green; put the test summary in the commit message. If anything is red, do not push to `main`.
- Commit messages end with the attribution lines your harness provides.

## When to ask vs decide
Decide yourself (and note in the report): file/class names, small markup details, query tuning, stopword list. **Stop and ask** when a change touches the non-negotiables, needs a schema change beyond the spec, or the spec is contradictory or missing (open items: SLA working hours, SLA target values, whether admin may reply, `is_reference` approval flow, attachments).

## Report format (every phase)
1. What was completed (3–5 lines). 2. Files changed, grouped. 3. Real test output summary (suite, ConfigGuard, concurrency). 4. Visual evidence paths + overflow/reduced-motion results. 5. Deviations from the spec and why. 6. Risks and questions. 7. Mutation-check table with real results. Never say "done" without numbers and evidence.

## Definition of done
All six pages match the mockups at 5 widths with zero horizontal overflow; RBAC and isolation tests green; SLA and `resolution_time` correct; auto-reply never empty; duplicate finder works with privacy rules; analytics correct against fixtures; Doxygen docblocks complete and `Doxyfile` generates HTML without warnings on the documented classes; debugging doc present; full suite green; no payment/security regressions.

Start with phase 1. Before writing code, reply with: confirmation that you read every file in "Read first", the list of unclear points, and your phase-1 plan.
