# PulseDesk

> A multi-tenant customer support ticketing SaaS — built at **Forge 2 · Edition 1** using a live Hermes × OpenClaw agent loop.

## Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8.2+ · Laravel 11 · REST API · Laravel Sanctum |
| Database | MySQL 8 · Migrations + Seeders |
| Frontend | React 19 · Vite · Tailwind CSS v3 |
| Auth | Sanctum token auth (register / login) |
| Tests | Pest / PHPUnit feature tests |
| CI | GitHub Actions — installs, migrates, tests on every PR |

## Agent Roles

| Agent | Model (via EastRouter) | Role |
|-------|----------------------|------|
| **Hermes** | `z-ai/glm-5.1` | Orchestrator / Product Owner — sprint planning, task assignment, backlog |
| **OpenClaw** | `moonshotai/kimi-k2.7-code` | Coder — implements issues, runs tests, opens PRs, posts to #agent-log |

## Slack Channels

| Channel | Purpose |
|---------|---------|
| `#sprint-main` | Human ↔ Hermes — sprint goals, planning, decisions |
| `#agent-coder` | Hermes ↔ OpenClaw — task handoffs |
| `#agent-log` | OpenClaw structured reports (What I Did / Left / Needs Your Call) |
| `#ci-cd` | CI/test results |
| `#human-review` | Human approves release candidates before merge |

---

## Exact Run Steps (fresh clone)

### Prerequisites
- PHP 8.2+, Composer, Node 20+, MySQL 8

### Backend

```bash
cd backend
composer install
cp .env.example .env
# Edit .env: set DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD
php artisan key:generate
php artisan migrate --seed
php artisan serve
# API running at http://localhost:8000
```

### Frontend

```bash
cd frontend
npm install
cp .env.example .env
# Edit .env: set VITE_API_URL=http://localhost:8000
npm run dev
# UI running at http://localhost:5173
```

### Run Tests

```bash
cd backend
php artisan test
```

---

## Multi-Tenancy Approach

Every record is scoped by `organization_id`. The `OrganizationScope` global scope (at `app/Scopes/OrganizationScope.php`) is automatically applied to all tenant models — `Organization`, `Ticket`, `Comment`, `SlaPolicy`, `ActivityLog`. A user from Org A **cannot** read or modify Org B's data. The scope derives from the authenticated user's session, not a client-supplied header.

Use `withoutTenantScope()` as the admin escape hatch.

## What Was Built

**MUST tier (core):**
- Multi-tenant Organizations with complete data isolation
- Laravel Sanctum auth — register (creates User + Org atomically) + login (returns token)
- Full Ticket CRUD — status, priority, requester, assignee
- Threaded comments — public replies + internal notes (is_internal flag)
- OrganizationScope global scope on all models

**SHOULD tier (in progress):**
- SLA policies per priority (response + resolution targets)
- Activity log per ticket (status changes, assignments, comments)
- Dashboard metrics

## Known Limitations

- Live deployment not set up — runs cleanly on localhost from `migrate --seed`
- Real-time updates not implemented (polling approach used in frontend)
- CSAT, ticket merge, bulk actions not attempted (STRETCH tier)

## Libraries Used

- [Laravel Sanctum](https://laravel.com/docs/sanctum) — token auth
- [Tailwind CSS v3](https://tailwindcss.com) — frontend styling
- [Vite](https://vitejs.dev) + [React 19](https://react.dev) — frontend framework
