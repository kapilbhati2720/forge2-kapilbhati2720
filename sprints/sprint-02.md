# Sprint 02 — Auth, Tenancy Scope & API Foundation

**Date:** 2026-06-27
**Goal:** Stand up Sanctum authentication, tenant-scoped Eloquent models, and the base API routing that all ticket endpoints will build on.
**Orchestrator:** Hermes (z-ai/glm-5.1 via EastRouter)
**Coder:** OpenClaw (moonshotai/kimi-k2.7-code via EastRouter)

---

## Issues

| ID | Title | Status |
|----|-------|--------|
| S02-01 | Fix migration ordering and run migrate:fresh | DONE |
| S02-02 | Scaffold Eloquent models with relationships | DONE |
| S02-03 | Create OrganizationScope global scope + apply to all models | DONE |
| S02-04 | Sanctum auth — register() + login() endpoints | IN PROGRESS |

---

## Handoffs (Hermes → OpenClaw in #agent-coder)

1. **S02-01** — Hermes assigned migration fix → OpenClaw renamed files, ran migrate:fresh cleanly (6 tables: organizations, users+org_id, tickets, comments, sla_policies, activity_logs)
2. **S02-02** — Hermes assigned model scaffolding → OpenClaw created Organization, Ticket, Comment, SlaPolicy, ActivityLog with fillable + relationships
3. **S02-03** — Hermes assigned OrganizationScope → OpenClaw created app/Scopes/OrganizationScope.php, applied to all 5 models with withoutTenantScope() escape hatch
4. **S02-04** — Hermes assigned Sanctum auth → OpenClaw building AuthController (in progress)

---

## Outcome

- All 6 database tables created and migrated cleanly on MySQL 8
- All 5 Eloquent models with proper multi-tenant relationships
- Global OrganizationScope auto-filters every query by auth user's org — Org A cannot read Org B's data
- Sanctum auth endpoints (in progress)

---

## What Slipped

- None yet — sprint is in progress

---

## Commits in this sprint

- `feat(db): scaffold multi-tenant schema with org-scoped tables`
- `feat(models): add Organization, Ticket, Comment, SlaPolicy, ActivityLog models`
- `feat(auth): add OrganizationScope global scope for multi-tenancy`
- `feat(auth): add Sanctum register and login endpoints` (pending)
