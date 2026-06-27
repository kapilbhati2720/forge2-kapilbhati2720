<@U0BBL7EQZEK> **Sprint 6 — Final Polish. Branch, build, report, PR.**

**Sprint goal:** Build SLA policies, dashboard metrics, and finalize documentation.

---
**BRANCH** (start fresh from main):
```
git checkout main && git pull origin main
git checkout -b feature/sprint-6-final-polish
```
- Commit style: `feat(scope): description`, one logical commit per issue.
- Validate EVERY new/modified PHP file with `php -l` before committing (no test suite yet — this is the gate).
- Post structured progress to <#C0BBUPKSJAF> after each issue.
- Open a PR to `main`, post the link in <#C0BBUPM0PR9>, and WAIT for human approval. Do NOT merge.

---
**ISSUE 1 — SLA Policies CRUD**
⚠️ The `sla_policies` table migration and `App\Models\SlaPolicy` model ALREADY EXIST on `main`. Do NOT re-create them. You only need the controller + routes.

1. Create `backend/app/Http/Controllers/Api/SlaPolicyController.php` with full CRUD: `index, store, show, update, destroy`. Extend `App\Http\Controllers\Controller`.
2. Register routes in `backend/routes/api.php` inside the `auth:sanctum` group:
   `Route::apiResource('sla-policies', SlaPolicyController::class);`
3. **Critical pitfalls:**
   - On store/update, explicitly set `organization_id => auth()->user()->organization_id`. The `OrganizationScope` only filters READS — it does NOT auto-fill writes.
   - The table has `unique(organization_id, priority)`. Validate to avoid a raw QueryException: use `Rule::unique('sla_policies')->where(fn($q)=>$q->where('organization_id', $orgId))`, and `->ignore($policy->id)` on update.
   - Validate: `name` required string; `priority` in `['low','medium','high','urgent']`; `response_minutes` & `resolution_minutes` required integers > 0; `is_default` boolean. Use `['sometimes', ...]` on update.
   - (Optional) If `is_default=true`, reset other rows in the same org to false.
4. Verify: `php -l` controller; `php artisan route:list --path=api/sla-policies` shows all routes.

Existing model fillable fields for reference: `organization_id, name, priority, response_minutes, resolution_minutes, is_default`.

---
**ISSUE 2 — Dashboard Metrics (net-new)**
Add a stats endpoint returning: ticket counts by status, counts by priority, SLA breach rate, average resolution time.

1. Create `backend/app/Http/Controllers/Api/DashboardController.php` with `index()`. Add route in `api.php` (sanctum group): `Route::get('/stats', [DashboardController::class, 'index']);`
2. All aggregates must be tenant-scoped — `Ticket` & `SlaPolicy` already apply `OrganizationScope`, so plain `Ticket::query()` is safe.
3. **Metric specs:**
   - Counts by status / priority: `selectRaw(status, count(*) ...)` group by enum.
   - Avg resolution time (minutes): avg over tickets where `resolved_at IS NOT NULL` → `TIMESTAMPDIFF(MINUTE, created_at, resolved_at)`.
   - SLA breach rate: for each ticket, look up its org's `SlaPolicy` matching the ticket's `priority` → use `resolution_minutes` as target. Breached if `(resolved ? resolved_at : now()) - created_at > target`. Return `breached_count`, `total_evaluated`, `breach_rate_percent`.
   - GUARD divide-by-zero when no tickets/policies exist (return 0 / null gracefully).
4. Compact JSON: `{ counts_by_status:{...}, counts_by_priority:{...}, sla:{ breached, total, breach_rate_percent }, average_resolution_minutes }`
5. Verify: `php -l` controller; hit endpoint with a valid Sanctum token after seeding sample tickets.

Note: tickets table has `status` (open|pending|on_hold|resolved|closed), `priority` (low|medium|high|urgent), `created_at`, `resolved_at`, `first_response_at`.

---
**ISSUE 3 — Polish & Docs**
1. `SUBMISSION.md` — fill `[Pending]` placeholders, check off every requirement with EXACT in-repo file paths:
   - Repo URL → `github.com/kapilbhati2720/forge2-kapilbhati2720`
   - First/Last commit SHA → fill AT THE FINAL COMMIT (`git rev-list --max-parents=0 HEAD` and `git rev-parse HEAD`).
   - Keep existing run instructions (`php artisan migrate:fresh --seed`, `php artisan test`). List evidence path under `slack-export/screenshots/`.
2. `ARCHITECTURE.md` — reconcile stale "Proposed" schema against REAL codebase:
   - Add `sla_policies` and `activity_logs` tables; add `tickets.resolved_at`, `first_response_at`, `softDeletes`, and the REAL status enum (`open, pending, on_hold, resolved, closed`).
   - REMOVE/mark the `tags` column — it does NOT exist in the real migration.
   - FIX API routes section — real endpoints have NO `/api/v1` prefix (e.g. `POST /api/tickets`, `POST /api/register`). Add new `sla-policies` resource routes and `GET /api/stats`.
3. Verify: every file path mentioned actually exists (`test -e <path>`); no `/api/v1` references remain.

---
**PR COMPLETION CHECKLIST:**
- [ ] Branch `feature/sprint-6-final-polish` off main ✓
- [ ] Issue 1: SLA CRUD + routes, `php -l` clean
- [ ] Issue 2: `/api/stats` returns all 4 metrics, divide-by-zero guarded
- [ ] Issue 3: docs reconciled with real schema/routes, SHAs filled
- [ ] Progress posted to <#C0BBUPKSJAF>; PR link in <#C0BBUPM0PR9>, awaiting approval

Please acknowledge in <#C0BBUPKSJAF> once you've branched, and flag anything in the brief that conflicts with what you see in the code. 🚀
